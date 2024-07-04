<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('checkout', compact('cart'));
    }

    public function process(Request $request)
    {
        // Retrieve cart from session
        $cart = session()->get('cart', []);
        $total = $request->input('total');
        $fullname = $request->input('fullname');
        $phone = $request->input('phone');
        $email = $request->input('email');

        // Create a new order
        $order = Order::create([
            'fullname' => $fullname,
            'phone' => $phone,
            'email' => $email,
            'total' => $total,
            'status' => 'pending',
        ]);

        // Attach products to the order
        foreach ($cart as $id => $details) {
            $order->products()->attach($id, ['quantity' => $details['quantity']]);
        }

        Config::$serverKey = config('midtrans.serverKey');
        Config::$isProduction = config('midtrans.isProduction');
        Config::$isSanitized = config('midtrans.isSanitized');
        Config::$is3ds = config('midtrans.is3ds');

        $params = [
            'transaction_details' => [
                'order_id' => $order->id,
                'gross_amount' => $total,
            ],
            'customer_details' => [
                'first_name' => $fullname,
                'last_name' => '', // Optional, can be blank or added
                'email' => $email,
                'phone' => $phone,
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return view('midtrans_payment', compact('snapToken', 'order'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function callback(Request $request)
    {
        $serverKey = config('midtrans.serverKey');
        $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            $order = Order::find($request->order_id);
            if ($order) {
                $transactionStatus = $request->transaction_status;
                if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                    $order->update(['status' => 'proses']);
                } elseif ($transactionStatus == 'pending') {
                    $order->update(['status' => 'pending']);
                } else {
                    $order->update(['status' => 'failed']);
                }

                return response()->json(['message' => 'Payment status updated.']);
            }
        } else {
            return response()->json(['message' => 'Invalid signature.'], 400);
        }
    }
}
