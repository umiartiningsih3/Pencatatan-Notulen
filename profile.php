<?php
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
$user_id = 1; // ID pengguna yang sedang login (HARUS DINAMIS!)

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// 2. CEK KONEKSI
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// 3. AMBIL DATA DARI TABEL NOTULIS (Gunakan Prepared Statement untuk keamanan)
$query = "SELECT nama_lengkap, email, divisi, peran, bergabung_sejak, foto_profile FROM notulis WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$profile_data = [
    'nama_lengkap' => 'Data Tidak Ditemukan',
    'email' => 'data.tidak@ditemukan.com',
    'divisi' => 'N/A',
    'peran' => 'N/A',
    'bergabung_sejak' => 'N/A',
    'foto_profile' => 'user.png' // Default image path
];

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    // Formatting Tanggal ke Bahasa Indonesia
    if ($row['bergabung_sejak'] && $row['bergabung_sejak'] !== '0000-00-00') {
        $timestamp = strtotime($row['bergabung_sejak']);
        $bulan_indonesia = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $row['bergabung_sejak'] = date('d', $timestamp) . ' ' . $bulan_indonesia[date('n', $timestamp)] . ' ' . date('Y', $timestamp);
    }

    $profile_data = $row;

    // Pastikan foto_profile tidak kosong
    if (empty($profile_data['foto_profile'])) {
        $profile_data['foto_profile'] = 'user.png';
    }
}

mysqli_close($conn);

$dropdown_email = htmlspecialchars($profile_data['email']);
$dropdown_nama = htmlspecialchars($profile_data['nama_lengkap']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil | Notulen Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Body, Navbar, Konten, Footer, Croppie Styling */
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

        /* Penyesuaian untuk Dropdown Menu (Untuk menampilkan foto) */
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
            margin-top: -3px; /* Jarak antara nama dan email */
        }

        /* Konten */
        main {
            flex: 1;
        }

        .profile-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-width: 500px;
            margin: 100px auto 60px auto;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .profile-pic-container {
            position: relative;
            display: inline-block;
        }

        .profile-card img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #007bff;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .profile-card img:hover {
            transform: scale(1.05);
        }

        .edit-icon {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            padding: 5px;
            font-size: 12px;
            cursor: pointer;
        }

        .profile-card input[type="file"] {
            display: none;
        }

        .profile-info {
            text-align: left;
            margin-top: 20px;
        }

        .profile-info label {
            font-weight: 600;
            margin-top: 10px;
        }

        .profile-info input {
            width: 100%;
            border: none;
            background: transparent;
            border-bottom: 1px solid #ccc;
            padding: 5px;
            font-size: 15px;
            outline: none;
            transition: 0.3s;
        }

        .profile-info input:disabled {
            color: #333;
        }

        .btn-edit {
            background-color: #003366;
            color: white;
            border-radius: 6px;
            border: none;
        }

        .btn-edit:hover {
            background-color: #00264d;
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

        /* Styling area Croppie */
        #upload-crop-area {
            width: 100%;
            height: 350px;
            margin: auto;
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
        <a class="nav-link " href="dashboard.php">
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
        <a class="nav-link" href="FAQ.php">
          <i class="bi bi-question-circle"></i>
          <span>FAQ</span>
        </a>
      </li>
                <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle active" href="#" id="userDropdown" data-bs-toggle="dropdown">
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

<div class="profile-card">
    <div class="profile-pic-container">
        <img id="fotoProfil" src="<?php echo htmlspecialchars($profile_data['foto_profile']); ?>" alt="Foto Profil" title="Klik untuk ganti foto">
        <span class="edit-icon">✎</span>
        <input type="file" id="uploadFoto" accept="image/*">
    </div>

    <div class="profile-info mt-4">
        <label for="nama">Nama Lengkap</label>
        <input type="text" id="nama" value="<?php echo htmlspecialchars($profile_data['nama_lengkap']); ?>" disabled>

        <label for="email">Email</label>
        <input type="email" id="email" value="<?php echo htmlspecialchars($profile_data['email']); ?>" disabled>

        <label for="divisi">Divisi</label>
        <input type="text" id="divisi" value="<?php echo htmlspecialchars($profile_data['divisi']); ?>" disabled>

        <label for="peran">Peran</label>
        <input type="text" id="peran" value="<?php echo htmlspecialchars($profile_data['peran']); ?>" disabled>

        <label for="bergabung">Bergabung Sejak</label>
        <input type="text" id="bergabung" value="<?php echo htmlspecialchars($profile_data['bergabung_sejak']); ?>" disabled>
    </div>

    <button id="btnEdit" class="btn btn-edit mt-4">Edit Profil</button>
    <a href="dashboard.php" class="btn btn-secondary mt-4">Kembali</a>
</div>

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
// ===== LOGIKA FRONTEND (EDIT/SIMPAN) & CROP =====
// ===========================================

const btnEdit = document.getElementById('btnEdit');
const inputs = document.querySelectorAll('.profile-info input');
let editMode = false;
// Gunakan ID pengguna yang diambil dari PHP
const userId = <?php echo json_encode($user_id); ?>;

// --- LOGIKA CROPPIE ---
let croppieInstance;
const cropModalElement = document.getElementById('cropModal');
const cropModal = new bootstrap.Modal(cropModalElement);
const cropArea = document.getElementById('upload-crop-area');
const cropButton = document.getElementById('cropButton');
const inputFile = document.getElementById('uploadFoto');
const fotoProfil = document.getElementById('fotoProfil');
const editIcon = document.querySelector('.edit-icon');

// Pemicu input file saat klik gambar atau ikon
if (fotoProfil) {
    fotoProfil.addEventListener('click', () => inputFile.click());
}
if (editIcon) {
    editIcon.addEventListener('click', () => inputFile.click());
}

if (inputFile) {
    inputFile.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;

        // **VALIDASI UKURAN FILE**
        const maxSize = 2 * 1024 * 1024; // Maksimum 2 MB
        if (file.size > maxSize) {
            alert('❌ Ukuran file terlalu besar. Maksimum 2MB.');
            this.value = ''; // Reset input file
            return;
        }

        const reader = new FileReader();

        reader.onload = function(e) {
            // Hancurkan instance lama jika ada
            if (croppieInstance) {
                croppieInstance.destroy();
            }

            // Inisialisasi Croppie
            croppieInstance = new Croppie(cropArea, {
                viewport: { width: 120, height: 120, type: 'circle' },
                boundary: { width: '100%', height: 350 }, // Ruang yang cukup untuk crop
                enableOrientation: true,
                enableExif: true,
            });

            // Bind image ke Croppie
            croppieInstance.bind({
                url: e.target.result
            });

            // Tampilkan modal
            cropModal.show();
        };

        reader.readAsDataURL(file);
    });
}

