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

  $role_display  = !empty($profile_db['role']) ? $profile_db['role'] : 'Peserta';
  $role_check    = strtolower($role_display);
  $nama_lengkap_user = $profile_db['nama_lengkap'];
  $dropdown_nama  = htmlspecialchars($nama_lengkap_user);
  $dropdown_email = htmlspecialchars($profile_db['email']);
  $dropdown_foto  = (!empty($profile_db['foto_profile']) && file_exists($profile_db['foto_profile'])) 
                    ? htmlspecialchars($profile_db['foto_profile']) 
                    : 'user.png';
  
  if ($role_check === 'notulis' || $role_check === 'admin') {
      $sql_query = "SELECT * FROM rapat ORDER BY id DESC";
  } else {
      $sql_query = "SELECT * FROM rapat WHERE peserta LIKE '%$nama_lengkap_user%' ORDER BY id DESC";
  }

  $query = mysqli_query($conn, $sql_query);

  if (!$query) {
      die("Query Error: " . mysqli_error($conn));
  }

  $total_notulen = mysqli_num_rows($query);

  $result_notulen = $query; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Notulen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
      html, body {
        height: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
        padding-top: 50px; 
      }
      
      body { 
        font-family: Poppins, system-ui, -apple-system, Segoe UI, Roboto;
        background: url('gambarr.png') no-repeat center center fixed !important;
        background-size: cover !important;
        position: relative;
        z-index: 0;
      }
      
      body::before {
        content: "";
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background-color: rgba(245, 247, 250, 0.85); 
        z-index: -1;
      }
      
      .card { 
        border-radius: 10px; 
        background: rgba(255, 255, 255, 0.9) !important;
        backdrop-filter: blur(5px);
      }
      
      .full-container { 
        width: 100%; 
        padding: 0 30px; 
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
      
      .card { 
        border-radius: 10px; 
      }

      .table thead th { 
        background-color: #003366;
        color: white; 
        text-align: center; 
        font-weight: 600; 
      }

      .status-belum { 
        background-color: #fbc02d; 
        color: #000; 
        padding: 4px 10px; 
        border-radius: 5px; 
        font-size: 0.85rem; 
      }

      .status-selesai { 
        background-color: #2e7d32; 
        color: white; 
        padding: 4px 10px; 
        border-radius: 5px; 
        font-size: 0.85rem; 
      }

      .btn-lihat { 
        background-color: #1565c0; 
        color: white;
      }

      .btn-download-pdf {
        background-color: #c44822ff;
        color: white;
      }

      .btn-tambah-notulen { 
        background-color: #003366;
        color: white; 
        font-weight: 500; 
        border-radius: 6px; 
        border: none; 
        padding: 8px 16px; 
      }

      .modal-edit-header {
        background-color: #003366 !important;
        color: white !important;
      }

      .btn-tambah-notulen:hover { 
        background-color: #303f9f;
      }

      .full-container { 
        width: 100%; 
        padding: 0 30px; 
      }

      .table tbody tr:hover { 
        background-color: #f1f3f6; 
      }

      .modal-header { 
        background-color: #003366; 
        color: white; 
      }

      .detail-label { 
        font-weight: 600; 
        color: #003366;
      }

      .table-hasil th {
        background-color: #003366 !important;
        color: white !important;
      }

      .table-hasil th, .table-hasil td { 
        vertical-align: middle; 
        text-align: center; 
        border: 1px solid #ddd;
      }

      footer {
        background-color: #003366;
        color: white;
        text-align: center;
        padding: 15px 0;
        font-size: 0.9rem;
        margin-top: auto;
      }
      
      .table-actions {
        display: grid;
        grid-template-columns: repeat(3, 65px); 
        gap: 5px;
        justify-content: center;
      }

      .table-actions .btn, .table-actions a {
        width: 59px; 
        text-align: center;
        padding: 5px 0;
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
    
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto nav-effect">
        <li class="nav-item">
          <a class="nav-link" href="dashboard.php">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
          </a>
        </li>
        
        <li class="nav-item">
          <a class="nav-link active" href="daftar_notulen.php">
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
  
  <div class="full-container">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h4 class="text-primary fw-bold mb-0">📘 DAFTAR NOTULEN</h4>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filterPanel">
              <i class="bi bi-funnel"></i> Pencarian
            </button>
            <?php if ($role_check === 'notulis'): ?>
              <a href="input_rapat.php"><button class="btn-tambah-notulen">+ Tambah Notulen</button></a>
              <?php endif; ?>
            </div>
          </div>
          
          <div class="collapse mb-3" id="filterPanel">
            <div class="card card-body">
              <form id="filterForm" class="row g-2">
                <div class="col-md-4"><input type="text" id="filterJudul" class="form-control" placeholder="Cari judul..."></div>
                <div class="col-md-3"><input type="date" id="filterTanggal" class="form-control"></div>
                <div class="col-md-3"><input type="text" id="filterNotulis" class="form-control" placeholder="Cari Notulis..."></div>
                <div class="col-md-2">
                  <select id="filterStatus" class="form-select">
                    <option value="Semua" selected>Semua</option>
                    <option value="Belum Selesai">Belum Selesai</option>
                    <option value="Selesai">Selesai</option>
                  </select>
                </div>
                
                <div class="col-12 text-end">
                  <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Cari Data</button>
                  <button type="button" id="resetFilter" class="btn btn-secondary btn-sm ms-1"><i class="bi bi-arrow-repeat"></i> Muat Ulang</button>
                </div>
              </form>
            </div>
          </div>
          
          <div class="table-responsive">
            <table class="table table-bordered align-middle" id="notulenTable">
              <thead>
                <tr>
                  <th>Judul Rapat</th>
                  <th>Tanggal</th>
                  <th>Notulis</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              
              <tbody>
                <?php if (mysqli_num_rows($query) > 0): ?>
                <?php while($r = mysqli_fetch_assoc($query)): 
                $detail = mysqli_query($conn,"SELECT * FROM rapat_detail WHERE id_rapat='".$r['id']."'");
                $hasil = [];
                while($d = mysqli_fetch_assoc($detail)){
                  $hasil[] = [$d['topik'],$d['pembahasan'],$d['tindak_lanjut'],$d['pic']];
                }
                
                $json_detail = json_encode([
                  "judul"         => $r["judul"],
                  "tanggal"       => $r["tanggal"],
                  "waktu"         => $r["waktu"],
                  "tempat"        => $r["tempat"],
                  "penyelenggara" => $r["penyelenggara"],
                  "notulis"       => $r["notulis"],
                  "peserta"       => explode(",", $r["peserta"]),
                  "pembahasan"    => $hasil,
                  "catatan"       => explode("\n", $r["catatan"]),
                  "status"        => $r["status"]
                ]);
                ?>
                
                <tr data-id="<?= $r['id'] ?>" data-detail='<?= htmlspecialchars($json_detail, ENT_QUOTES, 'UTF-8') ?>'>
                  <td><?= htmlspecialchars($r['judul']) ?></td>
                  <td><?= date('d-m-Y', strtotime($r['tanggal'])) ?></td>
                  <td><?= htmlspecialchars($r['notulis']) ?></td>
                  <td class="text-center"><?= $r['status'] == "Selesai" ? "<span class='status-selesai'>Selesai</span>" : "<span class='status-belum'>Belum Selesai</span>" ?>
                </td>
                <td class="text-center">
                  <div class="table-actions">
                    <button class="btn btn-lihat btn-sm btn-info text-white">Lihat</button>
                    <button class="btn btn-download-pdf btn-sm btn-secondary" 
                    data-rapat-id="<?= $r['id'] ?>" 
                    data-rapat-judul="<?= htmlspecialchars($r['judul']) ?>">
                    Unduh
                  </button>
                  
                  <?php 
                  if ($role_check === 'admin' || ($role_check === 'notulis' && $r['notulis'] === $nama_lengkap_user)): 
                  ?>
                  <a href="hapus_rapat.php?id=<?= $r['id']; ?>" 
                  onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" 
                  class="btn btn-danger btn-sm">Hapus</a>
                  <?php endif; ?> 
                </div>
              </td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="text-center text-muted py-4">
                  Tidak ada daftar rapat yang tersedia.
                </td>
              </tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  
  <div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content shadow-lg">
        <div class="modal-header">
          <h5 class="modal-title fw-semibold">Detail Notulen</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="detailContent"></div> 
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <div id="detailActionArea"></div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header modal-edit-header">
          <h5 class="modal-title fw-bold">Edit Notulen Rapat</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="editForm" action="update_rapat.php" method="POST">
            <input type="hidden" id="editId" name="id"> 
            
            <div class="mb-3">
              <label class="form-label fw-bold">Judul Rapat</label>
              <input type="text" class="form-control" id="editJudul" name="judul" required>
            </div>
            
            <div class="mb-3">
              <label class="form-label fw-bold">Tanggal Rapat</label>
              <input type="date" class="form-control" id="editTanggal" name="tanggal" required>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Waktu Rapat</label>
                <input type="time" class="form-control" id="editWaktu" name="waktu">
              </div>

            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">Tempat Rapat</label>
              <input type="text" class="form-control" id="editTempat" name="tempat">
            </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Penyelenggara Rapat</label>
              <input type="text" class="form-control" id="editPenyelenggara" name="penyelenggara">
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Notulis</label>
              <input type="text" class="form-control bg-light" id="editNotulis" name="notulis" readonly>
            </div>
          
            <div class="mb-3">
              <label class="form-label fw-bold">Catatan</label>
              <textarea class="form-control" id="editCatatan" name="catatan" rows="3"></textarea>
            </div>
          
            <div class="mb-3">
              <label class="form-label fw-bold">Status</label>
              <select class="form-select" id="editStatus" name="status">
                <option value="Belum Selesai">Belum Selesai</option>
                <option value="Selesai">Selesai</option>
              </select>
            </div>
            <button type="submit" class="btn btn-success w-100">Simpan Perubahan</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <footer>©2025 Notulen Tracker. Semua hak cipta dilindungi</footer>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const userRole = <?= json_encode(strtolower($role_display)); ?>;
    const userName = "<?= $nama_lengkap_user ?>"; 
    const form = document.getElementById("filterForm");
    const resetBtn = document.getElementById("resetFilter");
    const table = document.getElementById("notulenTable").getElementsByTagName("tbody")[0];

    form.addEventListener("submit", e => {
        e.preventDefault();
        const judul = document.getElementById("filterJudul").value.toLowerCase();
        const tanggal = document.getElementById("filterTanggal").value;
        const notulis = document.getElementById("filterNotulis").value.toLowerCase();
        const status = document.getElementById("filterStatus").value;
        
        let visibleCount = 0; 
        const initialEmptyRow = document.getElementById("initialEmptyRow");
        const noDataRow = document.getElementById("noFilterResultRow");
        
        if (initialEmptyRow) initialEmptyRow.style.display = "none";
        
        for (let row of table.rows) {
            if (row.id === 'noFilterResultRow' || row.id === 'initialEmptyRow') continue; 
            if (row.cells.length < 5) continue; 
            
            const judulText = row.cells[0].textContent.toLowerCase();
            const tanggalText = row.cells[1].textContent.trim();
            const notulisText = row.cells[2].textContent.toLowerCase();
            const statusText = row.cells[3].textContent.trim();
            
            let visible = true;
            if (judul && !judulText.includes(judul)) visible = false;
            if (tanggal && tanggalText !== tanggal) visible = false;
            if (notulis && !notulisText.includes(notulis)) visible = false;
            if (status !== "Semua" && statusText !== status) visible = false;
            
            row.style.display = visible ? "" : "none";
            if (visible) visibleCount++;
        }
        
        if (noDataRow) noDataRow.style.display = visibleCount === 0 ? "" : "none";
    });

    resetBtn.addEventListener("click", () => {
        form.reset();
        const initialEmptyRow = document.getElementById("initialEmptyRow");
        const noDataRow = document.getElementById("noFilterResultRow");
        if (noDataRow) noDataRow.style.display = "none";
        
        let hasDataRows = false;
        for (let row of table.rows) {
            if (row.id === 'noFilterResultRow' || row.id === 'initialEmptyRow') continue;
            if (row.cells.length === 5) {
                row.style.display = ""; 
                hasDataRows = true;
            }
        }
        if (!hasDataRows && initialEmptyRow) initialEmptyRow.style.display = "";
    });

    document.querySelectorAll(".btn-lihat").forEach(btn => {
        btn.addEventListener("click", e => {
            const row = e.target.closest("tr");
            const rapatId = row.dataset.id;
            const data = JSON.parse(row.dataset.detail || '{}'); 
            
            if (!data.judul) return alert("Tidak ada detail untuk baris ini.");
            
            const isOwner = (data.notulis === userName);
            const canEdit = (userRole === "admin" || (userRole === "notulis" && isOwner));

            let actionButtons = "";
            if (canEdit || userRole === "notulis") {
                actionButtons = `<div class="text-end mb-3" id="modalTopActions">`;
                if (canEdit) {
                    actionButtons += `<button class="btn btn-success btn-sm me-2" id="btnEdit" data-rapat-id="${rapatId}"><i class="bi bi-pencil-square"></i> Edit</button>`;
                }
                actionButtons += `<button class="btn btn-secondary btn-sm" id="btnShare" data-rapat-judul="${data.judul}"><i class="bi bi-share"></i> Bagikan</button></div>`;
            }

            const content = `
            <div id="printableArea" style="font-family: Poppins, sans-serif; font-size: 11pt; padding: 10px;">
                ${actionButtons}
                <h3 style="text-align: center; color: #003366; font-weight: 700; border-bottom: 3px solid #003366; padding-bottom: 10px; margin-bottom: 20px;">HASIL RAPAT</h3>
                <table style="width: 100%; margin-bottom: 20px;">
                    <tr><td style="width: 30%; font-weight: 600; color: #003366;">Judul Rapat</td><td style="width: 5%;">:</td><td>${data.judul}</td></tr>
                    <tr><td style="font-weight: 600; color: #003366;">Tanggal/Waktu</td><td>:</td><td>${data.tanggal} / ${data.waktu}</td></tr>
                    <tr><td style="font-weight: 600; color: #003366;">Tempat</td><td>:</td><td>${data.tempat}</td></tr>
                    <tr><td style="font-weight: 600; color: #003366;">Penyelenggara</td><td>:</td><td>${data.penyelenggara}</td></tr>
                    <tr><td style="font-weight: 600; color: #003366;">Notulis</td><td>:</td><td>${data.notulis}</td></tr>
                </table>
                <p style="font-weight: 600; color: #003366; margin-top: 15px;">Peserta Rapat:</p>
                <ul style="padding-left: 20px;">
                    ${data.peserta.map(p=>`<li style="margin-bottom: 5px;">${p}</li>`).join("")}
                </ul>
                <h4 style="color: #003366; margin-top: 30px; font-weight: 600; border-bottom: 1px dashed #ccc; padding-bottom: 5px;">Detail Pembahasan:</h4>
                <table class="table table-bordered table-sm" style="width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 10pt;">
                    <thead>
                        <tr style="background-color: #003366; color: white;">
                            <th style="padding: 10px; text-align: center; width: 5%;">No</th>
                            <th style="padding: 10px; width: 20%;">Topik</th>
                            <th style="padding: 10px; width: 35%;">Pembahasan</th>
                            <th style="padding: 10px; width: 25%;">Tindak Lanjut</th>
                            <th style="padding: 10px; width: 15%;">PIC</th>
                        </tr>
                    </thead>
                    <tbody>${data.pembahasan.map((p,i)=>`
                        <tr>
                            <td style="padding: 8px; text-align: center; background-color: ${i % 2 === 0 ? '#ffffff' : '#f4f4f4'}; border: 1px solid #ddd;">${i+1}</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">${p[0]}</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">${p[1]}</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">${p[2]}</td>
                            <td style="padding: 8px; text-align: center; border: 1px solid #ddd;">${p[3]}</td>
                        </tr>`).join("")}
                    </tbody>
                </table>
                <h4 style="color: #003366; margin-top: 30px; font-weight: 600; border-bottom: 1px dashed #ccc; padding-bottom: 5px;">Catatan Tambahan:</h4>
                <ul style="list-style-type: disc; padding-left: 20px;">
                    ${data.catatan.map(c=>`<li style="margin-bottom: 5px;">${c}</li>`).join("")}
                </ul>
                <p style="margin-top: 30px; font-weight: 600;">Status Rapat: 
                    <span style="background-color: ${data.status === 'Selesai' ? '#2e7d32' : '#fbc02d'}; color: ${data.status === 'Selesai' ? 'white' : 'black'}; padding: 4px 10px; border-radius: 5px; font-size: 0.9em; font-weight: normal;">
                        ${data.status}
                    </span>
                </p>
            </div>`;
            
            document.getElementById("detailContent").innerHTML = content;
            
            const btnEditModal = document.getElementById("btnEdit");
            if(btnEditModal) {
                btnEditModal.addEventListener("click", () => {
                    const detailModalInstance = bootstrap.Modal.getInstance(document.getElementById("detailModal"));
                    detailModalInstance.hide();
                    document.getElementById("editId").value = rapatId; 
                    document.getElementById("editJudul").value = data.judul;
                    document.getElementById("editTanggal").value = data.tanggal;
                    document.getElementById("editWaktu").value = data.waktu;
                    document.getElementById("editTempat").value = data.tempat;
                    document.getElementById("editPenyelenggara").value = data.penyelenggara;
                    document.getElementById("editNotulis").value = data.notulis;
                    document.getElementById("editCatatan").value = data.catatan.join("\n"); 
                    document.getElementById("editStatus").value = data.status;

                    setTimeout(() => {
                        new bootstrap.Modal(document.getElementById("editModal")).show();
                    }, 300);
                });
            }

            const btnShareModal = document.getElementById("btnShare");
            if(btnShareModal) {
                btnShareModal.addEventListener("click", () => {
                    if (navigator.share) {
                        navigator.share({ title: "Notulen: " + data.judul, url: window.location.href });
                    } else {
                        alert('Share tidak didukung browser ini.');
                    }
                });
            }

            new bootstrap.Modal(document.getElementById("detailModal")).show();
        });
    });

    document.querySelectorAll(".btn-download-pdf").forEach(btn => {
        btn.addEventListener("click", async (e) => {
          const rapatJudul = e.target.dataset.rapatJudul;
          const row = e.target.closest("tr");
          const data = JSON.parse(row.dataset.detail || '{}');

          if (!data.judul) {
            return alert("Gagal mengambil data notulen untuk dibuat PDF.");
          }

          const pdfContentHtml = `
            <div style="padding: 25px; font-family: Poppins, sans-serif; font-size: 11pt;">
              <h3 style="text-align: center; color: #003366; font-weight: 700; border-bottom: 3px solid #003366; padding-bottom: 10px; margin-bottom: 20px;">
                HASIL RAPAT
              </h3>
              
              <table style="width: 100%; margin-bottom: 20px;">
                  <tr><td style="width: 25%; font-weight: 600; color: #003366;">Judul Rapat</td><td style="width: 5%;">:</td><td>${data.judul}</td></tr>
                  <tr><td style="font-weight: 600; color: #003366;">Tanggal/Waktu</td><td>:</td><td>${data.tanggal} / ${data.waktu}</td></tr>
                  <tr><td style="font-weight: 600; color: #003366;">Tempat</td><td>:</td><td>${data.tempat}</td></tr>
                  <tr><td style="font-weight: 600; color: #003366;">Penyelenggara</td><td>:</td><td>${data.penyelenggara}</td></tr>
                  <tr><td style="font-weight: 600; color: #003366;">Notulis</td><td>:</td><td>${data.notulis}</td></tr>
              </table>

              <p style="font-weight: 600; color: #003366; margin-top: 15px;">Peserta Rapat:</p>
              <ul style="margin-left: -20px; padding-left: 20px;">
                ${data.peserta.map(p=>`<li style="margin-bottom: 5px;">${p}</li>`).join("")}
              </ul>
              
              <h4 style="color: #003366; margin-top: 30px; font-weight: 600; border-bottom: 1px dashed #ccc; padding-bottom: 5px;">Detail Pembahasan:</h4>
              <table style="width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 10pt;">
                <thead>
                  <tr style="background-color: #003366; color: white;">
                    <th style="border: 1px solid #003366; padding: 10px; text-align: center; width: 5%;">No</th>
                    <th style="border: 1px solid #003366; padding: 10px; width: 20%;">Topik</th>
                    <th style="border: 1px solid #003366; padding: 10px; width: 35%;">Pembahasan</th>
                    <th style="border: 1px solid #003366; padding: 10px; width: 25%;">Tindak Lanjut</th>
                    <th style="border: 1px solid #003366; padding: 10px; width: 15%;">PIC</th>
                  </tr>
                </thead>
                <tbody>${data.pembahasan.map((p,i)=>`
                  <tr>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center; background-color: ${i % 2 === 0 ? '#ffffff' : '#f4f4f4'};">${i+1}</td>
                    <td style="border: 1px solid #ddd; padding: 8px; background-color: ${i % 2 === 0 ? '#ffffff' : '#f4f4f4'};">${p[0]}</td>
                    <td style="border: 1px solid #ddd; padding: 8px; background-color: ${i % 2 === 0 ? '#ffffff' : '#f4f4f4'};">${p[1]}</td>
                    <td style="border: 1px solid #ddd; padding: 8px; background-color: ${i % 2 === 0 ? '#ffffff' : '#f4f4f4'};">${p[2]}</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center; background-color: ${i % 2 === 0 ? '#ffffff' : '#f4f4f4'};">${p[3]}</td>
                  </tr>`).join("")}
                </tbody>
              </table>

              <h4 style="color: #003366; margin-top: 30px; font-weight: 600; border-bottom: 1px dashed #ccc; padding-bottom: 5px;">Catatan Tambahan:</h4>
              <ul style="list-style-type: disc; margin-left: -20px; padding-left: 20px;">
                ${data.catatan.map(c=>`<li style="margin-bottom: 5px;">${c}</li>`).join("")}
              </ul>
              
              <div style="text-align: right; margin-top: 50px; font-size: 0.8em; color: #888;">
                  Dokumen ini dibuat otomatis oleh Notulen Tracker.
              </div>
            </div>
          `;

          const filename = `${rapatJudul.replace(/\s+/g,'_')}_${data.tanggal}.pdf`;

          html2pdf().from(pdfContentHtml).set({
            margin: 0.5,
            filename: filename,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
          }).save();
        });
      });

    document.getElementById("editForm").addEventListener("submit", e => {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        fetch('edit_rapat.php', {
            method: 'POST',
            body: new URLSearchParams(Object.fromEntries(formData.entries()))
        })
        .then(res => res.json())
        .then(result => {
            if (result.status === "success") {
                alert("✅ " + result.message);
                window.location.reload(); 
            } else {
                alert("❌ Gagal: " + result.message);
            }
        });
    });

    function updateDashboardData() {
        const rows = document.querySelectorAll("#notulenTable tbody tr");
        let selesai = 0, belum = 0;
        rows.forEach(row => {
            if (row.cells.length === 5) {
                const status = row.cells[3].innerText.trim();
                if (status === "Selesai") selesai++;
                else if (status === "Belum Selesai") belum++;
            }
        });
        localStorage.setItem("notulenStats", JSON.stringify({ selesai, belum, total: selesai + belum }));
    }
    updateDashboardData();
    document.getElementById("logoutLink").addEventListener("click", (e) => {
        e.preventDefault();
        if (confirm("Keluar dari sistem?")) window.location.href = "login.php";
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('status') === 'success') {
        Swal.fire({
            title: "Berhasil Disimpan!",
            text: "Notulensi rapat telah masuk ke sistem.",
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Sip!"
        }).then(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    }
</script>
</body>
</html>