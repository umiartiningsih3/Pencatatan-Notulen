<?php
// profile.php (VERSI PERBAIKAN & PENYEMBUNYIAN IKON)
$activePage = basename($_SERVER['PHP_SELF']);
// TAMPILKAN ERROR UNTUK DEBUGGING. HAPUS BARIS INI JIKA SUDAH DI LINGKUNGAN PRODUKSI.
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ----------------------------------------------------
// 1. DETAIL KONEKSI DATABASE (UBAH SESUAI KONFIGURASI ANDA)
// ----------------------------------------------------
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "notulen_db";
// 🚨 PENTING: GANTI DENGAN $_SESSION['user_id'] SETELAH IMPLEMENTASI LOGIN
$user_id = 1; 

// --- DUMMY DATA FOR DEMO ---
$profile_data = [
    'nama_lengkap' => 'Nafilah Thahirah Anwar',
    'email' => '3312511069.Nafilah@students.polibatam.ac.id',
    'divisi' => 'Teknik Informatika', 
    'peran' => 'Peserta', 
    'nik' => '3312511069',
    'no_hp' => '081277727475',
    'bergabung_sejak' => date('Y-m-d'), 
    'foto_profile' => 'user.png' 
];
// --- END DUMMY DATA ---

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// 3. AMBIL DATA DARI DB (Gunakan Prepared Statement untuk keamanan)
$query = "SELECT nama_lengkap, email, divisi, peran, nik, no_hp, bergabung_sejak, foto_profile FROM notulis WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        $row['unit'] = $row['divisi']; 
        $row['role'] = $row['peran']; 

        // Formatting Tanggal
        if ($row['bergabung_sejak'] && $row['bergabung_sejak'] !== '0000-00-00') {
            $timestamp = strtotime($row['bergabung_sejak']);
            $bulan_indonesia = [ 1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $row['bergabung_sejak'] = date('d', $timestamp) . ' ' . $bulan_indonesia[date('n', $timestamp)] . ' ' . date('Y', $timestamp);
        }

        $profile_data = array_merge($profile_data, $row); 
    }
    mysqli_close($conn);
}