// Handler tombol 'Potong & Unggah' di dalam modal
if (cropButton) {
    cropButton.addEventListener('click', function() {
        if (!croppieInstance) return;

        // Mendapatkan hasil potongan (blob) dari Croppie
        // Output resolusi 400x400 untuk kualitas yang baik
        croppieInstance.result({
            type: 'blob',
            size: { width: 400, height: 400 },
            format: 'jpeg',
            quality: 0.9
        }).then(function(blob) {

            cropModal.hide();

            // Kirim Blob yang sudah dipotong ke server
            uploadCroppedPicture(blob);
        });
    });
}

// Fungsi untuk mengunggah Blob yang sudah dipotong (Dipanggil oleh cropButton)
function uploadCroppedPicture(blob) {
    const formData = new FormData();
    // Gunakan nama 'profile_picture' untuk menangkap file di PHP
    formData.append('profile_picture', blob, 'cropped_image.jpeg');
    formData.append('user_id', userId);

    console.log('Mengirim file foto yang sudah dipotong...');

    fetch('upload_profile_picture.php', { // AJAX ke file PHP terpisah
        method: 'POST',
        body: formData
    })
    .then(response => {
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
             return response.json();
        } else {
             return response.text().then(text => {
                console.error("Output server non-JSON:", text);
                throw new Error("Respon server bukan format JSON. Mungkin ada error PHP yang dicetak.");
             });
        }
    })
    .then(data => {
        if (data.status === 'success') {
            alert('✅ Foto profil berhasil dipotong dan diunggah!');
            window.location.reload();
        } else {
            alert('❌ Gagal mengunggah foto: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error unggah foto:', error);
        alert('❌ Terjadi kesalahan saat mengunggah foto: ' + error.message);
    });
}


// --- LOGIKA TOMBOL EDIT/SIMPAN DATA TEKS ---
if (btnEdit) {
    btnEdit.addEventListener('click', () => {
        if (editMode) {
            saveProfile(); // Menyimpan data teks
        } else {
            toggleEditMode(true);
        }
    });
}

// Fungsi untuk Mengubah Mode Tampilan/Edit
function toggleEditMode(isEditing) {
    editMode = isEditing;
    inputs.forEach(input => {
        // Email dan Bergabung tidak diubah
        if (input.id !== 'email' && input.id !== 'bergabung') {
            input.disabled = !editMode;
        }
    });

    if (editMode) {
        btnEdit.textContent = 'Simpan Perubahan';
        btnEdit.classList.remove('btn-edit');
        btnEdit.classList.add('btn-success');
    } else {
        btnEdit.textContent = 'Edit Profil';
        btnEdit.classList.remove('btn-success');
        btnEdit.classList.add('btn-edit');
    }
}

// Fungsi Simpan Profil ke Server (Hanya Data Teks)
function saveProfile() {
    const formData = {
        id: userId, // ID pengguna yang akan di-update
        nama: document.getElementById('nama').value,
        divisi: document.getElementById('divisi').value,
        peran: document.getElementById('peran').value
    };

    btnEdit.disabled = true;
    btnEdit.textContent = 'Menyimpan...';

    fetch('update_profile.php', { // AJAX ke file PHP terpisah
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
             return response.json();
        } else {
             return response.text().then(text => {
                console.error("Output server non-JSON:", text);
                throw new Error("Respon server bukan format JSON. Mungkin ada error PHP yang dicetak.");
             });
        }
    })
    .then(data => {
        btnEdit.disabled = false;

        if (data.status === 'success') {
            alert('✅ Perubahan profil berhasil disimpan!');
            toggleEditMode(false);
            window.location.reload();
        } else {
            alert('❌ Gagal menyimpan perubahan: ' + data.message);
            toggleEditMode(true);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btnEdit.disabled = false;
        btnEdit.textContent = 'Simpan Perubahan';

        alert('❌ Terjadi kesalahan koneksi atau server saat menyimpan data teks: ' + error.message);
        toggleEditMode(true);
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