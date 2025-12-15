<?php
$activePage = basename($_SERVER['PHP_SELF']);
// ================= KONEKSI DATABASE =================
$conn = mysqli_connect("localhost", "root", "", "notulen_db");
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// ================= AMBIL DATA NOTULEN =================
$query = mysqli_query($conn, "SELECT judul, tanggal, notulis, status FROM rapat");

$total = 0;
$selesai = 0;
$belum = 0;
$dataTabel = [];

while ($row = mysqli_fetch_assoc($query)) {
    $total++;

    if ($row['status'] === 'Selesai') {
        $selesai++;
    } elseif ($row['status'] === 'Belum Selesai') {
        $belum++;
    }

    $dataTabel[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Notulen Tracker</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
  <style>
    html, body {
      height: 100%;
      margin: 0;
      display: flex;
      flex-direction: column;
    }
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f5f7fa;
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
    #notulenTable thead th {
      background-color: #003366;
      color: white;
      font-weight: bold;
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
  </style>
</head>

<body>

<header class="text-center mt-4">
  <h1 class="fw-bold text-primary">Selamat Datang di Notulen Tracker!</h1>
  <p class="lead">Solusi digital terbaik untuk kebutuhan rapat Anda.</p>
</header>

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
        <a class="nav-link active" href="dashboard.php">
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

  <!-- STATISTIK -->
  <div class="row g-3 text-center">
    <div class="col-md-4">
      <div class="card shadow-sm p-3">
        <h5 class="fw-bold text-primary">Total Rapat</h5>
        <h3 class="fw-bold"><?= $total ?></h3>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm p-3">
        <h5 class="fw-bold text-success">Selesai</h5>
        <h3 class="fw-bold"><?= $selesai ?></h3>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm p-3">
        <h5 class="fw-bold text-warning">Belum Selesai</h5>
        <h3 class="fw-bold"><?= $belum ?></h3>
      </div>
    </div>
  </div>

  <!-- PIE CHART (TETAP SEPERTI PUNYA KAMU) -->
  <div class="row mt-4 justify-content-center">
    <div class="col-md-8">
      <div class="card shadow-lg border-0">
        <div class="card-body text-center">
          <h4 class="fw-bold text-primary mb-4">
            📊 Status Penyelesaian Rapat
          </h4>
          <canvas id="pieChart" style="max-height:420px;"></canvas>
          <p class="mt-3 text-muted">
            Diagram ini menunjukkan proporsi rapat yang telah dan belum diselesaikan.
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- TABEL NOTULEN -->
  <div class="card shadow-sm mt-4">
    <div class="card-body">
      <h4 class="text-primary fw-bold mb-3">📘 Daftar Notulen Rapat</h4>
      <div class="table-responsive">
        <table class="table table-bordered align-middle">
          <thead class="table-primary">
            <tr>
              <th>Judul Rapat</th>
              <th>Tanggal</th>
              <th>Notulis</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($dataTabel as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row['judul']) ?></td>
                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                <td><?= htmlspecialchars($row['notulis']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<footer class="text-center mt-4 mb-3">
  ©2025 Notulen Tracker. Semua hak cipta dilindungi
</footer>

<script>
Chart.register(ChartDataLabels);

new Chart(document.getElementById("pieChart"), {
  type: "pie",
  data: {
    labels: ["Selesai", "Belum Selesai"],
    datasets: [{
      data: [<?= $selesai ?>, <?= $belum ?>],
      backgroundColor: ["#2e7d32", "#fbc02d"],
      hoverBackgroundColor: ["#1b5e20", "#f9a825"],
      borderWidth: 3,
      offset: [20, 0]
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        position: "bottom",
        labels: { font: { size: 14, weight: "bold" } }
      },
      datalabels: {
        color: "#fff",
        font: { weight: "bold", size: 16 },
        formatter: (value, ctx) => {
          const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
          return ((value / total) * 100).toFixed(1) + "%";
        }
      }
    }
  }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



</body>
</html>
