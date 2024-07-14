<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Landing Page Billiard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;300;400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="style_web/style.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
</head>
<body>
    <div class="loader-wrapper">
        <div class="loader"></div>
    </div>
    <header>
        <div class="logo">BilliardClub</div>
        <nav>
            <ul>
                <li><a href="#tentang">Tentang Kami</a></li>
                <li><a href="#layanan">Layanan</a></li>
                <li><a href="#galeri">Galeri</a></li>
                <li><a href="#kontak">Kontak</a></li>
            </ul>
        </nav>
        <a href="#kontak" class="cta">Pesan Sekarang</a>
    </header>
    <section class="hero">
        <div class="hero-content">
            <h1>Selamat Datang di BilliardClub</h1>
            <p>Tempat Terbaik untuk Bermain Billiard dan Bersantai</p>
            <a href="#tentang" class="cta">Pelajari Lebih Lanjut</a>
        </div>
    </section>
    <section id="tentang" class="about">
        <h2>Tentang Kami</h2>
        <div class="about-content">
            <div class="about-text">
                <p>
                    Kami adalah BilliardClub, klub billiard terkemuka yang menawarkan
                    pengalaman bermain terbaik. Misi kami adalah menyediakan tempat yang
                    nyaman dan menyenangkan untuk semua penggemar billiard. Kami
                    memiliki meja billiard berkualitas tinggi, serta pelatih profesional
                    yang siap membantu Anda meningkatkan kemampuan bermain billiard
                    Anda.
                </p>
            </div>
            <div class="about-image">
                <img src="style_web/img/j.jpg" alt="Tentang Kami" />
            </div>
        </div>
    </section>
    <section id="layanan" class="services">
        <h2>Layanan Kami</h2>
        <div class="service-cards">
            <div class="card">
                <h3>Sewa Meja Billiard</h3>
                <p>Rasakan sensasi bermain billiard dengan meja kualitas terbaik.</p>
            </div>
            <div class="card">
                <h3>Pelatihan Billiard</h3>
                <p>
                    Belajar dari pelatih profesional kami untuk meningkatkan kemampuan
                    Anda.
                </p>
            </div>
            <div class="card">
                <h3>Turnamen Billiard</h3>
                <p>Ikuti turnamen dan menangkan hadiah menarik.</p>
            </div>
        </div>
    </section>
    <section id="galeri" class="gallery">
        <h2>Galeri Kami</h2>
        <div class="gallery-grid">
            @foreach($mejas as $meja)
            <div class="portfolio-box">
                @if ($meja->image)
                    <img src="{{ $meja->image->getUrl('preview') }}">
                @endif
                <div class="portfolio-layer">
                    <h4>{{ $meja->name }}</h4>
                    <p>{{ $meja->description }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    <section id="kontak" class="contact">
        <h2>Kontak Kami</h2>
        <div class="contact-form-wrapper">
            <form id="booking-form" action="/submit-booking" method="POST">
                @csrf
                <label for="fullname">Full Name:</label>
                <input type="text" id="fullname" name="fullname" required>
                <label for="phone">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter phone number with country code, e.g., 628123456789">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <label for="meja">Pilih Table:</label>
                <select id="table_id" name="table_id" required>
                    @foreach($mejas as $meja)
                        <option value="{{ $meja->id }}">{{ $meja->name }}</option>
                    @endforeach
                </select>
                <label for="start_book">Tanggal & Jam Mulai:</label>
                <input type="text" id="start_book" name="start_book" required />
                <label for="finish_book">Tanggal & Jam Selesai:</label>
                <input type="text" id="finish_book" name="finish_book" required />
                <button type="submit">Kirim</button>
            </form>
        </div>
    </section>
    <footer>
        <div class="footer-content">
            <div class="footer-info">
                <h3>BilliardClub</h3>
                <p>
                    Location: Ruko Paramount Dot Com Red, Jl. Boulevard Raya Gading
                    Serpong No.5, RW.6, Kelapa Dua, Tangerang Regency, Banten 15810
                </p>
                <p>Phone: 0895-2555-9560</p>
                <p>Email: info@billiardclub.com</p>
            </div>
            <div class="footer-social">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
        <p>
            Created by
            <a href="https://www.instagram.com/galihls_/">Angga Muhammad Galih</a>
            And Mocci. &copy; 2024 BilliardClub. All rights reserved.
        </p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script src="style_web/script.js"></script>
    <script>
      document.getElementById('booking-form').addEventListener('submit', function(event) {
            event.preventDefault();

            // Ambil data dari form
            const tableId = document.getElementById('table_id').value;
            const startBook = document.getElementById('start_book').value;
            const finishBook = document.getElementById('finish_book').value;
            const fullname = document.getElementById('fullname').value;
            const phone = document.getElementById('phone').value;
            const email = document.getElementById('email').value;

            fetch('/submit-booking', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    table_id: tableId,
                    start_book: startBook,
                    finish_book: finishBook,
                    fullname: fullname,
                    phone: phone,
                    email: email,
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    swal('Success', data.message, 'success');
                } else {
                    swal('Error', data.message, 'error');
                }
            })
        });
    </script>
</body>
</html>
