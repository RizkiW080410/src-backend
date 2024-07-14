<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Checkout</title>
    <link rel="stylesheet" href="tampilan_scan/styleco.css">
    
    <!-- font awesome icons cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Midtrans Snap.js -->
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}"></script>
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

        <form id="checkout-form">
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
            <div>
                <label for="table_id">Select Table:</label>
                <select id="table_id" name="table_id" required>
                    @foreach($tables as $table)
                        <option value="{{ $table->id }}">{{ $table->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit">Proceed to Payment</button>
        </form>
    </div>

    <script>
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            e.preventDefault();

            let form = e.target;

            fetch("{{ route('checkout.process') }}", {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.snapToken) {
                    window.snap.pay(data.snapToken, {
                        onSuccess: function(result) {
                            alert("Payment success!");
                            window.location.href = "{{ url('/order-success') }}/" + data.order_id;
                        },
                        onPending: function(result) {
                            alert("Waiting for your payment!");
                        },
                        onError: function(result) {
                            alert("Payment failed!");
                        },
                        onClose: function() {
                            alert('You closed the popup without finishing the payment');
                        }
                    });
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>
