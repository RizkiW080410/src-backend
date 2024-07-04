<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyMidtransSignature
{
    // public function handle(Request $request, Closure $next)
    // {
    //     $serverKey = config('midtrans.serverKey');
    //     if (!$serverKey) {
    //         return response()->json(['error' => 'Server key is not set'], 401);
    //     }

    //     $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

    //     if ($hashed == $request->signature_key) {
    //         return $next($request);
    //     }

    //     return response()->json(['error' => 'Unauthorized'], 401);
    // }
}
