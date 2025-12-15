<?php
$activePage = basename($_SERVER['PHP_SELF']);
// ==========================================================
// 1. KONEKSI DATABASE DAN PENGAMBILAN DATA PROFIL PENGGUNA
// ==========================================================

// Detail koneksi database
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "notulen_db";

// 🚨 GANTI DENGAN $_SESSION['user_id'] ASLI SETELAH IMPLEMENTASI LOGIN
$user_id = 1; 

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil Data Profil untuk Navbar (Tabel: notulis)
$query_profile = "SELECT nama_lengkap, email, foto_profile FROM notulis WHERE id = ?";
$stmt = mysqli_prepare($conn, $query_profile);
// Pastikan $user_id adalah integer
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result_profile = mysqli_stmt_get_result($stmt);

// Data default jika user_id tidak ditemukan atau kosong
$profile_data = [
    'nama_lengkap' => 'Notulis Tamu',
    'email' => 'tamu@notulen.com',
    'foto_profile' => 'user.png' // Pastikan ada gambar default di folder Anda
];

if ($row = mysqli_fetch_assoc($result_profile)) {
    // Timpa data default dengan data dari database
    $profile_data['nama_lengkap'] = $row['nama_lengkap'];
    $profile_data['email'] = $row['email'];
    // Gunakan foto_profile dari DB jika tidak kosong
    if (!empty($row['foto_profile'])) {
        $profile_data['foto_profile'] = $row['foto_profile'];
    }
}

// Variabel untuk digunakan di HTML
$dropdown_email = htmlspecialchars($profile_data['email']);
$dropdown_nama = htmlspecialchars($profile_data['nama_lengkap']);


// ==========================================================
// 2. PROSES SUBMIT FORM KONTAK (KODE ASLI ANDA)
// ==========================================================

$pesan_terkirim = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Digunakan untuk simulasi AJAX di bagian JavaScript, bukan untuk PHP form submit biasa
    // Karena form di bawah menggunakan e.preventDefault(), bagian ini tidak akan dieksekusi 
    // kecuali Anda mengubah form submission-nya menjadi sync/AJAX PHP file terpisah.
    
    // Namun, jika Anda ingin menggunakan PHP, ini adalah kode yang benar:
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pesan = mysqli_real_escape_string($conn, $_POST['pesan']);

    $sql = "INSERT INTO kontak (nama, email, pesan) VALUES ('$nama', '$email', '$pesan')";
    if (mysqli_query($conn, $sql)) {
        // Jika berhasil, header redirect untuk menghindari resubmission form
        // header("Location: kontak.php?status=success");
        // exit();
        $pesan_terkirim = true;
    } else {
        // echo "Error: " . mysqli_error($conn); // sebaiknya tidak ditampilkan ke user
        // header("Location: kontak.php?status=error");
        // exit();
    }
}

// Tutup statement
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kontak | Notulen Tracker</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      font-family: Poppins,system-ui,-apple-system,Segoe UI,Roboto;
      background-color: #f5f7fa;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      padding-top: 80px;
    }

    /* Navbar utama */
.custom-navbar {
  background-color: #003366;
  height: 70px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}

/* List navbar */
.nav-effect {
  gap: 10px; /* 🔹 jarak antar item */
}
/* Item navbar */
.nav-effect .nav-link {
  color: #dce3ea !important;
  padding: 10px 18px; /* 🔹 jarak dalam */
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 500;
  transition: all 0.3s ease;
  position: relative;
}

/* Hover */
.navbar-nav .nav-link:hover {
  background: rgba(255,255,255,0.08);
  color: #ffffff !important;
}

/* Active page */
.navbar-nav .nav-link.active {
  background: rgba(255,255,255,0.15);
  color: #ffffff !important;
  font-weight: 600;
}

/* Icon */
.nav-effect .nav-link i {
  font-size: 1.1rem;
  transition: transform 0.3s ease;
}

/* Icon animasi */
.nav-effect .nav-link:hover i {
  transform: scale(1.15);
}

/* Active icon */
.nav-effect .nav-link.active i {
  color: #0d6efd;
}


/* ===== BRAND PRO ===== */
.brand-pro {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
}

