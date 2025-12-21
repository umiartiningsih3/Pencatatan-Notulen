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
    <title>Reset Password - Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #003366, #007bff);
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        .form-container {
            background: #e3f2fd;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            padding: 30px;
            width: 100%;
            max-width: 400px;
        }
        .form-control { border-radius: 8px; }
        .btn-primary { 
            border-radius: 8px; 
            background-color: #007bff; 
            border: none;
            padding: 10px;
        }
        .btn-primary:hover { background-color: #0056b3; }
        h4 { font-weight: bold; color: #003366; }
    </style>
</head>
<body>

<div class="form-container">
    <h4 class="text-center mb-3">Reset Kata Sandi</h4>
    <p class="text-center text-muted small mb-4">Gunakan NIM terdaftar untuk mengubah sandi.</p>

    <form method="POST" action="">
      <div class="mb-3">
        <label class="form-label">NIM</label>
        <input type="text" class="form-control" name="nim" placeholder="Masukkan NIM Anda" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Kata Sandi Baru</label>
        <input type="password" class="form-control" name="password" placeholder="Min. 8 karakter" required>
      </div>

      <div class="mb-4">
        <label class="form-label">Konfirmasi Kata Sandi</label>
        <input type="password" class="form-control" name="confirm" placeholder="Ulangi sandi baru" required>
      </div>

      <button type="submit" name="reset" class="btn btn-primary w-100">Simpan Perubahan</button>
    </form>

    <div class="text-center mt-3">
        <a href="login.php" class="text-decoration-none small" style="color:#003366;">Kembali ke Login</a>
    </div>
</div>

</body>
</html>