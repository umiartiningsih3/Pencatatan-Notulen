<?php
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
    'foto_profile' => 'user.png' // Pastikan ada gambar default di folder Anda
];

if ($row = mysqli_fetch_assoc($result_profile)) {
    $profile_data['nama_lengkap'] = $row['nama_lengkap'];
    $profile_data['email'] = $row['email'];
    if (!empty($row['foto_profile'])) {
        $profile_data['foto_profile'] = $row['foto_profile'];
    }
}

// Variabel untuk digunakan di HTML
$dropdown_email = htmlspecialchars($profile_data['email']);
$dropdown_nama = htmlspecialchars($profile_data['nama_lengkap']);

// Tutup statement
// Cek jika $stmt sudah didefinisikan sebelum ditutup
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

      /* Navbar */
      .navbar {
        background-color: #003366;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1030;
      }

      .navbar-brand {
        color: white;
        font-weight: 600;
      }

      .navbar-nav .nav-link {
        color: #cfd8dc !important;
        margin-right: 15px;
      }

      .navbar-nav .nav-link.active {
        color: #fff !important;
        font-weight: 600;
      }

     .navbar .dropdown-toggle {
        background-color: rgba(128, 128, 128, 0.3) !important;
        color: white !important;
        border-radius: 6px;
        padding: 6px 14px;
        font-weight: 500;
        transition: all 0.3s ease;
      }

      .navbar .dropdown-toggle:hover,
      .navbar .dropdown-toggle:focus {
        color: #fff !important;
        transform: scale(1.05);
      }
      
      /* Dropdown User Info Styles (BARU DITAMBAHKAN) */
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

    <nav class="navbar navbar-expand-lg navbar-dark px-4">
      <a class="navbar-brand" href="#">
          <img src="logono.jpeg" alt="Logo Notulen Tracker" width="50" class="me-2 rounded-circle">
          Notulen Tracker
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="daftar_notulen.php">Daftar Notulen</a></li>
          <li class="nav-item"><a class="nav-link" href="kontak.php">Kontak</a></li>
          <li class="nav-item"><a class="nav-link active" href="FAQ.php">FAQ</a></li>
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
            <li><a id="logoutLink" class="dropdown-item text-danger" href="login.php">Keluar</a></li>
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
        // Jika link sudah diarahkan ke login.php di HTML, kita perlu mencegah default
        // agar konfirmasi bisa muncul.
        if (e.target.href.includes("login.php")) {
            e.preventDefault(); 
        }

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