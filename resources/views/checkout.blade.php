<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Checkout</title>
    <link rel="stylesheet" href="tampilan_scan/style.css">
    
    <!-- font awesome icons cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <h1>Checkout</h1>
    <div>
        <h3>Products in your cart:</h3>
        <ul>
            @php $total = 0; @endphp
            @foreach($cart as $id => $details)
                <li>{{ $details['product_name'] }} ({{ $details['quantity'] }} x {{ $details['price'] }})</li>
                @php $total += $details['quantity'] * $details['price']; @endphp
            @endforeach
        </ul>
        <h3>Total: Rp. {{ number_format($total, 0, ',', '.') }}</h3>

        <form action="{{ route('checkout.process') }}" method="POST">
            @csrf
            <input type="hidden" name="total" value="{{ $total }}">
            
            <div>
                <label for="fullname">Full Name:</label>
                <input type="text" id="fullname" name="fullname" required>
            </div>
            <div>
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <button type="submit">Proceed to Payment</button>
        </form>
    </div>
</body>
</html>