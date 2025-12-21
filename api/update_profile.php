<?php
session_start();
header('Content-Type: application/json');

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "notulen_db";

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['id']) || !isset($input['nama'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    exit;
}

$user_id = $input['id'];
$nama = $input['nama'];

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

$query = "UPDATE pengguna SET nama_lengkap = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "si", $nama, $user_id);
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success', 'message' => 'Nama berhasil diperbarui.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui database.']);
    }
}
mysqli_close($conn);
?>