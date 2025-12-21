<?php
$conn = mysqli_connect("localhost", "root", "", "notulen_db");
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM rapat_detail WHERE id_rapat = '$id'");

mysqli_query($conn, "DELETE FROM rapat WHERE id = '$id'");

header("Location: daftar_notulen.php");
exit();
?>
