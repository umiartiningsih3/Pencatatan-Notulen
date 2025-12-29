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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Notulen Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { 
            display: flex; 
            flex-direction: column; 
            min-height: 100vh; 
            font-family: Poppins,system-ui,-apple-system,Segoe UI,Roboto; 
            margin: 0; 
            padding-top: 80px; 
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
	
        .faq-title {
            color: #0d6efd;
            font-weight: 700;
            margin-bottom: 10px !important;
        }

        .category-title {
            color: #003366;
            font-weight: 700;
            margin-top: 20px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #003366;
            display: inline-block;
            font-size: 1.1rem;
        }

        .first-category {
            margin-top: 5px !important;
        }

        .accordion-button:not(.collapsed) {
            background-color: #003366;
            color: white;
        }
      
        .accordion-button.collapsed {
            background-color: rgba(255, 255, 255, 0.7);
            color: #003366;
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

        .brand-info { 
            display: flex; 
            flex-direction: column; 
            line-height: 1.1;
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

        .brand-pro:hover img {
            transform: scale(1.08) rotate(-4deg);
            box-shadow: 0 8px 25px rgba(144,202,249,0.45);
        }

        .nav-effect .nav-link.active i {
            color: #0d6efd;
        }

        .brand-name { 
            font-size: 21px; 
            font-weight: 700; 
            color: #ffffff; }
        .brand-tagline { font-size: 13px; color: #90caf9; }
        
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

        .card {
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
        }

        #tabelHasil textarea {
            width: 100%;
            border: none;
            resize: none;
            overflow: hidden;
            background-color: transparent;
            white-space: pre-wrap;
            word-wrap: break-word;
            outline: none;
            padding: 5px; 
        }

        #tabelHasil td {
            vertical-align: top;
            word-break: break-word;
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

    <main class="container my-4">
        <h2 class="text-center faq-title">Pertanyaan Umum (FAQ)</h2>

        <div class="accordion" id="faqAccordion">
            
            <h5 class="category-title first-category"><i class="bi bi-star-fill me-2"></i>Fitur Utama</h5>
            
            <div class="accordion-item shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        Apa itu Notulen Tracker?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Notulen Tracker adalah aplikasi berbasis web yang digunakan untuk mencatat, menyimpan, dan mengelola hasil rapat (notulen) secara digital agar mudah diakses kapan saja.</div>
                </div>
            </div>

            <div class="accordion-item mt-2 shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        Bagaimana cara menambahkan notulen rapat baru?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Anda dapat menambahkan notulen baru melalui tombol <b>“+ Tambah Notulen”</b> pada halaman daftar notulen, lalu isi detail rapat sesuai form yang tersedia.</div>
                </div>
            </div>

            <div class="accordion-item mt-2 shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                        Bagaimana cara mengedit notulen yang sudah disimpan?
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Buka halaman Daftar Notulen, pilih notulen yang ingin diedit, lalu klik tombol "Edit" untuk memperbarui data.</div>
                </div>
            </div>

            <div class="accordion-item mt-2 shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                        Bagaimana cara menghapus notulen yang salah?
                    </button>
                </h2>
                <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Anda dapat mengklik opsi "Hapus" pada daftar notulen. Pastikan kembali sebelum menghapus, karena data yang terhapus tidak dapat dikembalikan.</div>
                </div>
            </div>

            <h5 class="category-title"><i class="bi bi-folder-fill me-2"></i>Pengelolaan & Pencarian Data</h5>

            <div class="accordion-item shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        Apa fungsi fitur "Pencarian" pada daftar notulen?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Fitur pencarian berfungsi untuk mempermudah pencarian data notulen berdasarkan judul, tanggal, PIC, atau status rapat.</div>
                </div>
            </div>

            <div class="accordion-item mt-2 shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                        Bagaimana cara mengekspor notulen menjadi format PDF?
                    </button>
                </h2>
                <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Dari halaman hasil rapat, pilih opsi "Unduh". File akan otomatis terunduh dan tersimpan pada local storage.</div>
                </div>
            </div>

            <div class="accordion-item mt-2 shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                        Apakah ada batasan jumlah notulen yang bisa disimpan?
                    </button>
                </h2>
                <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Tidak ada batasan. Anda dapat menyimpan sebanyak mungkin notulen rapat selama kapasitas penyimpanan akun Anda masih mencukupi.</div>
                </div>
            </div>

            <h5 class="category-title"><i class="bi bi-shield-lock-fill me-2"></i>Keamanan & Akun</h5>

            <div class="accordion-item shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq8">
                        Bagaimana cara mengubah kata sandi akun saya?
                    </button>
                </h2>
                <div id="faq8" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Anda dapat mengubah kata sandi melalui menu "Profil Saya" > "Keamanan & Password". Masukkan kata sandi lama dan kata sandi baru Anda, lalu klik "Ganti Password".</div>
                </div>
            </div>

            <div class="accordion-item mt-2 shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq9">
                        Bagaimana jika saya lupa password saya?
                    </button>
                </h2>
                <div id="faq9" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Anda dapat menggunakan fitur pemulihan akun dengan melengkapi data validasi berupa NIM atau NIK, Nama Lengkap, Role, dan Tanggal Lahir. Setelah identitas tervalidasi, Anda dapat membuat kata sandi baru minimal 8 karakter dan menyimpannya untuk kembali masuk ke aplikasi.</div>
                </div>
            </div>

            <div class="accordion-item mt-2 shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq11">
                        Apakah saya wajib memperbarui data tanggal lahir di profil saya?
                    </button>
                </h2>
                <div id="faq11" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Ya, data tanggal lahir sangat penting sebagai komponen validasi wajib untuk memverifikasi kepemilikan akun Anda. Data ini digunakan oleh sistem saat Anda melakukan proses pemulihan akun jika lupa kata sandi.</div>
                </div>
            </div>

            <h5 class="category-title"><i class="bi bi-headset me-2"></i>Bantuan & Kontak</h5>

            <div class="accordion-item shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq10">
                        Bagaimana jika saya mengalami kendala atau memiliki pertanyaan terkait aplikasi?
                    </button>
                </h2>
                <div id="faq10" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Anda dapat mengakses menu "Kontak" di mana Nama Lengkap dan Email Anda akan terisi secara otomatis. Cukup tuliskan detail kendala Anda pada kolom Pesan dan klik tombol Kirim Pesan untuk mendapatkan bantuan teknis.</div>
                </div>
            </div>

        </div>
    </main>
    <br><br>

    <footer>©2025 Notulen Tracker. Semua hak cipta dilindungi</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById("logoutLink").addEventListener("click", (e) => {
        e.preventDefault(); 
        if (confirm("Apakah Anda yakin ingin keluar dari Notulen Tracker?")) {
            window.location.href = "login.php";
        }
    });
    </script>
</body>
</html>