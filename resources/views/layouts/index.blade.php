<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>reservasi</title>
    <link rel="stylesheet" href="tampilan_scan/style.css">
    
    <!-- font awesome icons cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <header class="header">
        <nav class="header--menu">
            <div class="burger--icon">
                <i class="fa-solid fa-bars"></i>
            </div>
            <div class="search--box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" placeholder="search" />
            </div>
            <div class="menu--icons">
                <i class="fa-solid fa-bowl-food"></i>
                <div class="cart-icon">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>{{ count((array) session('cart')) }}</span>
                </div>
            </div>
        </nav>
    </header>

    <!-- cover section -->
    <section class="cover" style="background-image: url({{ $galleris[3]->image->getUrl('preview') }})">
        <div class="cover--overlay">
            <h1>{{ $galleris[3]->title }}</h1>
            <span class="slogan">{!! $galleris[3]->description !!}</span>
        </div>
    </section>

    <!-- menu list -->
    <main>
        @yield('carditem')

        <!-- cart sidebar section -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-close">
                <i class="fa-solid fa-close"></i>
            </div>
                <div class="cart-menu">
                    <h3>My Cart</h3>
                    <div class="cart-items">
                        @if (session('cart'))
                            @foreach (session('cart') as $id => $details)
                                <div class="individual-cart-item">
                                <span>({{ $details['quantity'] }}x) {{ $details['product_name'] }}</span>
                                <span class="cart-item-price">Rp.{{ $details['price'] * $details['quantity'] }}</span>
                                <button class="remove-btn" data-id="{{ route('remove_from_cart', $id) }}"><i class="fa-solid fa-times"></i></button>
                                <button class="decrease-btn" data-id="{{ route('decrease_cart', $id) }}"><i class="fa-solid fa-minus"></i></button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="sidebar--footer">
                    @php $total = 0 @endphp
                    @foreach((array) session('cart') as $id => $details)
                        @php $total += $details['price'] * $details['quantity'] @endphp
                    @endforeach
                    <div class="total--amount">
                        <h5>Total</h5>
                        <div class="cart-total">Rp. {{ $total }}</div>
                    </div>
                    <form id="checkout-form" action="{{ route('checkout.index') }}" method="GET">
                        <button type="submit" class="checkout-btn">Checkout</button>
                    </form>
                </div>                
        </div>
    </main>
    <script>
        const cartIcon = document.querySelector('.cart-icon');
        const sidebar = document.getElementById('sidebar');

        cartIcon.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        const closeButton = document.querySelector('.sidebar-close');
        closeButton.addEventListener('click', () => {
            sidebar.classList.remove('open');
        });

        // remove
        document.querySelectorAll('.remove-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const url = this.getAttribute('data-id');
                fetch(url, {
                    method: 'GET',
                })
                .then(response => {
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        console.error('Error removing product from cart:', response.statusText);
                    }
                })
                .catch(error => console.error('Error removing product from cart:', error));
            });
        });

        // untuk mengurangi pesanan
        document.querySelectorAll('.decrease-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const url = this.getAttribute('data-id');
                fetch(url, {
                    method: 'GET',
                })
                .then(response => {
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        console.error('Error decreasing product quantity:', response.statusText);
                    }
                })
                .catch(error => console.error('Error decreasing product quantity:', error));
            });
        });

        // fitur search
        const searchInput = document.getElementById('searchInput');

        searchInput.addEventListener('input', () => {
            const searchText = searchInput.value.toLowerCase().trim();

            const cardItems = document.querySelectorAll('.card');

            cardItems.forEach(item => {
                const itemName = item.querySelector('.card--title').textContent.toLowerCase();
                if (itemName.includes(searchText)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
