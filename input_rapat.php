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
// Asumsi nama tabel adalah 'notulis' dan memiliki kolom 'nama_lengkap', 'email', 'foto_profile', dan 'id'
$query_profile = "SELECT nama_lengkap, email, foto_profile FROM notulis WHERE id = ?";
$stmt = mysqli_prepare($conn, $query_profile);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result_profile = mysqli_stmt_get_result($stmt);
} else {
    // Penanganan error jika prepare statement gagal
    $result_profile = false;
}

// Data default jika user_id tidak ditemukan atau koneksi gagal
$profile_data = [
    'nama_lengkap' => 'Notulis Tamu',
    'email' => 'tamu@notulen.com',
    'foto_profile' => 'user.png' // Pastikan ada gambar default di folder Anda
];

if ($result_profile && $row = mysqli_fetch_assoc($result_profile)) {
    $profile_data['nama_lengkap'] = $row['nama_lengkap'];
    $profile_data['email'] = $row['email'];
    if (!empty($row['foto_profile'])) {
        $profile_data['foto_profile'] = $row['foto_profile'];
    }
}

// Variabel untuk digunakan di HTML
$dropdown_email = htmlspecialchars($profile_data['email']);
$dropdown_nama = htmlspecialchars($profile_data['nama_lengkap']);

if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Input Arsip Rapat | Notulen Tracker</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      background-color: #f5f7fa;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      font-family: Poppins,system-ui,-apple-system,Segoe UI,Roboto;
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

    /* Card */
    .card {
      border-radius: 12px;
    }

    /* Table Textarea Styling */
    #tabelHasil textarea {
      width: 100%;
      border: none;
      resize: none;
      overflow: hidden;
      background-color: transparent;
      white-space: pre-wrap;
      word-wrap: break-word;
      outline: none;
    }

    #tabelHasil td {
      vertical-align: top;
      word-break: break-word;
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
        <li class="nav-item"><a class="nav-link" href="dashboar.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link active" href="daftar_notulen.php">Daftar Notulen</a></li>
        <li class="nav-item"><a class="nav-link" href="kontak.php">Kontak</a></li>
        <li class="nav-item"><a class="nav-link" href="faq.php">FAQ</a></li>
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

  <div class="container mt-5 mb-5">
    <div class="card shadow p-4">
      <h4 class="text-center mb-4 text-primary fw-bold">Formulir Hasil Rapat</h4>
      <form id="formRapat" method="POST" action="input_hasil.php">

        <div class="mb-3">
          <label for="judulRapat" class="form-label">Judul Rapat</label>
          <input type="text" class="form-control" id="judulRapat" placeholder="Masukkan judul rapat" name="judul" required>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="tanggalRapat" class="form-label">Tanggal Rapat</label>
            <input type="date" class="form-control" id="tanggalRapat" name="tanggal" required>
          </div>
          <div class="col-md-6 mb-3">
            <label for="waktuRapat" class="form-label">Waktu Rapat</label>
            <input type="time" class="form-control" id="waktuRapat" name="waktu" required>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="tempatRapat" class="form-label">Tempat Rapat</label>
            <input type="text" class="form-control" id="tempatRapat" placeholder="Masukkan tempat rapat" name="tempat">
          </div>
          <div class="col-md-6 mb-3">
            <label for="penyelenggara" class="form-label">Penyelenggara Rapat</label>
            <input type="text" class="form-control" id="penyelenggara" placeholder="Masukkan penyelenggara rapat" name="penyelenggara">
          </div>
        </div>

        <div class="mb-3">
          <label for="notulis" class="form-label">Notulis</label>
          <input type="text" class="form-control" id="notulis" placeholder="Nama notulis" name="notulis">
        </div>

        <div class="mb-3">
          <label for="peserta" class="form-label">Peserta Rapat</label>
          <textarea class="form-control" id="peserta" rows="3" placeholder="Daftar peserta rapat" name="peserta"></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Hasil Rapat</label>
          <table class="table table-bordered align-middle" id="tabelHasil" name="hasil">
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
                <td><textarea name="topik[]"></textarea></td>
                <td><textarea name="pembahasan[]"></textarea></td>
                <td><textarea name="tindak_lanjut[]"></textarea></td>
                <td><textarea name="pic[]"></textarea></td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="hapusBaris(this)">Hapus</button></td>
              </tr>
            </tbody>
          </table>
          <button type="button" class="btn btn-outline-primary btn-sm" id="tambahBaris">+ Tambah Baris</button>
        </div>

        <div class="mb-3">
          <label for="catatan" class="form-label">Catatan Tambahan</label>
          <textarea class="form-control" id="catatan" rows="3" placeholder="Tambahkan catatan tambahan di sini..." name="catatan"></textarea>
        </div>

        <div class="mb-3">
          <label for="status" class="form-label">Status Rapat</label>
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

  <footer>
    ©2025 Notulen Tracker. Semua hak cipta dilindungi
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Otomatis tinggi textarea sesuai isi
    document.addEventListener("input", function (e) {
      if (e.target.tagName.toLowerCase() === "textarea") {
        e.target.style.height = "auto";
        e.target.style.height = e.target.scrollHeight + "px";
      }
    });

    // Tambah baris tabel
    document.getElementById("tambahBaris").addEventListener("click", () => {
      const tbody = document.querySelector("#tabelHasil tbody");
      const rowCount = tbody.rows.length + 1;
      const newRow = `
        <tr>
          <td>${rowCount}</td>
          <td><textarea name="topik[]" placeholder="Masukkan topik"></textarea></td>
          <td><textarea name="pembahasan[]" placeholder="Masukkan pembahasan"></textarea></td>
          <td><textarea name="tindak_lanjut[]" placeholder="Masukkan tindak lanjut"></textarea></td><td><textarea name="pic[]" placeholder="Nama PIC"></textarea></td>
          <td><button type="button" class="btn btn-danger btn-sm" onclick="hapusBaris(this)">Hapus</button></td>
        </tr>`;
      tbody.insertAdjacentHTML("beforeend", newRow);
      // Auto-size textarea setelah ditambahkan
      tbody.lastElementChild.querySelectorAll('textarea').forEach(textarea => {
          textarea.style.height = 'auto';
      });
    });

    // Hapus baris tabel
    function hapusBaris(btn) {
      btn.closest("tr").remove();
      const rows = document.querySelectorAll("#tabelHasil tbody tr");
      rows.forEach((r, i) => r.cells[0].textContent = i + 1);
    }

    // Batal
    document.getElementById("btnBatal").addEventListener("click", () => {
      if (confirm("Apakah Anda yakin ingin membatalkan?")) {
        window.location.href = "daftar_notulen.php";
      }
    });
  </script>

  <script>
  // Fungsi logout melalui menu dropdown
  document.getElementById("logoutLink").addEventListener("click", (e) => {
    e.preventDefault();
    const konfirmasi = confirm("Apakah Anda yakin ingin keluar dari Notulen Tracker?");
    if (konfirmasi) {
      window.location.href = "login.php";
    }
  });
  </script>

  <script>
