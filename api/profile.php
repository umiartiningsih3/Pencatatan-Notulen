<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id']; 
$activePage = basename($_SERVER['PHP_SELF']);

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "notulen_db";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$query_profile = "SELECT * FROM pengguna WHERE id = ?";
$stmt = mysqli_prepare($conn, $query_profile);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$profile_data = mysqli_fetch_assoc($result);

if (!$profile_data) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$role_display = !empty($profile_data['role']) ? $profile_data['role'] : 'Notulis';
$dropdown_nama = htmlspecialchars($profile_data['nama_lengkap']);
$dropdown_email = htmlspecialchars($profile_data['email']);
$tgl_lahir = !empty($profile_data['tgl_lahir']) ? $profile_data['tgl_lahir'] : '';

$is_default_photo = empty($profile_data['foto_profile']) || !file_exists($profile_data['foto_profile']);
$foto_path = (!$is_default_photo) ? htmlspecialchars($profile_data['foto_profile']) : 'userr.png';

$bulan_indonesia = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'   
];

if (!empty($profile_data['bergabung_sejak']) && $profile_data['bergabung_sejak'] !== '0000-00-00') {
    $timestamp = strtotime($profile_data['bergabung_sejak']);
    $formatted_date = date('d', $timestamp) . ' ' . $bulan_indonesia[date('n', $timestamp)] . ' ' . date('Y', $timestamp);
} else {
    $formatted_date = "Baru Saja"; 
}

