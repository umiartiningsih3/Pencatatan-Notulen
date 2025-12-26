<?php
header('Content-Type: application/json');

$conn = mysqli_connect("localhost", "root", "", "notulen_db");

if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Koneksi database gagal: " . mysqli_connect_error()]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id      = $_POST['id'] ?? '';
    $judul   = $_POST['judul'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    $notulis = $_POST['notulis'] ?? '';
    $catatan = $_POST['catatan'] ?? '';
    $status  = $_POST['status'] ?? '';

    $id_safe      = mysqli_real_escape_string($conn, $id);
    $judul_safe   = mysqli_real_escape_string($conn, $judul);
    $tanggal_safe = mysqli_real_escape_string($conn, $tanggal);
    $notulis_safe = mysqli_real_escape_string($conn, $notulis);
    $catatan_safe = mysqli_real_escape_string($conn, $catatan);
    $status_safe  = mysqli_real_escape_string($conn, $status);

    $sql = "UPDATE rapat SET 
            judul = '$judul_safe', 
            tanggal = '$tanggal_safe', 
            notulis = '$notulis_safe',
            catatan = '$catatan_safe',
            status = '$status_safe' 
            WHERE id = '$id_safe'";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["status" => "success", "message" => "Perubahan notulen berhasil disimpan!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menyimpan perubahan: " . mysqli_error($conn)]);
    }

    mysqli_close($conn);
    exit;

} else {
    echo json_encode(["status" => "error", "message" => "Metode request tidak diizinkan."]);
    mysqli_close($conn);
    exit;
}
?>