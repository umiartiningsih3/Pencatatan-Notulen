<?php
$koneksi = new mysqli("localhost", "root", "", "notulen_db");

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$nama = $_POST['nama'];
$email = $_POST['email'];
$peran = $_POST['peran'];
$password = $_POST['password'];
$password2 = $_POST['password2'];

// Cek password sama
if ($password !== $password2) {
    die("Password tidak sama!");
}

// Cek apakah email sudah ada
$cek = $koneksi->query("SELECT * FROM pendaftaran WHERE email='$email'");
if ($cek->num_rows > 0) {
    die("Email sudah terdaftar!");
}

// Enkripsi password
$hash = password_hash($password, PASSWORD_DEFAULT);

// Insert data
$sql = "INSERT INTO pendaftaran (nama, email, peran, password)
        VALUES ('$nama', '$email', '$peran', '$hash')";

if ($koneksi->query($sql) === TRUE) {
    echo "Pendaftaran berhasil!";
} else {
    echo "Error: " . $koneksi->error;
}
?>
