<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notulen Tracker</title>
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
      overflow-x: hidden;
    }
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes float {
      0% { transform: translateY(0px); }
      50% { transform: translateY(-20px); }
      100% { transform: translateY(0px); }
    }

    .hero {
      max-width: 1100px;
      width: 90%;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 50px;
      align-items: center;
    }

    .hero-img {
      display: flex;
      justify-content: center;
      animation: fadeInUp 1s ease-out;
    }

    .hero-img img {
      width: 100%;
      max-width: 480px;
      display: block;
      filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));
      animation: float 4s ease-in-out infinite;
    }
    .hero-content h1 {
      font-size: 3rem;
      font-weight: 700;
      line-height: 1.2;
      margin-bottom: 20px;
      animation: fadeInUp 0.8s ease-out forwards;
    }

    .hero-content p {
      font-size: 1.1rem;
      opacity: 0.9;
      margin-bottom: 35px;
      animation: fadeInUp 1s ease-out forwards;
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
      animation: fadeInUp 1.2s ease-out forwards;
    }

    .btn:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.3);
      background: #f8f9fa;
    }

    footer {
      position: absolute;
      bottom: 20px;
      text-align: center;
      font-size: 0.9rem;
      opacity: 0.8;
      width: 100%;
    }
    @media (max-width: 900px) {
      .hero {
        grid-template-columns: 1fr;
        text-align: center;
        padding-top: 50px;
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
    <div class="hero-img">
      <img src="gambar.png" alt="Ilustrasi Rapat">
    </div>

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