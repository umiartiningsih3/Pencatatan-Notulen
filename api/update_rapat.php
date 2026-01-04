<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_rapat = $_POST['id'];
    $judul    = mysqli_real_escape_string($conn, $_POST['judul']);
    $tanggal  = $_POST['tanggal'];
    $waktu    = $_POST['waktu'];
    $tempat   = mysqli_real_escape_string($conn, $_POST['tempat']);
    $status   = $_POST['status'];

    $queryUpdate = "UPDATE rapat SET 
                    judul = '$judul', 
                    tanggal = '$tanggal', 
                    waktu = '$waktu', 
                    tempat = '$tempat', 
                    status = '$status' 
                    WHERE id = '$id_rapat'";
    
    if (mysqli_query($conn, $queryUpdate)) {
        
        header("Location: index.php?status=sukses");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>