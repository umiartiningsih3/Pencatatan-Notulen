<?php
include 'koneksi.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_rapat = mysqli_real_escape_string($conn, $_POST['id']);
    $judul    = mysqli_real_escape_string($conn, $_POST['judul']);
    $tanggal  = $_POST['tanggal'];
    $waktu    = $_POST['waktu'];
    $tempat   = mysqli_real_escape_string($conn, $_POST['tempat']);
    $penyelenggara = mysqli_real_escape_string($conn, $_POST['penyelenggara']);
    $status   = $_POST['status'];
    $catatan  = mysqli_real_escape_string($conn, $_POST['catatan']);
    $peserta_arr = isset($_POST['peserta']) ? $_POST['peserta'] : [];
    $peserta_str = mysqli_real_escape_string($conn, implode(",", $peserta_arr));

    $queryUpdate = "UPDATE rapat SET 
                    judul = '$judul', 
                    tanggal = '$tanggal', 
                    waktu = '$waktu', 
                    tempat = '$tempat',
                    penyelenggara = '$penyelenggara', 
                    peserta = '$peserta_str', 
                    catatan = '$catatan',
                    status = '$status' 
                    WHERE id = '$id_rapat'";
    
    if (mysqli_query($conn, $queryUpdate)) {
        
        if (isset($_POST['topik']) && !empty($_POST['topik'])) {
            mysqli_query($conn, "DELETE FROM rapat_detail WHERE id_rapat = '$id_rapat'");

            $topik = $_POST['topik'];
            $pembahasan = $_POST['pembahasan'];
            $tindak_lanjut = $_POST['tindak_lanjut'];
            $pic = $_POST['pic'];

            for ($i = 0; $i < count($topik); $i++) {
                $t = mysqli_real_escape_string($conn, $topik[$i]);
                $p = mysqli_real_escape_string($conn, $pembahasan[$i]);
                $tl = mysqli_real_escape_string($conn, $tindak_lanjut[$i]);
                $pc = mysqli_real_escape_string($conn, $pic[$i]);

                $queryDetail = "INSERT INTO rapat_detail (id_rapat, topik, pembahasan, tindak_lanjut, pic) 
                                VALUES ('$id_rapat', '$t', '$p', '$tl', '$pc')";
                mysqli_query($conn, $queryDetail);
            }
        }

        echo json_encode(["status" => "success", "message" => "Data berhasil diperbarui"]);
    } else {
        echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
    }
}
?>