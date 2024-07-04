<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payment</title>
    <link rel="stylesheet" href="tampilan_scan/style.css">
    
    <!-- font awesome icons cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <h1>Midtrans Payment</h1>
    <button id="pay-button">Pay!</button>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}"></script>
    <script>
        document.getElementById('pay-button').onclick = function () {
            snap.pay('{{ $snapToken }}', {
                onSuccess: function (result) {
                    alert("Payment success!"); 
                    window.location.href = "{{ route('order.success', $order->id) }}";
                },
                onPending: function (result) {
                    alert("Waiting for your payment!"); 
                },
                onError: function (result) {
                    alert("Payment failed!"); 
                },
                onClose: function () {
                    alert('You closed the popup without finishing the payment');
                }
            });
        };
    </script>
</body>
</html>