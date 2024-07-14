<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.serverKey');
        Config::$isProduction = config('midtrans.isProduction');
        Config::$isSanitized = config('midtrans.isSanitized');
        Config::$is3ds = config('midtrans.is3ds');
    }

    public function index()
    {
        $cart = session()->get('cart', []);
        $tables = Table::where('status', 'kosong')->get();
        return view('checkout', compact('cart', 'tables'));
    }

    public function process(Request $request)
    {
        $cart = session()->get('cart', []);
        $total = $request->input('total');
        $fullname = $request->input('fullname');
        $phone = $request->input('phone');
        $email = $request->input('email');
        $table_id = $request->input('table_id');
    
        // Generate a unique order ID
        $orderId = uniqid('order_');
    
        $order = Order::create([
            'order_id' => $orderId, // Save the unique order ID to the database
            'fullname' => $fullname,
            'phone' => $phone,
            'email' => $email,
            'total' => $total,
            'table_id' => $table_id,
            'status' => 'pending',
        ]);
    
        foreach ($cart as $id => $details) {
            $order->products()->attach($id, ['quantity' => $details['quantity']]);
        }
    
        $params = [
            'transaction_details' => [
                'order_id' => $orderId, // Use the unique order ID here
                'gross_amount' => $total,
            ],
            'customer_details' => [
                'first_name' => $fullname,
                'last_name' => '',
                'email' => $email,
                'phone' => $phone,
            ],
        ];
    
        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['snapToken' => $snapToken, 'order_id' => $order->id]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function callback(Request $request)
    {
        Log::info('Callback received', $request->all());

        $server_key = config('midtrans.serverKey');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $server_key);

        Log::info('Callback validation', ['hashed' => $hashed, 'signature_key' => $request->signature_key]);

        if ($hashed == $request->signature_key) {
            $transactionStatus = $request->transaction_status;
            $orderIdParts = explode('_', $request->order_id);
            $orderId = $orderIdParts[1];
            $order = Order::find($orderId);

            if ($order) {
                if (in_array($transactionStatus, ['capture', 'settlement'])) {
                    $order->update(['status' => 'proses']);
                    Log::info('Order status updated to proses', ['order_id' => $order->id]);
                } elseif ($transactionStatus == 'pending') {
                    $order->update(['status' => 'pending']);
                    Log::info('Order status updated to pending', ['order_id' => $order->id]);
                } else {
                    $order->update(['status' => 'failed']);
                    Log::info('Order status updated to failed', ['order_id' => $order->id]);
                }

                return response()->json(['message' => 'Payment status updated.']);
            } else {
                Log::warning('Order not found', ['order_id' => $request->order_id]);
                return response()->json(['message' => 'Order not found.'], 404);
            }
        } else {
            Log::warning('Callback validation failed', ['hashed' => $hashed, 'signature_key' => $request->signature_key]);
            return response()->json(['message' => 'Invalid signature.'], 400);
        }
    }

    public function success(Order $order)
    {
        return view('order_success', compact('order'));
    }
}