$tgl_lahir_display = "Belum diatur";
if (!empty($tgl_lahir) && $tgl_lahir !== '0000-00-00') {
    $ts_lahir = strtotime($tgl_lahir);
    $tgl_lahir_display = date('d', $ts_lahir) . ' ' . $bulan_indonesia[date('n', $ts_lahir)] . ' ' . date('Y', $ts_lahir);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            display: flex; 
            flex-direction: column; 
            min-height: 100vh; 
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

        .nav-effect {
            gap: 10px;
        }

        .custom-navbar { 
            background-color: #003366; 
            height: 70px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.12); 
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

        .profile-sidebar { 
            background: rgba(255,255,255,0.9); 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
            backdrop-filter: blur(5px); 
        }

        .profile-sidebar img.main-avatar { 
            width: 120px; 
            height: 120px; 
            border-radius: 50%; 
            object-fit: cover; 
            border: 4px solid #0d6efd; 
            transition: 0.3s; 
            cursor: pointer; 
            display: block; 
            margin: 0 auto; 
        }

        .profile-sidebar img.main-avatar:hover { opacity: 0.8; }
        
        .badge-peserta { 
            background-color: #e3f2fd; 
            color: #0d6efd; 
            font-weight: 600; 
            padding: 5px 15px; 
            border-radius: 8px; 
            margin: 10px 0; 
            display: inline-block; 
        }
        
        .form-card { 
            background: rgba(255,255,255,0.9); 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
            height: 100%; 
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
        #upload-crop-area { 
            width: 100%; 
            height: 300px; 
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
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-grid"></i><span>Dashboard</span></a></li>
            <li class="nav-item"><a class="nav-link" href="daftar_notulen.php"><i class="bi bi-file-text"></i><span>Daftar Notulen</span></a></li>
            <li class="nav-item"><a class="nav-link" href="kontak.php"><i class="bi bi-envelope"></i><span>Kontak</span></a></li>
            <li class="nav-item"><a class="nav-link" href="FAQ.php"><i class="bi bi-question-circle"></i><span>FAQ</span></a></li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle active" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i> <?php echo ucwords(htmlspecialchars($role_display)); ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li class="user-info-header">
                        <img src="<?php echo $foto_path; ?>" class="user-avatar" alt="User">
                        <div class="user-text">
                            <strong class="text-truncate"><?php echo $dropdown_nama; ?></strong>
                            <small class="text-truncate"><?php echo $dropdown_email; ?></small>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> Profil Saya</a></li>
                    <li><a class="dropdown-item text-danger" href="logout.php" onclick="return confirm('Keluar dari sistem?')"><i class="bi bi-box-arrow-right"></i> Keluar</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

    <main class="container py-4">
        <h2 class="mb-4 fw-bold text-primary">Pengaturan Akun</h2>
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="profile-sidebar">
                    <div class="text-center">
                        <div class="position-relative d-inline-block">
                            <img id="fotoProfil" class="main-avatar" src="<?php echo $foto_path; ?>" alt="Foto Profil" title="Klik untuk ganti foto">
                            <div class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                            style="width: 35px; height: 35px; border: 3px solid #fff; cursor: pointer;"
                            onclick="document.getElementById('uploadFoto').click();">
                            <i class="bi bi-camera-fill" style="font-size: 16px;"></i>
                        </div>
                    </div>
                    
                    <input type="file" id="uploadFoto" accept="image/*" style="display: none;">
                    <?php if (!$is_default_photo): ?>
                        <div class="mt-2">
                            <button type="button" id="btnHapusFoto" class="btn btn-sm btn-outline-danger border-0">
                                <i class="bi bi-trash me-1"></i>Hapus Foto
                            </button>
                        </div>
                        <?php endif; ?>
                        
                        <h5 class="mt-3 mb-0 fw-bold"><?php echo $dropdown_nama; ?></h5>
                        <div class="text-muted mb-2">
                            <i class="bi bi-calendar3 me-1"></i><?php echo $tgl_lahir_display; ?>
                        </div>
                        
                        <p class="text-muted mb-1"><?php echo $dropdown_email; ?></p>
                        <span class="badge-peserta"><?php echo htmlspecialchars($profile_data['role']); ?></span>
                        <hr>
                        <div class="d-flex justify-content-between small mb-4">
                            <span>Bergabung Sejak</span>
                            <strong><?php echo $formatted_date; ?></strong>
                        </div>
                    </div>

                    <h5 class="text-primary mb-3"><i class="bi bi-person-lines-fill me-2"></i>Biodata Pribadi</h5>
                    <form id="dataDiriForm">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control bg-light" id="nama" value="<?php echo $dropdown_nama; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="tgl_lahir" value="<?php echo $tgl_lahir; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat Email</label>
                            <input type="email" class="form-control bg-light" value="<?php echo $dropdown_email; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Program Studi</label>
                            <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($profile_data['prodi']); ?>" readonly>
                        </div>
                        <button type="submit" id="btnSimpanData" class="btn btn-primary w-100">Simpan Perubahan</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="form-card">
                    <h5 class="text-dark mb-4"><i class="bi bi-shield-lock me-2"></i>Keamanan & Privasi</h5>
                    <form id="passwordForm">
                        <div class="mb-3">
                            <label class="form-label">Kata Sandi Saat Ini</label>
                            <input type="password" id="passwordLama" name="password" class="form-control" placeholder="Masukkan kata sandi sekarang" id="passwordLama" required>
                        </div>
                        <div class="row mb-3 g-3">
                            <div class="col-md-6">
                                <label class="form-label">Kata Sandi Baru</label>
                                <input type="password" id="passwordBaru" name="password" class="form-control" placeholder="Masukkan kata sandi baru" id="passwordBaru" minlength="4" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ulangi Kata Sandi Baru</label>
                                <input type="password" id="konfirmasiPassword" name="confirm" class="form-control" placeholder="Ketik ulang kata sandi baru" id="konfirmasiPassword" required>
                            </div>
                        </div>
                        <div class="form-check small mt-2">
                            <input class="form-check-input " type="checkbox" id="lihatSandi"
                            onclick="
                            passwordLama.type = this.checked ? 'text' : 'password';
                            passwordBaru.type = this.checked ? 'text' : 'password';
                            konfirmasiPassword.type = this.checked ? 'text' : 'password';
                            ">
                            <label class="form-check-label" for="lihatSandi">
                                Lihat kata sandi
                            </label>
                        </div>
                        
                        <div class="alert alert-info small mt-2">
                            <i class="bi bi-info-circle me-2"></i>Gunakan minimal 4 karakter untuk keamanan password Anda.
                        </div>
                        <div class="text-end">
                            <button type="submit" id="btnGantiPassword" class="btn btn-dark px-4">Ganti Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="cropModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sesuaikan Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="upload-crop-area"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="cropButton" class="btn btn-primary">Simpan Foto</button>
                </div>
            </div>
        </div>
    </div>

    <footer>©2025 Notulen Tracker. Semua hak cipta dilindungi</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js"></script>

    <script>
    const userId = <?= json_encode($user_id); ?>;

    document.getElementById('dataDiriForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSimpanData');
        btn.disabled = true;

        fetch('update_profile.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                id: userId, 
                nama: document.getElementById('nama').value,
                tgl_lahir: document.getElementById('tgl_lahir').value 
            })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') location.reload();
        })
        .finally(() => btn.disabled = false);
    });

    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const passBaru = document.getElementById('passwordBaru').value;
        if (passBaru !== document.getElementById('konfirmasiPassword').value) {
            return alert('Konfirmasi password tidak cocok!');
        }

        fetch('update_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id: userId,
                password_lama: document.getElementById('passwordLama').value,
                password_baru: passBaru
            })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') document.getElementById('passwordForm').reset();
        });
    });

    let croppieInst;
    const cropModal = new bootstrap.Modal(document.getElementById('cropModal'));
    const inputFoto = document.getElementById('uploadFoto');
    
    document.getElementById('fotoProfil').onclick = () => inputFoto.click();

    inputFoto.onchange = function() {
        if (!this.files[0]) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            if (croppieInst) croppieInst.destroy();
            croppieInst = new Croppie(document.getElementById('upload-crop-area'), {
                viewport: { width: 180, height: 180, type: 'circle' },
                boundary: { width: 300, height: 300 },
                showZoomer: true
            });
            croppieInst.bind({ url: e.target.result });
            cropModal.show();
        }
        reader.readAsDataURL(this.files[0]);
    };

    document.getElementById('cropButton').onclick = function() {
        this.disabled = true;
        this.innerText = 'Memproses...';
        
        croppieInst.result({ type: 'blob', size: 'viewport', format: 'jpeg' }).then(blob => {
            const fd = new FormData();
            fd.append('profile_picture', blob, 'avatar.jpg');
            fd.append('user_id', userId);

            fetch('upload_profile_picture.php', { method: 'POST', body: fd })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') location.reload();
            })
            .finally(() => {
                this.disabled = false;
                this.innerText = 'Simpan Foto';
            });
        });
    };

    const btnHapusFoto = document.getElementById('btnHapusFoto');
    if (btnHapusFoto) {
        btnHapusFoto.onclick = function() {
            if (confirm('Apakah Anda yakin ingin menghapus foto profil?')) {
                const fd = new FormData();
                fd.append('action', 'delete_photo');
                fd.append('user_id', userId);

                fetch('upload_profile_picture.php', { method: 'POST', body: fd })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') location.reload();
                });
            }
        };
    }
    </script>
</body>
</html>