<?php
include "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul         = mysqli_real_escape_string($conn, $_POST['judul']);
    $tanggal       = $_POST['tanggal'];
    $waktu         = $_POST['waktu'];
    $tempat        = mysqli_real_escape_string($conn, $_POST['tempat']);
    $penyelenggara = mysqli_real_escape_string($conn, $_POST['penyelenggara']);
    $notulis       = mysqli_real_escape_string($conn, $_POST['notulis']);
    $peserta       = isset($_POST['daftar_peserta']) ? mysqli_real_escape_string($conn, $_POST['daftar_peserta']) : '';
    $catatan       = isset($_POST['catatan']) ? mysqli_real_escape_string($conn, $_POST['catatan']) : '';
    
    $status        = mysqli_real_escape_string($conn, $_POST['status']); 

    $query_rapat = "INSERT INTO rapat (judul, tanggal, waktu, tempat, penyelenggara, notulis, peserta, catatan, status) 
                    VALUES ('$judul', '$tanggal', '$waktu', '$tempat', '$penyelenggara', '$notulis', '$peserta', '$catatan', '$status')";

    if (mysqli_query($conn, $query_rapat)) {
        $rapat_id = mysqli_insert_id($conn);

        if (isset($_POST['topik']) && is_array($_POST['topik'])) {
            foreach ($_POST['topik'] as $key => $val) {
                $topik         = mysqli_real_escape_string($conn, $_POST['topik'][$key]);
                $pembahasan    = mysqli_real_escape_string($conn, $_POST['pembahasan'][$key]);
                $tindak_lanjut = mysqli_real_escape_string($conn, $_POST['tindak_lanjut'][$key]);
                $pic           = mysqli_real_escape_string($conn, $_POST['pic'][$key]);

                if (!empty($topik)) {
                    $query_detail = "INSERT INTO rapat_detail (id_rapat, topik, pembahasan, tindak_lanjut, pic) 
                                     VALUES ('$rapat_id', '$topik', '$pembahasan', '$tindak_lanjut', '$pic')";
                    mysqli_query($conn, $query_detail);
                }
            }
        }
        
        header("Location: daftar_notulen.php?status=success");
        exit();
    } else {
        die("Error Database Rapat: " . mysqli_error($conn));
    }
}
?>