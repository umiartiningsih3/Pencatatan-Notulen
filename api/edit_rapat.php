<?php
header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "", "notulen_db");

if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Koneksi database gagal"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $judul = $_POST['judul'];
    $tanggal = $_POST['tanggal'];
    $waktu = $_POST['waktu'] ?? '';
    $tempat = $_POST['tempat'] ?? '';
    $penyelenggara = $_POST['penyelenggara'] ?? '';
    $catatan = $_POST['catatan'];
    $status = $_POST['status'];
    $peserta = isset($_POST['peserta']) ? implode(", ", $_POST['peserta']) : '';

    mysqli_begin_transaction($conn);

    try {
        $query_update = "UPDATE rapat SET 
            judul = ?, 
            tanggal = ?, 
            waktu = ?, 
            tempat = ?, 
            penyelenggara = ?, 
            peserta = ?, 
            catatan = ?, 
            status = ? 
            WHERE id = ?";
            
        $stmt = mysqli_prepare($conn, $query_update);
        mysqli_stmt_bind_param($stmt, "ssssssssi", $judul, $tanggal, $waktu, $tempat, $penyelenggara, $peserta, $catatan, $status, $id);
        mysqli_stmt_execute($stmt);

        if (isset($_POST['topik'])) {
            $query_delete = "DELETE FROM rapat_detail WHERE id_rapat = ?";
            $stmt_del = mysqli_prepare($conn, $query_delete);
            mysqli_stmt_bind_param($stmt_del, "i", $id);
            mysqli_stmt_execute($stmt_del);

            $query_detail = "INSERT INTO rapat_detail (id_rapat, topik, pembahasan, tindak_lanjut, pic) VALUES (?, ?, ?, ?, ?)";
            $stmt_ins = mysqli_prepare($conn, $query_detail);

            foreach ($_POST['topik'] as $key => $val) {
                $topik = $_POST['topik'][$key];
                $pembahasan = $_POST['pembahasan'][$key];
                $tindak_lanjut = $_POST['tindak_lanjut'][$key];
                $pic = $_POST['pic'][$key];
                
                mysqli_stmt_bind_param($stmt_ins, "issss", $id, $topik, $pembahasan, $tindak_lanjut, $pic);
                mysqli_stmt_execute($stmt_ins);
            }
        }

        mysqli_commit($conn);
        echo json_encode(["status" => "success", "message" => "Data rapat berhasil diperbarui"]);

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(["status" => "error", "message" => "Terjadi kesalahan: " . $e->getMessage()]);
    }
}
mysqli_close($conn);
?>