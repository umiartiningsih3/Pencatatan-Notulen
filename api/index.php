<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notulen Tracker</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body, html {
      height: 100%;
      width: 100%;
      overflow-x: hidden;
    }

    .hero-wrapper {
      position: relative;
      min-height: 100vh;
      width: 100%;
      background: linear-gradient(rgba(0, 15, 35, 0.65), rgba(0, 15, 35, 0.65)), 
                  url('gambarr.png');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      display: flex;
      flex-direction: column;
      color: white;
    }

    header {
      padding: 40px 5%;
      display: flex;
      justify-content: space-between;
      align-items: center;
      z-index: 10;
    }

    .logo {
      font-size: 1.8rem;
      font-weight: 800;
      display: flex;
      align-items: center;
      gap: 10px;
      background: linear-gradient(to right, #00d4ff, #007adf);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .btn-masuk {
      background-color: #00d4ff;
      color: #001833;
      padding: 10px 28px;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 700;
      transition: 0.3s ease;
      display: inline-block;
      border: none;
    }

    .btn-masuk:hover {
      background-color: #ffffff;
      transform: translateY(-2px);
    }

    .btn-masuk:active {
      background: linear-gradient(to right, #00d4ff, #007adf);
      color: #ffffff;
      transform: scale(0.95); /* Efek menekan */
    }

    .main-content {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0 5% 50px 5%;
    }

    .hero-grid {
      display: grid;
      grid-template-columns: 0.9fr 1.1fr; 
      gap: 10px;
      align-items: center;
      width: 100%;
      max-width: 1200px;
    }

    .visual-side {
      display: flex;
      justify-content: flex-end;
      animation: fadeInUp 1s ease-out forwards;
    }

    .visual-side img {
      width: 100%;
      max-width: 580px;
      filter: drop-shadow(0 20px 50px rgba(0,0,0,0.5));
      animation: float 5s ease-in-out infinite;
    }

    .text-side {
      padding-left: 0;
      animation: fadeInUp 1.2s ease-out forwards;
    }

    .text-float-wrapper {
      animation: float 5s ease-in-out infinite;
    }

    .text-side h1 {
      font-size: clamp(2.5rem, 4.5vw, 3.8rem);
      font-weight: 800;
      line-height: 1.1;
      margin-bottom: 20px;
      background: linear-gradient(to bottom, #ffffff 30%, #00d4ff 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      display: inline-block;
    }

    .no-break {
      white-space: nowrap;
    }

    .text-side p {
      font-size: 1.1rem;
      margin-bottom: 35px;
      opacity: 0.9;
      line-height: 1.6;
      color: #e0e0e0;
      max-width: 580px;
    }

    @keyframes fadeInUp {
      0% { opacity: 0; transform: translateY(40px); }
      100% { opacity: 1; transform: translateY(0); }
    }

    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-15px); }
    }

    @media (max-width: 992px) {
      .hero-grid { grid-template-columns: 1fr; text-align: center; gap: 30px; }
      .visual-side { justify-content: center; }
      .visual-side img { max-width: 400px; }
    }
  </style>
</head>
<body>

  <div class="hero-wrapper">
    <header>
      <div class="logo">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="url(#grad1)">
          <defs>
            <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="0%">
              <stop offset="0%" style="stop-color:#00d4ff;stop-opacity:1" />
              <stop offset="100%" style="stop-color:#007adf;stop-opacity:1" />
            </linearGradient>
          </defs>
          <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z"/>
        </svg>
        Notulen Tracker
      </div>
      <a href="login.php" class="btn-masuk">Masuk</a>
    </header>

    <main class="main-content">
      <div class="hero-grid">
        <div class="visual-side">
          <img src="index.png" alt="Visual">
        </div>

        <div class="text-side">
          <div class="text-float-wrapper">
            <br><br>
            <h1>Selamat Datang,<br><span class="no-break">di Notulen Tracker</span></h1>
            <p>Solusi digital modern untuk mencatat, mengelola, dan memantau hasil rapat secara efisien dan profesional.</p>
          </div>
        </div>
      </div>
    </main>

    <footer style="text-align: center; padding: 20px; opacity: 0.5; font-size: 0.8rem;">
      © 2025 Notulen Tracker. Semua hak cipta dilindungi.
    </footer>
  </div>

</body>
</html>