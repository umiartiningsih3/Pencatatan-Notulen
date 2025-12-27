<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesi berakhir, silakan login kembali.']);
    exit();
}

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "notulen_db";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Koneksi database gagal.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id']) && isset($data['nama'])) {
    $user_id = $data['id'];
    $nama = mysqli_real_escape_string($conn, $data['nama']);
    $tgl_lahir = mysqli_real_escape_string($conn, $data['tgl_lahir']); 

    if (empty($tgl_lahir)) {
        $query = "UPDATE pengguna SET nama_lengkap = ?, tgl_lahir = NULL WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $nama, $user_id);
    } else {
        $query = "UPDATE pengguna SET nama_lengkap = ?, tgl_lahir = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssi", $nama, $tgl_lahir, $user_id);
    }

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success', 'message' => 'Profil berhasil diperbarui!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui database: ' . mysqli_error($conn)]);
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
}

mysqli_close($conn);
?>