.brand-pro img {
  width: 42px;
  height: 42px;
  border-radius: 100px;
  padding: 6px;
  background: linear-gradient(135deg, #ffffff, #e3f2fd);
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

/* Hover brand */
.brand-pro:hover img {
  transform: scale(1.08) rotate(-4deg);
  box-shadow: 0 8px 25px rgba(144,202,249,0.45);
}
    /* Dropdown User Info Styles */
    .dropdown-menu .user-info-header {
      display: flex; 
      align-items: center;
      padding: 10px 15px;
    }
    .dropdown-menu .user-avatar {
      width: 40px; 
      height: 40px;
      border-radius: 50%; 
      object-fit: cover;
      margin-right: 10px;
    }
    .dropdown-menu .user-text small {
      display: block;
      margin-top: -3px; 
    }
    /* End Dropdown User Info Styles */

    /* Konten */
    main {
      flex: 1;
    }

    .container {
      max-width: 700px;
    }

    .card {
      border: none;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
    }

    .btn-primary {
      background-color: #003366;
      border: none;
    }

    .btn-primary:hover {
      background-color: #303f9f;
    }

    /* Footer */
    footer {
      background-color: #003366;
      color: white;
      text-align: center;
      padding: 15px 0;
      font-size: 0.9rem;
      margin-top: auto;
    }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark fixed-top px-4 custom-navbar">
  <a class="navbar-brand brand-pro" href="dashboard.php">
  <img src="logono.jpeg" alt="Logo">
  <div class="brand-info">
    <span class="brand-name">Notulen</span>
    <span class="brand-tagline">Tracker</span>
  </div>
</a>



  <div class="collapse navbar-collapse">
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
        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown">
          Notulis
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdown">
          <li class="user-info-header">
            <img
              src="<?php echo htmlspecialchars($profile_data['foto_profile']); ?>"
              alt="Avatar"
              class="user-avatar"
            >
            <div class="user-text">
              <strong><?php echo $dropdown_nama; ?></strong>
              <small class="text-muted"><?php echo $dropdown_email; ?></small>
            </div>
            </li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="profile.php">Profil</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a id="logoutLink" class="dropdown-item text-danger" href="#">Keluar</a></li>
        </ul>
        </li>
      </ul>
    </div>
  </nav>

  <main>
    <div class="container mt-5">
      <h2 class="text-center mb-4 text-primary fw-bold">Hubungi Kami</h2>
      <p class="text-center mb-5">Jika Anda memiliki pertanyaan, saran, atau kendala terkait aplikasi Notulen Tracker, silakan isi form di bawah ini.</p>
      
      <?php if ($pesan_terkirim): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          ✅ Pesan Anda berhasil dikirim! Kami akan segera merespons.
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <div class="card p-4">
        <form id="formKontak" method="POST" action="">
          <div class="mb-3">
            <label for="nama" class="form-label">Nama Lengkap</label>
            <input type="text" id="nama" class="form-control" name="nama" placeholder="Masukkan nama Anda" required>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" class="form-control" name="email" placeholder="Masukkan email Anda" required>
          </div>

          <div class="mb-3">
            <label for="pesan" class="form-label">Pesan</label>
            <textarea id="pesan" rows="4" class="form-control" name="pesan" placeholder="Tulis pesan Anda di sini..." required></textarea>
          </div>

          <div class="text-center">
            <button type="submit" class="btn btn-primary px-4">Kirim Pesan</button>
            <button type="reset" class="btn btn-secondary px-4">Batal</button>
          </div>
        </form>
      </div>

      <div class="text-center mt-5">
        <h5 class="text-primary fw-bold">Informasi Kontak Lainnya</h5>
        <p class="mb-1">📍 Politeknik Negeri Batam</p>
        <p class="mb-1">📞 0821-1234-5678</p>
        <p>✉ notulentracker@gmail.com</p>
      </div>
    </div>
  </main>

  <footer>
    ©2025 Notulen Tracker. Semua hak cipta dilindungi
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Kirim pesan (Simulasi notifikasi JS, membiarkan PHP yang handle submit)
    document.getElementById("formKontak").addEventListener("submit", function(e) {
      // e.preventDefault(); 
      // Karena PHP sudah diatur untuk memproses POST, 
      // kita hilangkan preventDefault agar halaman reload dan menampilkan alert PHP

      // Jika Anda ingin mempertahankan notifikasi JS tanpa reload:
      /*
      e.preventDefault(); 
      alert("Pesan Anda berhasil dikirim! Terima kasih telah menghubungi kami 😊");
      this.reset();
      */
    });

  // Fungsi logout melalui menu dropdown
  document.getElementById("logoutLink").addEventListener("click", (e) => {
    e.preventDefault();
    const konfirmasi = confirm("Apakah Anda yakin ingin keluar dari Notulen Tracker?");
    if (konfirmasi) {
      window.location.href = "login.php";
    }
  });
</script>

<?php
// Tutup koneksi database
mysqli_close($conn);
?>
</body>
</html>