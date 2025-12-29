<?php
session_start();

if (!isset($_SESSION['id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['id']; 
$activePage = basename($_SERVER['PHP_SELF']);

$conn = mysqli_connect("localhost", "root", "", "notulen_db");
if (!$conn) {
  die("Koneksi database gagal: " . mysqli_connect_error());
}

$query_profile = "SELECT nama_lengkap, email, foto_profile, role FROM pengguna WHERE id = ?";
$stmt = mysqli_prepare($conn, $query_profile);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$profile_db = mysqli_fetch_assoc($result);

if (!$profile_db) {
  session_destroy();
  header("Location: login.php");
  exit();
}

$role_display = !empty($profile_db['role']) ? $profile_db['role'] : 'Notulis';
$dropdown_nama = htmlspecialchars($profile_db['nama_lengkap']);
$dropdown_email = htmlspecialchars($profile_db['email']);
  
$dropdown_foto = (!empty($profile_db['foto_profile']) && file_exists($profile_db['foto_profile'])) 
? htmlspecialchars($profile_db['foto_profile']) 
: 'userr.png';

if (isset($stmt) && $stmt) { 
    mysqli_stmt_close($stmt);
}

$status_kirim = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_kirim'])) {
    $to = "notulen.trackerr@gmail.com";
    $subject = "Pesan Kontak: " . $dropdown_nama;
    $pesan_user = $_POST['pesan'];

    $headers = "From: " . $dropdown_email . "\r\n";
    $headers .= "Reply-To: " . $dropdown_email . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $body = "Anda menerima pesan baru dari formulir kontak Notulen Tracker:\n\n";
    $body .= "Nama Pengirim: " . $dropdown_nama . "\n";
    $body .= "Email Pengirim: " . $dropdown_email . "\n";
    $body .= "Isi Pesan:\n" . $pesan_user . "\n";

    if (@mail($to, $subject, $body, $headers)) {
        $status_kirim = "success";
    } else {
        $status_kirim = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kontak Kami</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body { 
      font-family: Poppins,system-ui,-apple-system,Segoe UI,Roboto;
      background-color: #f5f7fa;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      margin: 0;
      padding-top: 70px; 
      background: url('gambarr.png') no-repeat center center fixed !important;
      background-size: cover !important;
      position: relative;
      z-index: 0;
      font-size: 18px;
    }

    body::before {
      content: "";
      position: fixed;
      top: 0; left: 0; width: 100%; height: 100%;
      background-color: rgba(245, 247, 250, 0.85); 
      z-index: -1;
    }

    .custom-navbar {
      background-color: #003366;
      height: 70px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }

    .nav-effect {
      gap: 10px;
    }

    .nav-effect .nav-link {
      color: #dce3ea !important;
      padding: 10px 18px; 
      border-radius: 12px;
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 500;
      transition: all 0.3s ease;
      position: relative;
    }

    .navbar-nav .nav-link:hover {
      background: rgba(255,255,255,0.08);
      color: #ffffff !important;
    }

    .navbar-nav .nav-link.active {
      background: rgba(255,255,255,0.15);
      color: #ffffff !important;
      font-weight: 600;
    }

    .nav-effect .nav-link i {
      font-size: 1.1rem;
      transition: transform 0.3s ease;
    }

    .nav-effect .nav-link:hover i {
      transform: scale(1.15);
    }

    .nav-effect .nav-link.active i {
      color: #0d6efd;
    }

    .brand-pro {
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
    }

    .brand-pro img {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      object-fit: cover;
      background: #ffffff;
      border: 2px solid rgba(255,255,255,0.2);
      transition: all 0.35s ease;
    }

    .brand-info {
      display: flex;
      flex-direction: column;
      line-height: 1.1;
    }

    .brand-name {
      font-size: 21px;
      font-weight: 700;
      color: #ffffff;
      letter-spacing: 0.3px;
    }

    .brand-tagline {
      font-size: 13px;
      color: #90caf9;
      letter-spacing: 1px;
    }

    .brand-pro:hover img {
      transform: scale(1.08) rotate(-4deg);
      box-shadow: 0 8px 25px rgba(144,202,249,0.45);
    }
    .dropdown-menu {
        min-width: 250px !important;
        border-radius: 8px; 
        padding: 0;
    }
    .dropdown-menu .user-info-header {
      display: flex; 
      align-items: center;
      padding: 10px 15px;
      margin-bottom: 0;
    }
    .dropdown-menu .user-avatar {
      width: 50px; 
      height: 50px;
      border-radius: 50%; 
      object-fit: cover;
      margin-right: 12px;
      background-color: #f0f0f0;
    }
    .dropdown-menu .user-text {
        display: flex;
        flex-direction: column;
        overflow: hidden; 
    }
    .dropdown-menu .user-text strong {
        font-size: 15px;
        font-weight: 600;
        line-height: 1.2;
    }
    .dropdown-menu .user-text small {
      display: block;
      font-size: 13px;
      color: #6c757d; 
      line-height: 1.2;
    }
    
    .dropdown-menu .dropdown-item {
      display: flex;
      align-items: center;
      padding: 5px 15px; 
    }
    
    .dropdown-menu .dropdown-item i {
      font-size: 1.1rem;
      width: 20px;
      text-align: center;
      margin-right: 8px;
    }
    
    .dropdown-menu .user-text small {
      margin-top: 0; 
    }
    main {
      flex: 1;
    }

    .contact-container {
      max-width: 600px;
      margin: 50px auto;
      background: rgba(255, 255, 255, 0.9) !important;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      backdrop-filter: blur(5px);
    }

    footer {
      background-color: #003366;
      color: white;
      text-align: center;
      padding: 15px 0;
      font-size: 0.9rem;
      margin-top: auto;
    }

    @media (max-width: 991.98px) {
        .custom-navbar {
            height: auto;
            padding: 10px 15px;
        }
    
        body {
            padding-top: 100px;
        }

        .navbar-collapse {
            background: #003366; 
            padding: 15px;
            border-radius: 10px;
            margin-top: 10px;
        }
    }

        @media (max-width: 576px) {
        .brand-name {
            font-size: 18px; 
        }
    
        .profile-sidebar, .form-card {
            padding: 20px; 
        }
    
        .profile-sidebar img.main-avatar {
            width: 100px;
            height: 100px;
        }
    }
  </style>
</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-dark fixed-top px-4 custom-navbar">
    <a class="navbar-brand brand-pro" href="dashboard.php">
      <img src="logono.png" alt="Logo">
      <div class="brand-info">
        <span class="brand-name">Notulen</span>
        <span class="brand-tagline">TRACKER</span>
      </div>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto nav-effect">
        <li class="nav-item">
          <a class="nav-link" href="dashboard.php">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="daftar_notulen.php">
            <i class="bi bi-file-text"></i>
            <span>Daftar Notulen</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link active" href="kontak.php">
            <i class="bi bi-envelope"></i>
            <span>Kontak</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="FAQ.php">
            <i class="bi bi-question-circle"></i>
            <span>FAQ</span>
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle me-1"></i> <?php echo ucwords(htmlspecialchars($role_display)); ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
            <li class="user-info-header">
              <img
                src="<?php echo $dropdown_foto; ?>"
                alt="Avatar"
                class="user-avatar"
              >
              <div class="user-text">
                <strong class="text-truncate"><?php echo $dropdown_nama; ?></strong>
                <small class="text-muted text-truncate"><?php echo $dropdown_email; ?></small>
              </div>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> Profil Saya</a></li>
            <li><a id="logoutLink" class="dropdown-item text-danger" href="login.php"><i class="bi bi-box-arrow-right"></i> Keluar</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>

  <main>
    <div class="container">
      <div class="contact-container">
        <h2 class="text-center mb-4 text-primary fw-bold">Hubungi Kami</h2>
        <p class="text-center text-muted mb-4">Ada kendala atau ingin tanya-tanya? Yuk, sapa kami melalui pesan di bawah ini!</p>

        <form action="kontak.php" method="POST">
          <div class="mb-3">
            <label for="nama" class="form-label">Nama Lengkap</label>
            <input type="text" id="nama" class="form-control bg-light" name="nama" 
                   value="<?php echo $dropdown_nama; ?>" readonly required>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" class="form-control bg-light" name="email" 
                   value="<?php echo $dropdown_email; ?>" readonly required>
          </div>

          <div class="mb-3">
            <label for="pesan" class="form-label">Pesan</label>
            <textarea id="pesan" rows="4" class="form-control" name="pesan" placeholder="Tulis pesan Anda di sini..." required></textarea>
          </div>

          <div class="text-center">
            <button type="submit" name="btn_kirim" class="btn btn-primary px-4">Kirim Pesan</button>
            <button type="reset" class="btn btn-secondary px-4">Batal</button>
          </div>
        </form>
      </div>

      <div class="text-center mt-3 text-dark">
        <h5 class="text-primary fw-bold">Informasi Kontak Lainnya</h5>
        <p class="mb-1">📍 Politeknik Negeri Batam</p>
        <p class="mb-1">📞 0821-1234-5678</p>
        <br>
      </div>
    </div>
  </main>

  <footer>
    ©2025 Notulen Tracker. Semua hak cipta dilindungi
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.getElementById("logoutLink").addEventListener("click", (e) => {
      e.preventDefault();
      const konfirmasi = confirm("Apakah Anda yakin ingin keluar dari Notulen Tracker?");
      if (konfirmasi) {
        window.location.href = "login.php";
      }
    });

    <?php if ($status_kirim === "success"): ?>
      Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: 'Pesan Anda telah berhasil terkirim ke tim Notulen Tracker.',
          confirmButtonColor: '#003366'
      });
    <?php elseif ($status_kirim === "error"): ?>
      Swal.fire({
          icon: 'info',
          title: 'Info Pengiriman',
          text: 'Jika di localhost, email tidak benar-benar terkirim. Fitur ini memerlukan server hosting aktif.',
          confirmButtonColor: '#003366'
      });
    <?php endif; ?>
  </script>

  <?php
  if (isset($conn)) {
    mysqli_close($conn);
  }
  ?>
</body>
</html>