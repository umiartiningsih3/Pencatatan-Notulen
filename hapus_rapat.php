<?php
$conn = mysqli_connect("localhost", "root", "", "notulen_db");
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$id = $_GET['id'];

// Hapus detail rapat berdasarkan id_rapat
mysqli_query($conn, "DELETE FROM rapat_detail WHERE id_rapat = '$id'");

// Hapus data utama rapat
mysqli_query($conn, "DELETE FROM rapat WHERE id = '$id'");

// Kembali ke halaman daftar
header("Location: daftar_notulen.php");
exit();
?>
