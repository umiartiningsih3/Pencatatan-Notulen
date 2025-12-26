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

  $role_display   = !empty($profile_db['role']) ? $profile_db['role'] : 'Peserta';
  $role_check     = strtolower($role_display);
  $nama_user_login = $profile_db['nama_lengkap'];
  $dropdown_nama  = htmlspecialchars($nama_user_login);
  $dropdown_email = htmlspecialchars($profile_db['email']);
  
  $dropdown_foto = (!empty($profile_db['foto_profile']) && file_exists($profile_db['foto_profile'])) 
                   ? htmlspecialchars($profile_db['foto_profile']) 
                   : 'user.png';

  if ($role_check === 'notulis' || $role_check === 'admin') {
      $query_all = mysqli_query($conn, "SELECT status FROM rapat");
  } else {
      $query_all = mysqli_query($conn, "SELECT status FROM rapat WHERE peserta LIKE '%$nama_user_login%'");
  }

  $total_fallback = 0;
  $selesai_fallback = 0;
  $belum_fallback = 0;

  while ($row = mysqli_fetch_assoc($query_all)) {
      $total_fallback++;
      if ($row['status'] === 'Selesai') {
          $selesai_fallback++;
      } elseif ($row['status'] === 'Belum Selesai') {
          $belum_fallback++;
      }
  }

  if ($role_check === 'notulis' || $role_check === 'admin') {
      $query_limit = mysqli_query($conn, "SELECT judul, tanggal, notulis, status FROM rapat ORDER BY id DESC LIMIT 3");
  } else {
      $query_limit = mysqli_query($conn, "SELECT judul, tanggal, notulis, status FROM rapat WHERE peserta LIKE '%$nama_user_login%' ORDER BY id DESC LIMIT 3");
  }

  $dataTabel = [];
  while ($row = mysqli_fetch_assoc($query_limit)) {
      $dataTabel[] = $row;
  }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
    <style>
        html, body { height: 100%; margin: 0; display: flex; flex-direction: column; }
        
        body { 
            font-family: 'Poppins', sans-serif; 
            background: url('gambarr.png') no-repeat center center fixed; 
            background-size: cover;
            padding-top: 80px; 
        }

        body::before {
            content: "";
            position: fixed;
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%;
            background: rgba(245, 247, 250, 0.85);
            z-index: -1;
        }

        .custom-navbar {
            background-color: #003366;
            height: 70px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
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
        }

        .navbar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff !important;
        }

        .navbar-nav .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
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

        .brand-pro img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            background: #ffffff;
            border: 2px solid rgba(255, 255, 255, 0.2);
            transition: all 0.35s ease;
        }

        .brand-pro:hover img {
            transform: scale(1.08) rotate(-4deg);
            box-shadow: 0 8px 25px rgba(144, 202, 249, 0.45);
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

        .dropdown-menu {
            min-width: 250px !important;
            border-radius: 8px;
            padding: 0;
            overflow: hidden;
        }

        .dropdown-menu .user-info-header {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
        }

        .dropdown-menu .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 12px;
            background-color: #e9ecef;
        }

        .dropdown-menu .user-text {
            display: flex;
            flex-direction: column;
        }

        .dropdown-menu .user-text strong {
            font-size: 15px;
            font-weight: 600;
            line-height: 1.2;
            color: #333;
        }

        .dropdown-menu .user-text small {
            font-size: 12px;
            color: #6c757d;
        }

        .dropdown-menu .dropdown-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            transition: background 0.2s;
        }

        .dropdown-menu .dropdown-item i {
            font-size: 1.1rem;
            width: 25px;
            color: #555;
        }

        .dashboard-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .card-icon {
            font-size: 2.5rem;
            opacity: 0.6;
        }

        .card-value {
            font-size: 2.2rem;
            font-weight: 700;
        }

        footer {
            background-color: #003366;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: auto;
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
                <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="bi bi-grid"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="daftar_notulen.php"><i class="bi bi-file-text"></i> Daftar Notulen</a></li>
                <li class="nav-item"><a class="nav-link" href="kontak.php"><i class="bi bi-envelope"></i> Kontak</a></li>
                <li class="nav-item"><a class="nav-link" href="FAQ.php"><i class="bi bi-question-circle"></i> FAQ</a></li>
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

    <div class="container mt-4 mb-5">
        <header class="text-center mt-4 mb-5">
            <h1 class="fw-bold text-primary">Selamat Datang di Notulen Tracker, <?= $dropdown_nama; ?>!</h1>
            <p class="lead">Solusi digital terbaik untuk kebutuhan rapat Anda.</p>
        </header>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card dashboard-card p-3">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted fw-bold">Total Rapat</h6>
                            <p id="totalNotulen" class="card-value text-primary">0</p> 
                        </div>
                        <i class="bi bi-list-columns card-icon text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card p-3">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted fw-bold">Rapat Selesai</h6>
                            <p id="selesaiNotulen" class="card-value text-success">0</p> 
                        </div>
                        <i class="bi bi-check-circle card-icon text-success"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card p-3">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted fw-bold">Belum Selesai</h6>
                            <p id="belumNotulen" class="card-value text-danger">0</p> 
                        </div>
                        <i class="bi bi-x-circle card-icon text-danger"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-0 dashboard-card p-4">
                    <div class="card-body text-center">
                        <h4 class="fw-bold text-primary mb-4">📊 Status Penyelesaian Rapat</h4>
                        <canvas id="pieChart" style="max-height:400px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mt-5" style="background: rgba(255, 255, 255, 0.9);">
            <div class="card-body">
                <h4 class="text-primary fw-bold mb-3">📘 Daftar Notulen Terbaru</h4>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>Judul Rapat</th>
                                <th>Tanggal</th>
                                <th>Notulis</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($dataTabel)): ?>
                                <tr><td colspan="4" class="text-center text-muted">Belum ada data.</td></tr>
                            <?php else: ?>
                                <?php foreach ($dataTabel as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['judul']) ?></td>
                                        <td><?= htmlspecialchars($row['tanggal']) ?></td>
                                        <td><?= htmlspecialchars($row['notulis']) ?></td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill <?= $row['status'] === 'Selesai' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                                <?= htmlspecialchars($row['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <footer>©2025 Notulen Tracker. Semua hak cipta dilindungi</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const fallbackData = {
            total: <?= $total_fallback ?>,
            selesai: <?= $selesai_fallback ?>,
            belum: <?= $belum_fallback ?>
        };

        function loadDashboardStats() {
            let stats = fallbackData;

            document.getElementById('totalNotulen').textContent = stats.total;
            document.getElementById('selesaiNotulen').textContent = stats.selesai;
            document.getElementById('belumNotulen').textContent = stats.belum;

            renderChart(stats.selesai, stats.belum);
        }

        function renderChart(selesai, belum) {
            Chart.register(ChartDataLabels);
            Chart.getChart("pieChart")?.destroy(); 
            new Chart(document.getElementById("pieChart"), {
                type: "pie",
                data: {
                    labels: ["Selesai", "Belum Selesai"],
                    datasets: [{
                        data: [selesai, belum],
                        backgroundColor: ["#2e7d32", "#fbc02d"],
                        borderWidth: 2
                    }]
                },
                options: {
                    plugins: {
                        legend: { position: "bottom" },
                        datalabels: {
                            color: "#fff",
                            formatter: (value, ctx) => {
                                let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                return sum === 0 ? "0%" : ((value / sum) * 100).toFixed(1) + "%";
                            }
                        }
                    }
                }
            });
        }

        document.getElementById("logoutLink")?.addEventListener("click", function(e) {
            e.preventDefault();
            if (confirm("Apakah Anda yakin ingin keluar?")) { window.location.href = "login.php"; }
        });

        document.addEventListener('DOMContentLoaded', loadDashboardStats);
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>