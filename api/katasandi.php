<?php
include "koneksi.php";

if (isset($_POST['reset'])) {
    $nim     = $_POST['nim']; 
    $pass1   = $_POST['password'];
    $pass2   = $_POST['confirm'];

    if (empty($nim) || empty($pass1) || empty($pass2)) {
        echo "<script>alert('Semua data wajib diisi!');</script>";
    } elseif ($pass1 !== $pass2) {
        echo "<script>alert('Konfirmasi password tidak cocok!');</script>";
    } elseif (strlen($pass1) < 8) {
        echo "<script>alert('Password minimal 8 karakter!');</script>";
    } else {
        $stmt = $conn->prepare("SELECT nim FROM pengguna WHERE nim = ?");
        $stmt->bind_param("s", $nim);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo "<script>alert('NIM tidak terdaftar dalam database!');</script>";
        } else {
            $hash = password_hash($pass1, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE pengguna SET password = ? WHERE nim = ?");
            $update->bind_param("ss", $hash, $nim);

            if ($update->execute()) {
                echo "<script>
                    alert('Password berhasil diperbarui! Silakan login.');
                    window.location='login.php';
                </script>";
            } else {
                echo "<script>alert('Gagal sistem: Terjadi kesalahan saat update.');</script>";
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Atur Ulang Kata Sandi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(rgba(0, 15, 35, 0.75), rgba(0, 15, 35, 0.75)), 
                        url('gambarr.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: 'Poppins', sans-serif; 
            min-height: 100vh; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            padding: 20px; 
            margin: 0; 
            overflow-x: hidden;
        }

        .form-container { 
            background: rgba(227, 242, 253, 0.95); 
            backdrop-filter: blur(5px);
            border-radius: 15px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.5); 
            padding: 40px 35px; 
            width: 100%; 
            max-width: 400px; 
            transition: transform 0.3s ease;
            animation: float 5s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .form-container:hover { 
            animation-play-state: paused;
            box-shadow: 0 20px 40px rgba(0,0,0,0.6); 
        }

        h4 { 
            font-weight: 700; 
            font-size: 26px; 
            color: #003366; 
            margin-bottom: 10px; 
        }

        .form-label { font-weight: 600; 
            color: #333; 
            font-size: 14px; 
        }

        .form-control { font-size: 14px; 
            border: 1px solid #90caf9; 
            border-radius: 10px; 
            padding: 12px; 
        }
        
        .btn-primary { 
            background: linear-gradient(to right, #007bff, #003366); 
            border: none; 
            border-radius: 10px; 
            font-size: 16px; 
            font-weight: 600; 
            padding: 12px 0; 
            margin-top: 10px; 
            transition: 0.3s; 
        }

        .btn-primary:hover { 
            background: linear-gradient(to right, #003366, #001a33); 
            transform: translateY(-2px);
        }

        .btn-primary:active {
            transform: scale(0.95);
        }

        .small-link { 
            font-size: 13px; 
            color: #007bff; 
            text-decoration: none; 
        }
        
        .small-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="form-container">
    <h4 class="text-center">Atur Ulang Kata Sandi</h4>
    <p class="text-center text-muted small mb-4">Gunakan NIM terdaftar untuk mengubah sandi.</p>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">NIM</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0" style="border-radius: 10px 0 0 10px; border-color: #90caf9;">
                    <i class="bi bi-person text-muted"></i>
                </span>
                <input type="text" class="form-control border-start-0" name="nim" placeholder="Masukkan NIM Anda" style="border-radius: 0 10px 10px 0;" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Kata Sandi Baru</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0" style="border-radius: 10px 0 0 10px; border-color: #90caf9;">
                    <i class="bi bi-lock text-muted"></i>
                </span>
                <input type="password" class="form-control border-start-0" name="password" placeholder="Min. 8 karakter" style="border-radius: 0 10px 10px 0;" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Konfirmasi Kata Sandi</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0" style="border-radius: 10px 0 0 10px; border-color: #90caf9;">
                    <i class="bi bi-shield-check text-muted"></i>
                </span>
                <input type="password" class="form-control border-start-0" name="confirm" placeholder="Ulangi sandi baru" style="border-radius: 0 10px 10px 0;" required>
            </div>
        </div>

        <button type="submit" name="reset" class="btn btn-primary w-100">
            <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
        </button>
    </form>

    <div class="text-center mt-4">
        <a href="login.php" class="small-link">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Halaman Masuk
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>