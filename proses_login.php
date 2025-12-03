<?php
session_start();
include "koneksi_login.php";

$email = $_POST['email'];
$password = $_POST['password'];

$query = mysqli_query($koneksi, "SELECT * FROM pendaftaran WHERE email='$email' LIMIT 1");
$data = mysqli_fetch_assoc($query);

// Email tidak ditemukan
if (!$data) {
    echo "<script>
            alert('Email tidak ditemukan!');
            window.location='login.php';
          </script>";
    exit;
}

// Cek password (harus password_hash)
if (password_verify($password, $data['password'])) {

    $_SESSION['id'] = $data['id'];
    $_SESSION['nama'] = $data['nama'];
    $_SESSION['email'] = $data['email'];
    $_SESSION['peran'] = $data['peran'];

    if ($data['peran'] == "Notulis") {
        header("Location: dashboard.php");
    } else {
        header("Location: peserta.php");
    }
    exit;

} else {
    echo "<script>
            alert('Password salah!');
            window.location='login.php';
          </script>";
    exit;
}
?>
