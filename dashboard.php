<?php
$activePage = basename($_SERVER['PHP_SELF']);
// ================= KONEKSI DATABASE =================
$conn = mysqli_connect("localhost", "root", "", "notulen_db");
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// 🚨 ASUMSI USER ID (GANTI DENGAN SESSION SETELAH LOGIN)
$user_id = 1; 

// ================= AMBIL DATA PROFIL UNTUK DROPDOWN =================
$query_profile = "SELECT nama_lengkap, email, foto_profile FROM notulis WHERE id = ?";
$stmt = mysqli_prepare($conn, $query_profile);

// Data default jika user_id tidak ditemukan atau koneksi gagal
$profile_data = [
    'nama_lengkap' => 'Notulis Tamu',
    'email' => 'tamu@notulen.com',
    'foto_profile' => 'user.png' 
];

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result_profile = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result_profile)) {
        $profile_data['nama_lengkap'] = $row['nama_lengkap'];
        $profile_data['email'] = $row['email'];
        if (!empty($row['foto_profile'])) {
            $profile_data['foto_profile'] = $row['foto_profile'];
        }
    }
    mysqli_stmt_close($stmt);
}

// Variabel untuk digunakan di HTML
$dropdown_email = htmlspecialchars($profile_data['email']);
$dropdown_nama = htmlspecialchars($profile_data['nama_lengkap']);
$dropdown_foto = htmlspecialchars($profile_data['foto_profile']);


// ================= AMBIL DATA NOTULEN (STATISTIK) =================
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
        /* =================================================== */
        /* START: NAVBAR & BRAND PRO STYLES */
        /* =================================================== */

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
        /* =================================================== */
        /* END: NAVBAR & BRAND PRO STYLES */
        /* =================================================== */


        /* =================================================== */
        /* START: DROPDOWN STYLES (DIPERLUKAN UNTUK PROFIL) */
        /* =================================================== */
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
            width: 40px; 
            height: 40px;
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
            margin-top: 0; 
        }
        .dropdown-menu .dropdown-item {
            display: flex;
            align-items: center;
            padding: 8px 15px; 
        }
        .dropdown-menu .dropdown-item i {
            font-size: 1.1rem;
            width: 20px; 
            text-align: center;
            margin-right: 8px; 
        }
        /* =================================================== */
        /* END: DROPDOWN STYLES */
        /* =================================================== */


        /* Gaya Khusus Dashboard */
        .container-main {
            padding: 20px 15px;
            flex: 1; /* Kontainer utama mengambil ruang yang tersisa */
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

<div class="container container-main">
    <header class="text-center mt-4 mb-5">
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

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
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
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Notulis
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
                        <li><hr class="dropdown-divider"></li>
                        <li><a id="logoutLink" class="dropdown-item text-danger" href="login.php"><i class="bi bi-box-arrow-right"></i> Keluar</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <div class="row g-3 text-center">
        <div class="col-md-4">
            <div class="card shadow-sm p-3 border-0">
                <h5 class="fw-bold text-primary">Total Rapat</h5>
                <h3 class="fw-bold"><?= $total ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm p-3 border-0">
                <h5 class="fw-bold text-success">Selesai</h5>
                <h3 class="fw-bold"><?= $selesai ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm p-3 border-0">
                <h5 class="fw-bold text-warning">Belum Selesai</h5>
                <h3 class="fw-bold"><?= $belum ?></h3>
            </div>
        </div>
    </div>

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

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h4 class="text-primary fw-bold mb-3">📘 Daftar Notulen Rapat</h4>
            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="notulenTable">
                    <thead class="table-primary">
                        <tr>
                            <th>Judul Rapat</th>
                            <th>Tanggal</th>
                            <th>Notulis</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dataTabel)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada data notulen rapat yang tersedia.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($dataTabel as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['judul']) ?></td>
                                    <td><?= htmlspecialchars($row['tanggal']) ?></td>
                                    <td><?= htmlspecialchars($row['notulis']) ?></td>
                                    <td>
                                        <span class="badge rounded-pill 
                                            <?php 
                                                if ($row['status'] === 'Selesai') echo 'bg-success'; 
                                                else echo 'bg-warning text-dark';
                                            ?>">
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

<footer class="text-center mt-4">
    ©2025 Notulen Tracker. Semua hak cipta dilindungi
</footer>

<script>
// ================= SCRIPT CHART JS =================
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
                    return total === 0 ? "0%" : ((value / total) * 100).toFixed(1) + "%";
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const value = context.parsed;
                        const percentage = total === 0 ? "0%" : ((value / total) * 100).toFixed(1) + "%";
                        return context.label + ': ' + value + ' (' + percentage + ')';
                    }
                }
            }
        }
    }
});
</script>

<script>
// ================= SCRIPT LOGOUT =================
document.addEventListener('DOMContentLoaded', function() {
    const logoutLink = document.getElementById("logoutLink");
    if (logoutLink) {
        logoutLink.addEventListener("click", (e) => {
            e.preventDefault();
            const konfirmasi = confirm("Apakah Anda yakin ingin keluar dari Notulen Tracker?");
            if (konfirmasi) {
                // Di sini Anda bisa menambahkan AJAX call ke script logout PHP
                window.location.href = "login.php"; 
            }
        });
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php 
// Tutup koneksi database
if (isset($conn)) {
    mysqli_close($conn);
}
?>

</body>
</html>