document.addEventListener('DOMContentLoaded', () => {
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    const cartItemCount = document.querySelector('.cart-icon span');
    const cartItemsList = document.querySelector('.cart-tems');
    const cartTotal = document.querySelector('.cart-total');
    const cartIcon = document.querySelector('.cart-icon');
    const sidebar = document.getElementById('sidebar');
    const searchInput = document.querySelector('.search--box input');

    let cartItems = [];
    let totalAmount = 0;

    addToCartButtons.forEach((button, index) => {
        button.addEventListener('click', () => {
            const item = {
                name: document.querySelectorAll('.card .card--title')[index].textContent,
                price: parseFloat(
                    document.querySelectorAll('.card .card--price .price')[index].textContent.replace('Rp', '').replace('.', '').replace(',', '.')
                ), // Mengambil harga dari elemen HTML dengan format yang sesuai
                quantity: 1,
            };

            const existingItemIndex = cartItems.findIndex((cartItem) => cartItem.name === item.name);
            if (existingItemIndex !== -1) {
                cartItems[existingItemIndex].quantity++;
            } else {
                cartItems.push(item);
            }

            totalAmount = calculateTotalAmount(); // Memperbarui total harga setiap kali menambah item
            updateCartUI();
        });
    });

    function calculateTotalAmount() {
        return cartItems.reduce((total, item) => total + (item.price * item.quantity), 0);
    }

    function updateCartUI() {
        updateCartItemCount(cartItems.length);
        updateCartItemList();
        updateCartTotal();
    }

    function updateCartItemCount(count) {
        cartItemCount.textContent = count;
    }

    function updateCartItemList() {
        cartItemsList.innerHTML = '';
        cartItems.forEach((item, index) => {
            const cartItem = document.createElement('div');
            cartItem.classList.add('cart-item', 'individual-cart-item');
            cartItem.innerHTML = `
                <span>(${item.quantity}x) ${item.name}</span>
                <span class="cart-item-price">Rp${(item.price * item.quantity).toLocaleString()}</span>
                <button class="remove-btn" data-index="${index}"><i class="fa-solid fa-times"></i></button>
                <button class="decrease-btn" data-index="${index}"><i class="fa-solid fa-minus" data-index="${index}"></i></button> <!-- Tombol untuk mengurangi jumlah pesanan -->
            `;
            cartItemsList.appendChild(cartItem);
        });

        const removeButtons = document.querySelectorAll('.remove-btn');
        removeButtons.forEach((button) => {
            button.addEventListener('click', (event) => {
                const index = event.target.dataset.index;
                removeItemFromCart(index);
            });
        });

        const decreaseButtons = document.querySelectorAll('.decrease-btn');
        decreaseButtons.forEach((button) => {
            button.addEventListener('click', (event) => {
                const index = event.target.dataset.index;
                decreaseItemQuantity(index);
            });
        });
    }

    function decreaseItemQuantity(index) {
        if (cartItems[index].quantity > 1) {
            cartItems[index].quantity--; // Kurangi satu dari jumlah pesanan
        } else {
            cartItems.splice(index, 1); // Jika jumlah pesanan hanya satu, hapus item dari keranjang
        }
        totalAmount = calculateTotalAmount(); // Memperbarui total harga setiap kali mengurangi item
        updateCartUI();
    }

    function removeItemFromCart(index) {
        const removedItem = cartItems.splice(index, 1)[0];
        totalAmount = calculateTotalAmount(); // Memperbarui total harga setiap kali menghapus item
        updateCartUI();
    }

    function updateCartTotal() {
        cartTotal.textContent = `Rp${totalAmount.toLocaleString()}`;
    }

    cartIcon.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });

    const closeButton = document.querySelector('.sidebar-close');
    closeButton.addEventListener('click', () => {
        sidebar.classList.remove('open');
    });

    // Tambahkan event listener pada input search
    searchInput.addEventListener('input', () => {
        // Dapatkan nilai input dari elemen search
        const searchText = searchInput.value.toLowerCase().trim();

        // Seleksi semua item menu atau produk
        const menuItems = document.querySelectorAll('.menu--item');
        const cardItems = document.querySelectorAll('.card');

        // Lakukan iterasi pada setiap item dan terapkan filtering
        menuItems.forEach(item => {
            const itemName = item.querySelector('h5').textContent.toLowerCase();
            // Jika nama item mengandung teks pencarian, tampilkan item tersebut, jika tidak, sembunyikan
            if (itemName.includes(searchText)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });

        // Lakukan hal yang sama untuk item kartu produk
        cardItems.forEach(item => {
            const itemName = item.querySelector('.card--title').textContent.toLowerCase();
            if (itemName.includes(searchText)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});
