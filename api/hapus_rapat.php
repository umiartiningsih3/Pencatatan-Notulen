<?php
session_start();
include "koneksi.php"; 

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];
$id_rapat = $_GET['id'];

$query_user = mysqli_query($conn, "SELECT nama_lengkap, role FROM pengguna WHERE id = '$user_id'");
$user_data = mysqli_fetch_assoc($query_user);
$nama_user_login = $user_data['nama_lengkap'];
$role_user = strtolower($user_data['role']);

$query_rapat = mysqli_query($conn, "SELECT notulis FROM rapat WHERE id = '$id_rapat'");
$data_rapat = mysqli_fetch_assoc($query_rapat);

if (!$data_rapat) {
    die("Data rapat tidak ditemukan.");
}

if ($role_user === 'admin' || ($role_user === 'notulis' && $data_rapat['notulis'] === $nama_user_login)) {
    
    $delete_detail = mysqli_prepare($conn, "DELETE FROM rapat_detail WHERE id_rapat = ?");
    mysqli_stmt_bind_param($delete_detail, "i", $id_rapat);
    mysqli_stmt_execute($delete_detail);

    $delete_rapat = mysqli_prepare($conn, "DELETE FROM rapat WHERE id = ?");
    mysqli_stmt_bind_param($delete_rapat, "i", $id_rapat);
    
    if (mysqli_stmt_execute($delete_rapat)) {
        header("Location: daftar_notulen.php?status=deleted");
    } else {
        echo "Gagal menghapus data: " . mysqli_error($conn);
    }

} else {
    echo "<script>
            alert('Anda tidak memiliki izin untuk menghapus notulen ini!');
            window.location.href = 'daftar_notulen.php';
          </script>";
}

mysqli_close($conn);
exit();
?>