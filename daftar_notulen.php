<?php
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "notulen_db";

// 🚨 GANTI DENGAN $_SESSION['user_id'] ASLI
$user_id = 1; 

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    // Jika koneksi gagal, hentikan eksekusi dan tampilkan pesan error
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil Data Profil
$query_profile = "SELECT nama_lengkap, email, foto_profile FROM notulis WHERE id = ?";
$stmt = mysqli_prepare($conn, $query_profile);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result_profile = mysqli_stmt_get_result($stmt);

// Data default jika user_id tidak ditemukan
$profile_data = [
    'nama_lengkap' => 'Notulis Tamu',
    'email' => 'tamu@notulen.com',
    'foto_profile' => 'user.png' // Default image path
];

if ($row = mysqli_fetch_assoc($result_profile)) {
    // Timpa data default dengan data dari database
    $profile_data['nama_lengkap'] = $row['nama_lengkap'];
    $profile_data['email'] = $row['email'];
    if (!empty($row['foto_profile'])) {
        $profile_data['foto_profile'] = $row['foto_profile'];
    }
}

// Definisikan variabel untuk Dropdown
$dropdown_email = htmlspecialchars($profile_data['email']);
$dropdown_nama = htmlspecialchars($profile_data['nama_lengkap']);

// ---------------------------------------------------------
// 2. BLOK KODE PENGAMBILAN DATA RAPAT (KODE ASLI ANDA)
// ---------------------------------------------------------

// PERHATIAN: Variabel $conn dari blok 1 digunakan di sini. 
// Koneksi TIDAK ditutup di blok 1 karena masih dibutuhkan.

$query = mysqli_query($conn, "SELECT * FROM rapat ORDER BY tanggal DESC");