$dropdown_email = htmlspecialchars($profile_data['email']);
$dropdown_nama = htmlspecialchars($profile_data['nama_lengkap']);
$dropdown_foto = htmlspecialchars($profile_data['foto_profile']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun | Notulen Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: Poppins, system-ui, -apple-system, Segoe UI, Roboto; background-color: #f5f7fa; display: flex; flex-direction: column; min-height: 100vh; padding-top: 80px; }
        .custom-navbar { background-color: #003366; height: 70px; box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
        .nav-effect { gap: 10px; }
        .nav-effect .nav-link { color: #dce3ea !important; padding: 10px 18px; border-radius: 12px; display: flex; align-items: center; gap: 10px; font-weight: 500; transition: all 0.3s ease; position: relative; }
        .navbar-nav .nav-link:hover { background: rgba(255,255,255,0.08); color: #ffffff !important; }
        .navbar-nav .nav-link.active { background: rgba(255,255,255,0.15); color: #ffffff !important; font-weight: 600; }
        .nav-effect .nav-link i { font-size: 1.1rem; transition: transform 0.3s ease; }
        .nav-effect .nav-link:hover i { transform: scale(1.15); }
        .brand-pro { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .brand-pro img { width: 50px; height: 50px; border-radius: 100px; padding: 0px; background: linear-gradient(135deg, #ffffff, #e3f2fd); transition: all 0.35s ease; }
        .brand-info { display: flex; flex-direction: column; line-height: 1.1; }
        .brand-name { font-size: 21px; font-weight: 700; color: #ffffff; letter-spacing: 0.3px; }
        .brand-tagline { font-size: 13px; color: #90caf9; letter-spacing: 1px; }
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
      
      /* Style untuk dropdown item dengan ikon */
      .dropdown-menu .dropdown-item {
        display: flex;
        align-items: center;
        padding: 5px 15px; 
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
        main { flex: 1; }
        footer { background-color: #003366; color: white; text-align: center; padding: 15px 0; font-size: 0.9rem; margin-top: auto; }
        #upload-crop-area { width: 100%; height: 350px; margin: auto; }

        /* ===== STYLE PROFILE LAYOUT (SIDEBAR & CARDS) ===== */
        .page-header {
            font-weight: 700;
            color: #333;
            margin-bottom: 30px;
        }

        /* Sidebar Kiri (Pengaturan Akun) */
        .profile-sidebar {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            text-align: center;
            height: fit-content;
        }
        .profile-sidebar img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #0d6efd;
            margin-bottom: 15px;
            /* Tambahkan efek hover agar terlihat bisa diklik */
            transition: opacity 0.3s; 
        }
        .profile-sidebar img:hover {
            opacity: 0.8; /* Sedikit buram saat di hover */
        }

        .profile-sidebar h5 {
            font-weight: 600;
        }
        .profile-sidebar p {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .profile-sidebar .info-row {
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #eee;
            padding-top: 10px;
            margin-top: 10px;
            font-size: 0.9rem;
        }
        .profile-sidebar .info-row span:first-child {
            font-weight: 600;
            color: #333;
        }
        .badge-peserta {
            background-color: #e3f2fd;
            color: #0d6efd;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 8px;
            margin-top: 10px;
            display: inline-block;
        }
        
        /* ICON KAMERA DIHAPUS, JADI KODE INI TIDAK RELEVAN LAGI */
        /*.pic-edit-icon { ... }*/ 

        /* Card Data Diri / Keamanan */
        .form-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        .form-card h5 {
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        .form-card .form-control {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px 15px;
        }
        .form-card .form-label {
            font-weight: 500;
            margin-bottom: 5px;
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
                    <a class="nav-link " href="dashboard.php"><i class="bi bi-grid"></i><span>Dashboard</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="daftar_notulen.php"><i class="bi bi-file-text"></i><span>Daftar Notulen</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="kontak.php"><i class="bi bi-envelope"></i><span>Kontak</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="FAQ.php"><i class="bi bi-question-circle"></i><span>FAQ</span></a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo ($activePage == 'profile.php') ? 'active' : ''; ?>" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Notulis
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                        <li class="user-info-header">
                            <img
                                src="<?php echo $dropdown_foto; ?>"
                                id="navbarAvatar"
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
    <main class="container py-5">
        <h2 class="page-header">Pengaturan Akun</h2>

        <div class="row">
            <div class="col-md-4">
                <div class="profile-sidebar">
                    <div class="position-relative d-inline-block">
                        <img id="fotoProfil" src="<?php echo htmlspecialchars($profile_data['foto_profile']); ?>" alt="Foto Profil" title="Klik untuk ganti foto">
                        
                        <input type="file" id="uploadFoto" accept="image/*" style="display: none;"> 
                    </div>
                    
                    <h5 class="mb-0 mt-2"><?php echo htmlspecialchars($profile_data['nama_lengkap']); ?></h5>
                    <p class="mb-2"><?php echo htmlspecialchars($profile_data['email']); ?></p>
                    
                    <span class="badge-peserta"><?php echo htmlspecialchars($profile_data['peran']); ?></span>

                    <div class="info-row">
                        <span>Prodi</span>
                        <span><?php echo htmlspecialchars($profile_data['divisi']); ?></span>
                    </div>
                    <div class="info-row" style="border-bottom: 1px solid #eee; padding-bottom: 10px;">
                        <span>Bergabung</span>
                        <span><?php echo htmlspecialchars($profile_data['bergabung_sejak']); ?></span>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="form-card">
                    <h5 class="text-primary"><i class="bi bi-person me-2"></i>Data Diri</h5>
                    <form id="dataDiriForm" class="row g-3">
                        <div class="col-md-6">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" value="<?php echo htmlspecialchars($profile_data['nama_lengkap']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($profile_data['email']); ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label for="divisi" class="form-label">Prodi</label>
                            <input type="text" class="form-control" id="divisi" value="<?php echo htmlspecialchars($profile_data['divisi']); ?>" required>
                        </div>
                        <div class="col-12 text-end mt-4">
                            <button type="submit" id="btnSimpanData" class="btn btn-primary"><i class="bi bi-save me-2"></i>Simpan Perubahan</button>
                        </div>
                    </form>
                </div>

                <div class="form-card">
                    <h5 class="text-dark"><i class="bi bi-lock me-2"></i>Keamanan / Password</h5>
                    <form id="passwordForm" class="row g-3">
                        <div class="col-12">
                            <label for="passwordLama" class="form-label">Password Saat Ini</label>
                            <input type="password" class="form-control" id="passwordLama" placeholder="Masukkan password lama" required>
                        </div>
                        <div class="col-md-6">
                            <label for="passwordBaru" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="passwordBaru" placeholder="Minimal 4 karakter" minlength="4" required>
                        </div>
                        <div class="col-md-6">
                            <label for="konfirmasiPassword" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="konfirmasiPassword" placeholder="Ulangi password baru" required>
                        </div>
                        <div class="col-12 text-end mt-4">
                            <button type="submit" id="btnGantiPassword" class="btn btn-dark"><i class="bi bi-key me-2"></i>Ganti Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <div class="modal fade" id="cropModal" tabindex="-1" aria-labelledby="cropModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cropModalLabel">Sesuaikan Foto Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="upload-crop-area"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="cropButton" class="btn btn-primary">Potong & Unggah</button>
                </div>
            </div>
        </div>
    </div>
    <footer>
        ©2025 Notulen Tracker. Semua hak cipta dilindungi
    </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js"></script>

<script>
// ===========================================
// ===== LOGIKA FRONTEND JS (DATA DIRI & PASSWORD) =====
// ===========================================

const userId = <?php echo json_encode($user_id); ?>;

// --- LOGIKA FORM DATA DIRI ---
document.getElementById('dataDiriForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSimpanData');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
    
    const formData = {
        id: userId, 
        nama: document.getElementById('nama').value,
        no_hp: document.getElementById('no_hp').value,
        divisi: document.getElementById('divisi').value,
    };

    fetch('update_profile.php', { 
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('✅ Data diri berhasil diperbarui: ' + data.message);
            window.location.reload();
        } else {
            alert('❌ Gagal memperbarui data diri: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Terjadi kesalahan koneksi saat menyimpan data diri.');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-save me-2"></i>Simpan Perubahan';
    });
});


// --- LOGIKA FORM GANTI PASSWORD ---
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const passwordLama = document.getElementById('passwordLama').value;
    const passwordBaru = document.getElementById('passwordBaru').value;
    const konfirmasiPassword = document.getElementById('konfirmasiPassword').value;
    const btn = document.getElementById('btnGantiPassword');

    if (passwordBaru.length < 4) {
        alert('❌ Password baru minimal harus 4 karakter.');
        return;
    }

    if (passwordBaru !== konfirmasiPassword) {
        alert('❌ Konfirmasi password tidak cocok dengan password baru.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
    
    const formData = {
        id: userId,
        password_lama: passwordLama,
        password_baru: passwordBaru
    };

    fetch('update_password.php', { 
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('✅ Password berhasil diganti!');
            // Reset form
            document.getElementById('passwordForm').reset();
        } else {
            alert('❌ Gagal ganti password: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Terjadi kesalahan koneksi saat mengganti password.');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-key me-2"></i>Ganti Password';
    });
});


// --- LOGIKA CROPPIE (Perubahan di sini: Mengganti editIcon dengan fotoProfil) ---
let croppieInstance;
const cropModalElement = document.getElementById('cropModal');
const cropModal = new bootstrap.Modal(cropModalElement);
const cropArea = document.getElementById('upload-crop-area');
const cropButton = document.getElementById('cropButton');
const inputFile = document.getElementById('uploadFoto');
const fotoProfil = document.getElementById('fotoProfil'); // Mengambil elemen foto

// Pemicu input file saat klik foto profil
if (fotoProfil) {
    fotoProfil.addEventListener('click', () => inputFile.click()); // KLIK FOTO MEMICU INPUT FILE
}

if (inputFile) {
    inputFile.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;

        const maxSize = 2 * 1024 * 1024; // Maksimum 2 MB
        if (file.size > maxSize) {
            alert('❌ Ukuran file terlalu besar. Maksimum 2MB.');
            this.value = ''; 
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            if (croppieInstance) { croppieInstance.destroy(); }
            croppieInstance = new Croppie(cropArea, {
                viewport: { width: 120, height: 120, type: 'circle' },
                boundary: { width: '100%', height: 350 },
                enableOrientation: true,
                enableExif: true,
            });
            croppieInstance.bind({ url: e.target.result });
            cropModal.show();
        };
        reader.readAsDataURL(file);
    });
}

// Handler tombol 'Potong & Unggah'
if (cropButton) {
    cropButton.addEventListener('click', function() {
        if (!croppieInstance) return;
        croppieInstance.result({
            type: 'blob',
            size: { width: 400, height: 400 },
            format: 'jpeg',
            quality: 0.9
        }).then(function(blob) {
            cropModal.hide();
            uploadCroppedPicture(blob);
        });
    });
}

// Fungsi untuk mengunggah Blob yang sudah dipotong
function uploadCroppedPicture(blob) {
    const formData = new FormData();
    formData.append('profile_picture', blob, 'cropped_image.jpeg');
    formData.append('user_id', userId);

    fetch('upload_profile_picture.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('✅ Foto profil berhasil diunggah!');
            // Tambahkan parameter unik agar browser tidak mengambil gambar dari cache
            window.location.href = window.location.pathname + "?t=" + new Date().getTime();
        } else {
            alert('❌ Gagal mengunggah foto: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error unggah foto:', error);
        alert('❌ Terjadi kesalahan saat mengunggah foto.');
    });
}


// --- FUNGSI LOGOUT ---
document.getElementById("logoutLink").addEventListener("click", (e) => {
    e.preventDefault();
    const konfirmasi = confirm("Apakah Anda yakin ingin keluar dari Notulen Tracker?");
    if (konfirmasi) {
        window.location.href = "login.php"; // Ganti dengan skrip logout Anda
    }
});
</script>
</body>
</html>