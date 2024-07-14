<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Success</title>
</head>
<body>
    <div class="container">
        <h1>Order Success</h1>
        <p>Your order has been placed successfully!</p>
        <h2>Order Details:</h2>
        <ul>
            <li>Order ID: {{ $order->id }}</li>
            <li>Full Name: {{ $order->fullname }}</li>
            <li>Phone: {{ $order->phone }}</li>
            <li>Email: {{ $order->email }}</li>
            <li>Total: Rp. {{ number_format($order->total, 0, ',', '.') }}</li>
        </ul>
        <a href="{{ url('/pesanmakan') }}">Back to Home</a>
    </div>
</body>
</html>
