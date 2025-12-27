<?php
include "koneksi.php";

if (isset($_POST['reset'])) {
    $nim          = $_POST['nim']; 
    $nama_lengkap = $_POST['nama_lengkap'];
    $role         = $_POST['role'];
    $tgl_lahir    = $_POST['tgl_lahir'];
    $pass1        = $_POST['password'];
    $pass2        = $_POST['confirm'];

    if (empty($nim) || empty($nama_lengkap) || empty($role) || empty($tgl_lahir) || empty($pass1) || empty($pass2)) {
        echo "<script>alert('Semua data wajib diisi!');</script>";
    } elseif ($pass1 !== $pass2) {
        echo "<script>alert('Konfirmasi password tidak cocok!');</script>";
    } elseif (strlen($pass1) < 8) {
        echo "<script>alert('Password minimal 8 karakter!');</script>";
    } else {
        $stmt = $conn->prepare("SELECT nim FROM pengguna WHERE nim = ? AND LOWER(nama_lengkap) = LOWER(?) AND role = ? AND tgl_lahir = ?");
        $stmt->bind_param("ssss", $nim, $nama_lengkap, $role, $tgl_lahir);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo "<script>alert('Data verifikasi (NIM/Nama/Role/Tgl Lahir) tidak cocok!');</script>";
        } else {
            $hash = password_hash($pass1, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE pengguna SET password = ? WHERE nim = ?");
            $update->bind_param("ss", $hash, $nim);

            if ($update->execute()) {
                echo "<script>
                    alert('Identitas terverifikasi! Password berhasil diperbarui.');
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
    <title>Pemulihan Akun</title>
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
            backdrop-filter: blur(10px);
            border-radius: 15px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.5); 
            padding: 30px 35px; 
            max-width: 490px; 
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

        h4 { font-weight: 700; color: #003366; }
        .form-label { font-weight: 600; color: #333; font-size: 13px; margin-bottom: 4px; }
        
        /* 3. Animasi Input saat diklik */
        .form-control, .form-select { 
            font-size: 14px; 
            border-radius: 10px; 
            border: 1px solid #90caf9;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            box-shadow: 0 0 12px rgba(0, 123, 255, 0.3);
            transform: scale(1.02); /* Input sedikit membesar saat fokus */
        }
        
        .btn-primary { 
            background: linear-gradient(to right, #007bff, #003366); 
            border: none; border-radius: 10px; 
            font-weight: 600; padding: 12px; margin-top: 10px; 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .btn-primary:hover {
            transform: translateY(-3px); /* Tombol sedikit naik saat hover */
            box-shadow: 0 8px 20px rgba(0, 123, 255, 0.4);
            filter: brightness(1.1);
        }

        .section-divider {
            border-top: 2px dashed #90caf9;
            margin: 25px 0;
            opacity: 0.5;
        }

        .input-group-text {
            border-color: #90caf9;
            border-radius: 10px 0 0 10px !important;
        }
        
        .form-control {
            border-radius: 0 10px 10px 0 !important;
        }

        .back-link {
            transition: 0.3s ease;
            display: inline-block;
        }

        .back-link:hover {
            transform: translateX(-5px); /* Geser ke kiri halus */
            color: #003366 !important;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h4 class="text-center">Pemulihan Akun</h4>
    <p class="text-center text-muted small mb-4">Lengkapi seluruh data di bawah ini untuk memvalidasi kepemilikan akun Anda.</p>

    <form method="POST" action="">
        <div class="mb-2">
            <label class="form-label">NIM atau NIK</label>
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control" name="nim" placeholder="Masukkan NIM/NIK" required>
            </div>
        </div>

        <div class="mb-2">
            <label class="form-label">Nama Lengkap</label>
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white"><i class="bi bi-card-text"></i></span>
                <input type="text" class="form-control" name="nama_lengkap" placeholder="Nama sesuai profil" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-2">
                <label class="form-label">Role</label>
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-briefcase"></i></span>
                    <select class="form-select" name="role" required style="border-radius: 0 10px 10px 0 !important;">
                        <option value="" selected disabled>Pilih</option>
                        <option value="peserta">Peserta</option>
                        <option value="notulis">Notulis</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label">Tanggal Lahir</label>
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-calendar-event"></i></span>
                    <input type="date" class="form-control" name="tgl_lahir" required>
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <div class="mb-2">
            <label class="form-label">Kata Sandi Baru</label>
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control" name="password" placeholder="Min. 8 karakter" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Konfirmasi Kata Sandi</label>
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white"><i class="bi bi-shield-check"></i></span>
                <input type="password" class="form-control" name="confirm" placeholder="Ulangi sandi" required>
            </div>
        </div>

        <button type="submit" name="reset" class="btn btn-primary w-100 shadow">
            <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
        </button>
    </form>

    <div class="text-center mt-3">
        <a href="login.php" class="text-decoration-none small back-link" style="color: #007bff; font-weight: 600;">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Masuk
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>