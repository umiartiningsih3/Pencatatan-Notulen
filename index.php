<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notulen Tracker</title>

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      min-height: 100vh;
      background: linear-gradient(135deg, #003973, #007adf);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
    }

    .hero {
      max-width: 1100px;
      width: 90%;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 50px;
      align-items: center;
    }

    .hero-img img {
      width: 100%;
      max-width: 480px;
      display: block;
      margin: auto;
      filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));
    }

    .hero-content h1 {
      font-size: 3rem;
      font-weight: 700;
      line-height: 1.2;
      margin-bottom: 20px;
    }

    .hero-content p {
      font-size: 1.1rem;
      opacity: 0.9;
      margin-bottom: 35px;
    }

    .btn {
      display: inline-block;
      padding: 14px 36px;
      background: #fff;
      color: #007adf;
      font-weight: 600;
      border-radius: 50px;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 30px rgba(0,0,0,0.3);
    }

    footer {
      position: absolute;
      bottom: 20px;
      text-align: center;
      font-size: 0.9rem;
      opacity: 0.8;
      width: 100%;
    }

    /* Responsive */
    @media (max-width: 900px) {
      .hero {
        grid-template-columns: 1fr;
        text-align: center;
      }

      .hero-img {
        order: -1;
      }

      .hero-content h1 {
        font-size: 2.4rem;
      }
    }
  </style>
</head>

<body>

  <section class="hero">
    <!-- GAMBAR KIRI -->
    <div class="hero-img">
      <!-- Ganti gambar sesuai kebutuhan -->
      <img src="gambar.png" alt="Ilustrasi Rapat">
    </div>

    <!-- TEKS KANAN -->
    <div class="hero-content">
      <h1>Selamat Datang di<br>Notulen Tracker</h1>
      <p>
        Solusi digital modern untuk mencatat, mengelola, dan memantau
        hasil rapat secara efisien dan profesional.
      </p>
      <a href="login.php" class="btn">Masuk Sekarang</a>
    </div>
  </section>

  <footer>
    © 2025 Notulen Tracker. Semua hak cipta dilindungi.
  </footer>

</body>
</html>

