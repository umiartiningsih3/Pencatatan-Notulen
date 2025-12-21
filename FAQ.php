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
// Saat ini menggunakan ID statis 1
$user_id = 1; 

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    // Jika koneksi gagal, hentikan eksekusi dan tampilkan pesan error
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil Data Profil untuk Navbar (Tabel: notulis)
$query_profile = "SELECT nama_lengkap, email, foto_profile FROM notulis WHERE id = ?";
$stmt = mysqli_prepare($conn, $query_profile);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result_profile = mysqli_stmt_get_result($stmt);

// Data default jika user_id tidak ditemukan atau kosong
$profile_data = [
    'nama_lengkap' => 'Notulis Tamu',
    'email' => 'tamu@notulen.com',
    // Pastikan ada gambar default dengan nama ini (misal: user.png) di folder Anda
    'foto_profile' => 'user.png' 
];

if ($row = mysqli_fetch_assoc($result_profile)) {
    $profile_data['nama_lengkap'] = $row['nama_lengkap'];
    $profile_data['email'] = $row['email'];
    
    // Jika foto_profile dari DB kosong atau null, tetap gunakan default 'user.png'
    if (!empty($row['foto_profile'])) { 
        $profile_data['foto_profile'] = $row['foto_profile'];
    }
}

// Variabel untuk digunakan di HTML
$dropdown_email = htmlspecialchars($profile_data['email']);
$dropdown_nama = htmlspecialchars($profile_data['nama_lengkap']);
$dropdown_foto = htmlspecialchars($profile_data['foto_profile']);

// Tutup statement
if (isset($stmt) && $stmt) { 
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ | Notulen Tracker</title>

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
        margin: 0;
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
  width: 50px;
  height: 50px;
  border-radius: 100px;
  padding: 0px;
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
      /* Dropdown User Info Styles (DIOPTIMALKAN UNTUK MENYERUPAI GAMBAR) */
      .dropdown-menu {
          /* Untuk memastikan menu dropdown tidak terlalu lebar */
          min-width: 250px !important;
          border-radius: 8px; /* Lebih halus */
          padding: 0;
      }
      .dropdown-menu .user-info-header {
        display: flex; 
        align-items: center;
        padding: 10px 15px;
        margin-bottom: 0;
      }
      .dropdown-menu .user-avatar {
        width: 40px; 
        height: 40px;
        border-radius: 50%; 
        object-fit: cover;
        margin-right: 12px;
        background-color: #f0f0f0; /* Background jika gambar gagal load */
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
      
      /* Style untuk dropdown item dengan ikon */
      .dropdown-menu .dropdown-item {
        display: flex;
        align-items: center;
        padding: 8px 15px; 
      }
      
      .dropdown-menu .dropdown-item i {
        font-size: 1.1rem;
        width: 20px; /* Lebar tetap untuk ikon */
        text-align: center;
        margin-right: 8px; /* Jarak antara ikon dan teks */
      }
      
      /* Menghapus margin top bawaan small dari style lama */
      .dropdown-menu .user-text small {
        margin-top: 0; 
      }
      /* Akhir Dropdown User Info Styles */

      /* Konten */
      main {
        flex: 1;
      }

      h2 {
        color: #003366;
        font-weight: bold;
      }

      .accordion-button:focus {
        box-shadow: none;
      }

      /* Hanya tombol yang sedang dibuka (tidak collapsed) */
      .accordion-button:not(.collapsed) {
        background-color: #003366;
        color: white;
      }
      
      /* Tombol yang tertutup (collapsed) */
      .accordion-button.collapsed {
        background-color: #ffffff;
        color: #003366;
      }

      .accordion-body {
        background-color: #ffffff;
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
                    <a class="nav-link" href="kontak.php">
                        <i class="bi bi-envelope"></i>
                        <span>Kontak</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" href="FAQ.php">
                        <i class="bi bi-question-circle"></i>
                        <span>FAQ</span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Notulis
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
      <div class="container my-5">
        <h2 class="text-center mb-4 text-primary fw-bold">Pertanyaan Umum (FAQ)</h2>

        <div class="accordion" id="faqAccordion">
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="false">
                Apa itu Notulen Tracker?
              </button>
            </h2>
            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                Notulen Tracker adalah aplikasi berbasis web yang digunakan untuk mencatat, menyimpan, dan mengelola hasil rapat (notulen) secara digital agar mudah diakses kapan saja.
              </div>
            </div>
          </div>

          <div class="accordion-item mt-2">
            <h2 class="accordion-header" id="headingTwo">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                Apa fungsi fitur "Filter" pada daftar notulen?
              </button>
            </h2>
            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                Fitur filter berfungsi untuk mempermudah pencarian data notulen berdasarkan judul, tanggal, PIC, atau status rapat.
              </div>
            </div>
          </div>

          <div class="accordion-item mt-2">
            <h2 class="accordion-header" id="headingThree">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                Bagaimana cara menambahkan notulen rapat baru?
              </button>
            </h2>
            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                Anda dapat menambahkan notulen baru melalui tombol <b>“+ Tambah Notulen”</b> pada halaman daftar notulen, lalu isi detail rapat sesuai form yang tersedia.
              </div>
            </div>
          </div>

          <div class="accordion-item mt-2">
            <h2 class="accordion-header" id="headingFour">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                Bagaimana cara mengedit notulen yang sudah disimpan?
              </button>
            </h2>
            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                Buka halaman Daftar Notulen, pilih notulen yang ingin diedit, lalu klik tombol "Edit" untuk memperbarui data.
              </div>
            </div>
          </div>

          <div class="accordion-item mt-2">
            <h2 class="accordion-header" id="headingFive">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                Bagaimana cara mengekspor notulen menjadi format PDF?
              </button>
            </h2>
            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                Dari halaman hasil rapat, pilih opsi "Download PDF". File akan otomatis terdownload dan tersimapan pada local storage.
            </div>
          </div>
        </div>
      </div>
    </main>

    <footer>
      ©2025 Notulen Tracker. Semua hak cipta dilindungi
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // Fungsi logout menggunakan konfirmasi sebelum diarahkan ke login.php
    document.getElementById("logoutLink").addEventListener("click", (e) => {
        // Mencegah navigasi default
        e.preventDefault(); 

        const konfirmasi = confirm("Apakah Anda yakin ingin keluar dari Notulen Tracker?");
        if (konfirmasi) {
            // Arahkan ke login.php jika dikonfirmasi
            window.location.href = "login.php";
        }
    });
    </script>

    <?php
    // Tutup koneksi database
    if (isset($conn)) {
        mysqli_close($conn);
    }
    ?>
</body>
</html>