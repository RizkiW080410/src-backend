<?php

namespace App\Http\Controllers\Admin;

use Gate;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Table;
use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Requests\MassDestroyOrderRequest;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $currentDateTime = Carbon::now();
        
        $orders = Order::with(['products', 'table'])->get();

        foreach ($orders as $order) {
            if ($currentDateTime->greaterThanOrEqualTo(Carbon::parse($order->finish_book)) && $order->status != 'Selesai') {
                $order->update(['status' => 'Selesai']);
                $table = Table::find($order->table_id);
                if ($table) {
                    $table->status = 'kosong';
                    $table->save();
                }
            }
        }

        return view('admin.orders.index', compact('orders'));
    }

    public function create()
    {
        abort_if(Gate::denies('order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = Product::all();

        $availableTables = Table::where('status', 'kosong')->get();

        return view('admin.orders.create', compact('products', 'availableTables'));
    }

    public function store(StoreOrderRequest $request)
    {
        $total = 0;
        $quantities = $request->input('quantities');

        foreach ($quantities as $product_id => $quantity) {
            $product = Product::find($product_id);
            if ($product->stock < $quantity) {
                return redirect()->back()->withErrors(['error' => 'Stock for product ' . $product->name . ' is insufficient.']);
            }
            $total += $product->price * $quantity;
        }

        $order = Order::create($request->all());

        foreach ($quantities as $product_id => $quantity) {
            $order->products()->attach($product_id, ['quantity' => $quantity]);
        }

        $order->update(['total' => $total]);

        // Update table status to 'penuh' if order status is 'proses'
        $table = Table::find($request->table_id);
        if ($table) {
            if ($order->status == 'proses') {
                $table->status = 'penuh';
                $table->save();
            }
        }

        return redirect()->route('admin.orders.index');
    }

    public function edit(Order $order)
    {
        abort_if(Gate::denies('order_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = Product::all();

        $tables = Table::all()->pluck('name', 'id');

        return view('admin.orders.edit', compact('order', 'products', 'tables'));
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $total = 0;
        $quantities = $request->input('quantities');

        foreach ($quantities as $product_id => $quantity) {
            $product = Product::find($product_id);
            if ($product->stock < $quantity) {
                return redirect()->back()->withErrors(['error' => 'Stock for product ' . $product->name . ' is insufficient.']);
            }
            $total += $product->price * $quantity;
        }
        
        $previousStatus = $order->status;

        $order->update($request->all());

        // Update table status based on order status
        $table = Table::find($order->table_id);
        if ($table) {
            if ($request->status == 'selesai' && $previousStatus != 'selesai') {
                $table->status = 'kosong';
            } elseif ($request->status == 'proses' && $previousStatus != 'proses') {
                $table->status = 'penuh';
            }
            $table->save();
        }

        $order->products()->detach();

        foreach ($quantities as $product_id => $quantity) {
            $order->products()->attach($product_id, ['quantity' => $quantity]);
        }

        $order->update(['total' => $total]);

        // Update stock if status is 'selesai'
        if (in_array($order->status, ['selesai'])) {
            $this->adjustProductStock($order);
        }

        return redirect()->route('admin.orders.index');
    }

    public function show(Order $order)
    {
        abort_if(Gate::denies('order_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $order->load(['products']);

        $order->load('table');

        return view('admin.orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        abort_if(Gate::denies('order_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $table = Table::find($order->table_id);
        if ($table && $order->status != 'Cancel' && $order->status != 'Selesai') {
            $table->status = 'kosong';
            $table->save();
        }

        $order->delete();

        return back();
    }

    public function massDestroy(MassDestroyOrderRequest $request)
    {
        $orders = Order::find(request('ids'));

        foreach ($orders as $order) {
            $table = Table::find($order->table_id);
            if ($table && $order->status != 'Selesai') {
                $table->status = 'kosong';
                $table->save();
            }
            $order->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    private function adjustProductStock(Order $order)
    {
        foreach ($order->products as $product) {
            $quantity = $product->pivot->quantity;
            $product->decrement('stock', $quantity);
        }
    }
}