// Variabel $conn sekarang akan ditutup di akhir loop atau di footer.
// Dalam konteks ini, kita biarkan terbuka sampai akhir file.

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notulen Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
      /* Styles yang sudah ada tetap dipertahankan */
      html,body {
        height: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
        background-color: #f5f7fa;
        padding-top: 80px;
      }
      body { 
        font-family: Poppins,system-ui,-apple-system,Segoe UI,Roboto;
        background-color: #f5f7fa; 
        margin: 0; 
        padding: 0; 
      }
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
      .btn-edit { 
        background-color: #2e7d32; 
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
      .table-hasil th, .table-hasil td { 
        vertical-align: middle; 
        text-align: center; 
      }
      footer {
        background-color: #003366;
        color: white;
        text-align: center;
        padding: 15px 0;
        font-size: 0.9rem;
        margin-top: auto;
      }
      /* Penyesuaian untuk Dropdown Menu (Untuk menampilkan foto) */
      .dropdown-menu .user-info-header {
        display: flex; /* <-- PENTING: Membuat item sejajar (foto dan teks) */
        align-items: center;
        padding: 10px 15px;
      }
      .dropdown-menu .user-avatar {
        width: 40px; /* Ukuran Avatar */
        height: 40px;
        border-radius: 50%; /* Membuat gambar menjadi lingkaran */
        object-fit: cover;
        margin-right: 10px;
      }
      .dropdown-menu .user-text small {
        display: block;
        margin-top: -3px; /* Jarak antara nama dan email */
      }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark px-4">
      <a class="navbar-brand" href="#">
        <img src="logono.jpeg" alt="Logo Notulen Tracker" width="50" class="me-2 rounded-circle">
        Notulen Tracker
      </a>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link active" href="daftar_notulen.php">Daftar Notulen</a></li>
          <li class="nav-item"><a class="nav-link" href="kontak.php">Kontak</a></li>
          <li class="nav-item"><a class="nav-link" href="FAQ.php
          ">FAQ</a></li>
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

    <div class="full-container mt-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-primary fw-bold mb-0">📘 DAFTAR NOTULEN RAPAT</h4>
            <div class="d-flex gap-2">
              <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filterPanel">
                <i class="bi bi-funnel"></i> Filter
              </button>
              <a href="input_rapat.php"><button class="btn-tambah-notulen">+ Tambah Notulen</button></a>
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
                  <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Terapkan Filter</button>
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
                
                <?php while($r = mysqli_fetch_assoc($query)){ 
                  $detail = mysqli_query($conn,"SELECT * FROM rapat_detail WHERE id_rapat='".$r['id']."'");
                  $hasil = [];
                  while($d = mysqli_fetch_assoc($detail)){
                    $hasil[] = [$d['topik'],$d['pembahasan'],$d['tindak_lanjut'],$d['pic']];
                  }
                  ?>
                  
                  <tr data-id="<?= $r['id'] ?>" data-detail='<?= json_encode([
                    "judul"=>$r["judul"],
                    "tanggal"=>$r["tanggal"],
                    "waktu"=>$r["waktu"],
                    "tempat"=>$r["tempat"],
                    "penyelenggara"=>$r["penyelenggara"],
                    "notulis"=>$r["notulis"],
                    "peserta"=>explode(",",$r["peserta"]),
                    "pembahasan"=>$hasil,
                    "catatan"=>explode("\n",$r["catatan"]),
                    "status"=>$r["status"]
                    ]) ?>'>
                    
                    <td><?= $r['judul']?></td>
                    <td><?= $r['tanggal']?></td>
                    <td><?= $r['notulis']?></td>
                    <td class="text-center">
                      <?= $r['status']=="Selesai" ? "<span class='status-selesai'>Selesai</span>" : "<span class='status-belum'>Belum Selesai</span>" ?>
                    </td>
                    <td class="text-center">
                      <button class="btn btn-lihat btn-sm">Lihat</button>
                      <a href="hapus_rapat.php?id=<?= $r['id']; ?>" 
                          onclick="return confirm('Yakin ingin menghapus notulen ini?')"
                          class="btn btn-danger btn-sm ms-1">
                          Hapus
                        </a>
                      </td>
                    </tr>
                    <?php } ?>
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
            <h5 class="modal-title fw-semibold">Detail Hasil Rapat</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body" id="detailContent"></div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header modal-edit-header">
            <h5 class="modal-title">Edit Notulen Rapat</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form id="editForm">
              <input type="hidden" id="editId" name="id"> 
              
              <div class="mb-3"><label class="form-label">Judul Rapat</label>
              <input type="text" class="form-control" id="editJudul" name="judul"></div>
              <div class="mb-3"><label class="form-label">Tanggal Rapat</label>
              <input type="date" class="form-control" id="editTanggal" name="tanggal"></div>
              <div class="mb-3"><label class="form-label">Notulis</label>
              <input type="text" class="form-control" id="editNotulis" name="notulis"></div>
              
              <div class="mb-3"><label class="form-label">Catatan</label>
              <textarea class="form-control" id="editCatatan" name="catatan" rows="3"></textarea></div>
              
              <div class="mb-3"><label class="form-label">Status</label>
                <select class="form-select" id="editStatus" name="status">
                  <option>Belum Selesai</option>
                  <option>Selesai</option>
                </select>
              </div>
              <button type="submit" class="btn btn-success">Simpan Perubahan</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // Filter data (tetap sama)
      const form = document.getElementById("filterForm");
      const resetBtn = document.getElementById("resetFilter");
      const table = document.getElementById("notulenTable").getElementsByTagName("tbody")[0];
      form.addEventListener("submit", e => {
        e.preventDefault();
        const judul = document.getElementById("filterJudul").value.toLowerCase();
        const tanggal = document.getElementById("filterTanggal").value;
        const notulis = document.getElementById("filterNotulis").value.toLowerCase();
        const status = document.getElementById("filterStatus").value;
        for (let row of table.rows) {
          const judulText = row.cells[0].textContent.toLowerCase();
          const tanggalText = row.cells[1].textContent.trim();
          const notulisText = row.cells[2].textContent.toLowerCase();
          const statusText = row.cells[3].textContent.trim();
          let visible = true;
          if (judul && !judulText.includes(judul)) visible = false;
          if (tanggal && tanggalText !== tanggal) visible = false;
          if (notulis && !notulisText.includes(notulis)) visible = false;
          if (status !== "Semua" && statusText !==status) visible = false;
          row.style.display = visible ? "" : "none";
        }
      });
      resetBtn.addEventListener("click", () => {
        form.reset();
        for (let row of table.rows) row.style.display = "";
      });

      // Detail rapat
      document.querySelectorAll(".btn-lihat").forEach(btn => {
        btn.addEventListener("click", e => {
          const row = e.target.closest("tr");
          const rapatId = row.dataset.id;
          const data = JSON.parse(row.dataset.detail || '{}');
          if (!data.judul) return alert("Tidak ada detail untuk baris ini.");
          
          const content = `
            <div id="rapatDetail">
              <h4 class="text-center text-primary fw-bold mb-3">HASIL RAPAT</h4>
              <p><span class="detail-label">Judul Rapat :</span> ${data.judul}</p>
              <p><span class="detail-label">Tanggal Rapat :</span> ${data.tanggal}</p>
              <p><span class="detail-label">Waktu Rapat :</span> ${data.waktu}</p>
              <p><span class="detail-label">Tempat Rapat :</span> ${data.tempat}</p>
              <p><span class="detail-label">Penyelenggara :</span> ${data.penyelenggara}</p>
              <p><span class="detail-label">Notulis :</span> ${data.notulis}</p>
              <p><span class="detail-label">Peserta Rapat :</span><br>${data.peserta.map(p=>"- "+p).join("<br>")}</p>
              <hr>
              <h6 class="text-primary fw-bold mt-3">Hasil Rapat :</h6>
              <table class="table table-bordered table-hasil">
                <thead><tr><th>No</th><th>Topik</th><th>Pembahasan</th><th>Tindak Lanjut</th><th>PIC</th></tr></thead>
                <tbody>${data.pembahasan.map((p,i)=>`<tr><td>${i+1}</td><td>${p[0]}</td><td>${p[1]}</td><td>${p[2]}</td><td>${p[3]}</td></tr>`).join("")}</tbody>
              </table>
              <h6 class="text-primary fw-bold">Catatan Tambahan:</h6>
              <ul>${data.catatan.map(c=>`<li>${c}</li>`).join("")}</ul>
              <p class="mt-3"><span class="detail-label">Status Rapat :</span> ${data.status}</p>
            </div>
            <div class="text-end mt-4">
              <button class="btn btn-success me-2" id="btnEdit"><i class="bi bi-pencil-square"></i> Edit</button>
              <button class="btn btn-secondary me-2" id="btnShare"><i class="bi bi-share"></i> Bagikan</button>
              <button class="btn btn-dark" id="btnDownload"><i class="bi bi-file-earmark-pdf"></i> Simpan PDF</button>
            </div>`;
          document.getElementById("detailContent").innerHTML = content;
          new bootstrap.Modal(document.getElementById("detailModal")).show();

          document.getElementById("btnDownload").addEventListener("click", () => {
            html2pdf().from(document.getElementById("rapatDetail")).set({
              margin: 0.5,
              filename: `${data.judul.replace(/\s+/g,'_')}.pdf`,
              image: { type: 'jpeg', quality: 0.98 },
              html2canvas: { scale: 2 },
              jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
            }).save();
          });

          document.getElementById("btnShare").addEventListener("click", () => {
            const currentUrl = window.location.href;
              if (navigator.share) {
              navigator.share({
              title: "Notulen Rapat",
              text: "Lihat notulen rapat lengkap di link berikut:",
              url: currentUrl
            }).catch(err => console.error("Gagal membagikan:", err));
            } else {
              // Fallback
              alert('Fungsi Share tidak didukung di browser ini.');
            }
          });
          document.getElementById("btnEdit").addEventListener("click", () => {
            // 1. Dapatkan instance modal detail yang sedang terbuka
            const detailModalInstance = bootstrap.Modal.getInstance(document.getElementById("detailModal"));
            
            // 2. Sembunyikan Modal Detail
            detailModalInstance.hide(); 

            // 3. Isi data ke Modal Edit
            document.getElementById("editId").value = rapatId; 
            document.getElementById("editJudul").value = data.judul;
            document.getElementById("editTanggal").value = data.tanggal;
            document.getElementById("editNotulis").value = data.notulis;
            
            // PENTING: Menggabungkan array catatan menjadi string dengan \n
            document.getElementById("editCatatan").value = data.catatan.join("\n"); 
            
            document.getElementById("editStatus").value = data.status;
            
            // 4. Tampilkan Modal Edit
            // Tambahkan jeda sebentar (timeout) agar transisi penutupan modal detail selesai
            // Ini mencegah masalah tumpang tindih backdrop atau scrolling body
            setTimeout(() => {
                new bootstrap.Modal(document.getElementById("editModal")).show();
            }, 100); // Jeda 100ms sudah cukup
          });
        });
      });

      // LOGIKA SIMPAN PERUBAHAN KE DATABASE DENGAN AJAX
      document.getElementById("editForm").addEventListener("submit", e => {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        // Kirim data ke edit_rapat.php
        fetch('edit_rapat.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === "success") {
                alert("✅ " + result.message);
                
                // Tutup modal dan muat ulang halaman untuk menampilkan perubahan
                bootstrap.Modal.getInstance(document.getElementById("editModal")).hide();
                window.location.reload(); 
            } else {
                alert("❌ Gagal: " + result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("❌ Terjadi kesalahan koneksi atau server.");
        });
      });
      // =======================================================
    </script>
    
    <br>
    <footer>
      ©2025 Notulen Tracker. Semua hak cipta dilindungi
    </footer>

    <script>
      // Fungsi logout
      document.getElementById("logoutLink").addEventListener("click", (e) => {
        e.preventDefault();
        const konfirmasi = confirm("Apakah Anda yakin ingin keluar dari Notulen Tracker?");
        if (konfirmasi) {
          window.location.href = "login.php";
        }
      });
    </script>
    <script>
      // === Kirim data tabel ke localStorage ===
      function updateDashboardData() {
        const rows = document.querySelectorAll("#notulenTable tbody tr");
        let selesai = 0;
        let belum = 0;

        rows.forEach(row => {
          const statusText = row.cells[3].innerText.trim();
          if (statusText === "Selesai") selesai++;
          else if (statusText === "Belum Selesai") belum++;
        });

        const data = {
          selesai,
          belum,
          total: selesai + belum
        };

        // Simpan ke localStorage
        localStorage.setItem("notulenStats", JSON.stringify(data));
        console.log("✅ Data dikirim ke Dashboard:", data);
      }

      // Jalankan setiap kali halaman dibuka
      updateDashboardData();
      document.getElementById("filterForm").addEventListener("submit", updateDashboardData);
      document.getElementById("resetFilter").addEventListener("click", updateDashboardData);
    </script>

    <?php
    // Tutup koneksi database setelah semua query selesai
    if (isset($conn)) {
        mysqli_close($conn);
    }
    ?>
</body>
</html>