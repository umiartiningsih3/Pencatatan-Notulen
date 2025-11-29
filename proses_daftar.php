<?php
// KONEKSI KE DATABASE
$koneksi = new mysqli("localhost", "root", "", "notulen_db");

// CEK KONEKSI
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// AMBIL DATA DARI FORM
$nama     = $_POST['nama'];
$email    = $_POST['email'];
$peran    = $_POST['peran'];
$password = $_POST['password'];
$pass2    = $_POST['password2'];

// CEK KONFIRMASI PASSWORD
if ($password !== $pass2) {
    echo "<script>alert('Password tidak sama!'); window.location='daftar.php';</script>";
    exit();
}

// CEK EMAIL SUDAH ADA ATAU BELUM
$cek = $koneksi->query("SELECT * FROM pendaftaran WHERE email='$email'");

if ($cek->num_rows > 0) {
    echo "<script>alert('Email sudah terdaftar!'); window.location='daftar.php';</script>";
    exit();
}

// ENKRIPSI PASSWORD
$hash = password_hash($password, PASSWORD_DEFAULT);

// INSERT DATA
$sql = "INSERT INTO pendaftaran (nama, email, peran, password)
        VALUES ('$nama', '$email', '$peran', '$hash')";

if ($koneksi->query($sql) === TRUE) {
    echo "
        <script>
            alert('Pendaftaran berhasil!');
            window.location='login.php';   // 🔥 DI SINI REDIRECT
        </script>
    ";
} else {
    echo "Error: " . $koneksi->error;
}
?>
