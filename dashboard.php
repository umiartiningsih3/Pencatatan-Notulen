<?php
// ================= KONEKSI DATABASE =================
$conn = mysqli_connect("localhost", "root", "", "notulen_db");
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// ================= AMBIL DATA NOTULEN =================
$query = mysqli_query($conn, "SELECT judul_rapat, tanggal, notulis, status FROM notulen");

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
  <link rel="stylesheet" href="dashboard.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
</head>

<body>

<header class="text-center mt-4">
  <h1 class="fw-bold text-primary">Selamat Datang di Notulen Tracker!</h1>
  <p class="lead">Solusi digital terbaik untuk kebutuhan rapat Anda.</p>
</header>

<nav class="navbar navbar-expand-lg navbar-dark px-4">
  <a class="navbar-brand" href="#">
    <img src="logono.jpeg" alt="Logo" width="50" class="me-2 rounded-circle">
    Notulen Tracker
  </a>
</nav>

<div class="container mt-4">

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
                <td><?= htmlspecialchars($row['judul_rapat']) ?></td>
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
