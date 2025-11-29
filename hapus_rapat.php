<?php
$conn = mysqli_connect("localhost", "root", "", "notulen_db");
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$id = $_GET['id'];

// Hapus detail rapat DULU (karena biasanya berelasi)
mysqli_query($conn, "DELETE FROM rapat_detail WHERE rapat_id='$id'");

// Hapus data utama
mysqli_query($conn, "DELETE FROM rapat WHERE id='$id'");

// Redirect kembali ke halaman daftar
header("Location: daftar_notulen.php");
exit();
?>

