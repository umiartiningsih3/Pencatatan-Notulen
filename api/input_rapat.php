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

if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Rapat</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
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

        .custom-navbar { 
            background-color: #003366; 
            height: 70px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.12); 
        }
        
        .nav-effect { gap: 10px; }

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

        #containerPeserta .badge {
            padding: 8px 12px;
            border-radius: 50px;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }

        #containerPeserta .badge:hover {
            transform: translateY(-2px);
        }

        .italic { font-style: italic; }

        footer {
            background-color: #003366;
            color: white;
            text-align: center;
            padding: 15px 0;
            font-size: 0.9rem;
            margin-top: auto;
        }

        @media (max-width: 991.98px) {
            .custom-navbar { height: auto; padding: 10px 15px; }
            body { padding-top: 100px; }
            .navbar-collapse { background: #003366; padding: 15px; border-radius: 10px; margin-top: 10px; }
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
                <li class="nav-item"><a class="nav-link active" href="daftar_notulen.php"><i class="bi bi-file-text"></i><span>Daftar Notulen</span></a></li>
                <li class="nav-item"><a class="nav-link" href="kontak.php"><i class="bi bi-envelope"></i><span>Kontak</span></a></li>
                <li class="nav-item"><a class="nav-link" href="faq.php"><i class="bi bi-question-circle"></i><span>FAQ</span></a></li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i> <?php echo ucwords(htmlspecialchars($role_display)); ?>
                    </a>    
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li class="user-info-header">
                            <img src="<?= $dropdown_foto; ?>" alt="Avatar" class="user-avatar">
                            <div class="user-text">
                                <strong class="text-truncate"><?= $dropdown_nama; ?></strong>
                                <small class="text-muted text-truncate"><?= $dropdown_email; ?></small>
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

    <div class="container mt-5 mb-5">
        <div class="card shadow p-4">
            <h4 class="text-center mb-4 text-primary fw-bold">Formulir Hasil Rapat</h4>
            <form id="formRapat" method="POST" action="input_hasil.php">

                <div class="mb-3">
                    <label for="judulRapat" class="form-label fw-bold">Judul Rapat</label>
                    <input type="text" class="form-control" id="judulRapat" placeholder="Masukkan judul rapat" name="judul" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggalRapat" class="form-label fw-bold">Tanggal Rapat</label>
                        <input type="date" class="form-control" id="tanggalRapat" name="tanggal" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="waktuRapat" class="form-label fw-bold">Waktu Rapat</label>
                        <input type="time" class="form-control" id="waktuRapat" name="waktu" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tempatRapat" class="form-label fw-bold">Tempat Rapat</label>
                        <input type="text" class="form-control" id="tempatRapat" placeholder="Masukkan tempat rapat" name="tempat">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="penyelenggara" class="form-label fw-bold">Penyelenggara Rapat</label>
                        <input type="text" class="form-control" id="penyelenggara" placeholder="Masukkan penyelenggara rapat" name="penyelenggara">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Notulis</label>
                    <input type="text" class="form-control bg-light" name="notulis" value="<?= $dropdown_nama; ?>" readonly>
                </div>

                <div class="mb-3 position-relative">
                    <label class="form-label fw-bold">Daftar Peserta</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-people text-muted"></i>
                        </span>
                        <input type="text" id="inputNama" class="form-control border-start-0 ps-0" placeholder="Ketik nama atau cari peserta..." autocomplete="off">
                        <button class="btn btn-primary px-3" type="button" id="btnTambahPeserta" title="Tambah Peserta">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                    <div id="hasilPencarian" class="list-group position-absolute w-100 shadow" style="z-index: 1000;"></div>
                    
                    <div id="containerPeserta" class="mt-2 d-flex flex-wrap gap-2 p-3 border rounded bg-light-subtle" style="min-height: 60px; border-style: dashed !important;">
                        <span class="text-muted small italic"><i class="bi bi-info-circle me-1"></i>Belum ada peserta yang ditambahkan...</span>
                    </div>
                    <input type="hidden" name="daftar_peserta" id="hiddenPeserta">
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Hasil Rapat</label>
                    <table class="table table-bordered align-middle" id="tabelHasil">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Topik</th>
                                <th>Pembahasan</th>
                                <th>Tindak Lanjut</th>
                                <th>PIC</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td><textarea name="topik[]" placeholder="Masukkan Topik"></textarea></td>
                                <td><textarea name="pembahasan[]" placeholder="Masukkan Pembahasan"></textarea></td>
                                <td><textarea name="tindak_lanjut[]" placeholder="Masukkan Tindak lanjut"></textarea></td>
                                <td><textarea name="pic[]" placeholder="Masukkan PIC"></textarea></td>
                                <td><button type="button" class="btn btn-danger btn-sm" onclick="hapusBaris(this)">Hapus</button></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="tambahBaris">+ Tambah Baris</button>
                </div>

                <div class="mb-3">
                    <label for="catatan" class="form-label fw-bold">Catatan Tambahan</label>
                    <textarea class="form-control" id="catatan" rows="3" placeholder="Masukkan catatan tambahan di sini..." name="catatan"></textarea>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label fw-bold">Status Rapat</label>
                    <select class="form-select" id="status" name="status">
                        <option value="Belum Selesai">Belum Selesai</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success px-4">Simpan</button>
                    <button type="button" class="btn btn-secondary px-4" id="btnBatal">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <footer>©2025 Notulen Tracker. Semua hak cipta dilindungi</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const inputPeserta = document.getElementById('inputNama'); 
    const btnTambah = document.getElementById('btnTambahPeserta');
    const containerBadge = document.getElementById('containerPeserta'); 
    const hiddenInputPeserta = document.getElementById('hiddenPeserta');
    const hasilPencarian = document.getElementById('hasilPencarian');
    let daftarPeserta = [];

    inputPeserta.addEventListener('keyup', function() {
        let keyword = this.value;
        if (keyword.length > 0) {
            fetch('cari_peserta.php?key=' + keyword)
                .then(res => res.text())
                .then(data => {
                    hasilPencarian.innerHTML = data;
                });
        } else {
            hasilPencarian.innerHTML = '';
        }
    });

    function pilihPeserta(nama) {
        inputPeserta.value = nama;
        hasilPencarian.innerHTML = '';
        btnTambah.click();
    }

    document.addEventListener('click', function(e) {
        if (e.target !== inputPeserta) {
            hasilPencarian.innerHTML = '';
        }
    });

    btnTambah.addEventListener('click', function() {
        const nama = inputPeserta.value.trim();
        
        if (nama === "") return;
        if (daftarPeserta.includes(nama)) {
            alert("Nama sudah ada di daftar!");
            return;
        }

        daftarPeserta.push(nama);
        renderBadges();
        inputPeserta.value = ""; 
    });

    function renderBadges() {
        containerBadge.innerHTML = ""; 
        if (daftarPeserta.length === 0) {
            containerBadge.innerHTML = '<span class="text-muted small italic"><i class="bi bi-info-circle me-1"></i>Belum ada peserta yang ditambahkan...</span>';
        }

        daftarPeserta.forEach((nama, index) => {
            const badge = document.createElement('span');
            badge.className = "badge bg-primary d-flex align-items-center";
            badge.innerHTML = `
                ${nama} 
                <i class="bi bi-x ms-2" style="cursor:pointer; font-size: 1.1rem" onclick="hapusPeserta(${index})"></i>
            `;
            containerBadge.appendChild(badge);
        });
        hiddenInputPeserta.value = daftarPeserta.join(', ');
    }

    window.hapusPeserta = function(index) {
        daftarPeserta.splice(index, 1);
        renderBadges();
    };

    document.getElementById("tambahBaris").addEventListener("click", () => {
        const tbody = document.querySelector("#tabelHasil tbody");
        const rowCount = tbody.rows.length + 1;
        const newRow = `
            <tr>
                <td>${rowCount}</td>
                <td><textarea name="topik[]" placeholder="Topik"></textarea></td>
                <td><textarea name="pembahasan[]" placeholder="Pembahasan"></textarea></td>
                <td><textarea name="tindak_lanjut[]" placeholder="Tindak lanjut"></textarea></td>
                <td><textarea name="pic[]" placeholder="PIC"></textarea></td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="hapusBaris(this)">Hapus</button></td>
            </tr>`;
        tbody.insertAdjacentHTML("beforeend", newRow);
    });

    window.hapusBaris = function(btn) {
        btn.closest("tr").remove();
        const rows = document.querySelectorAll("#tabelHasil tbody tr");
        rows.forEach((r, i) => r.cells[0].textContent = i + 1);
    };

    document.addEventListener("input", function (e) {
        if (e.target.tagName.toLowerCase() === "textarea") {
            e.target.style.height = "auto";
            e.target.style.height = e.target.scrollHeight + "px";
        }
    });

    document.getElementById("formRapat").addEventListener("submit", function(e) {
        let error = [];
        if (daftarPeserta.length === 0) {
            error.push("Minimal harus ada satu peserta");
        }
        if (error.length > 0) {
            e.preventDefault();
            alert("⚠️ ERROR:\n\n" + error.join("\n"));
        }
    });

    document.getElementById("logoutLink")?.addEventListener("click", (e) => {
        e.preventDefault();
        if (confirm("Apakah Anda yakin ingin keluar?")) window.location.href = "login.php";
    });

    document.getElementById("btnBatal")?.addEventListener("click", () => {
        if (confirm("Apakah Anda yakin ingin membatalkan?")) window.location.href = "daftar_notulen.php";
    });
</script>
</body>
</html>