document.getElementById("formRapat").addEventListener("submit", function(e) {
  e.preventDefault();

  let error = [];

  let judul = document.getElementById("judulRapat").value.trim();
  let tanggal = document.getElementById("tanggalRapat").value;
  let waktu = document.getElementById("waktuRapat").value;
  let notulis = document.getElementById("notulis").value.trim();

  if (!judul) error.push("Judul rapat wajib diisi");
  if (!tanggal) error.push("Tanggal wajib diisi");
  if (!waktu) error.push("Waktu wajib diisi");
  if (!notulis) error.push("Notulis wajib diisi");

  // VALIDASI TABEL HASIL RAPAT
  const rows = document.querySelectorAll("#tabelHasil tbody tr");
  let adaIsi = false;
  let hasIncompleteRow = false;

  rows.forEach((row, i) => {
    let cells = row.querySelectorAll("textarea");
    let isi = 0;
    
    cells.forEach(c => { 
        if (c.value.trim() !== "") isi++; 
    });

    if (isi > 0) {
        adaIsi = true;
    }

    // VALIDASI SEL TIDAK BOLEH SETENGAH KOSONG (kecuali jika semua kosong)
    if (isi > 0 && isi < cells.length) {
      hasIncompleteRow = true;
      error.push("Baris Hasil Rapat No. " + (i + 1) + " harus diisi lengkap (Topik, Pembahasan, Tindak Lanjut, PIC) atau dikosongkan.");
    }
  });

  if (!adaIsi && rows.length > 0) {
    error.push("Tabel hasil rapat tidak boleh kosong!");
  }
  
  if (error.length > 0) {
    alert("⚠️ ERROR:\n\n" + error.join("\n"));
    return;
  }

  // Jika semua validasi dilewati, submit form.
  // alert("✅ Data valid, siap disimpan!"); // Hapus ini jika sudah live
  this.submit(); // lanjut ke PHP (input_hasil.php)
});
</script>

<?php
// Tutup koneksi database
mysqli_close($conn);
?>
</body>
</html>