<?php
// TAMPILKAN ERROR UNTUK DEBUGGING. HAPUS BARIS INI JIKA SUDAH DI LINGKUNGAN PRODUKSI.
ini_set('display_errors', 1);
error_reporting(E_ALL);

// DETAIL KONEKSI DATABASE
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "notulen_db";

header('Content-Type: application/json');

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Koneksi database gagal.']);
    exit;
}

// Menerima data JSON dari body request
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

$user_id = isset($data['id']) ? (int)$data['id'] : 0;
$nama = isset($data['nama']) ? trim($data['nama']) : '';
$divisi = isset($data['divisi']) ? trim($data['divisi']) : '';
$peran = isset($data['peran']) ? trim($data['peran']) : '';

if ($user_id === 0 || empty($nama) || empty($divisi) || empty($peran)) {
    echo json_encode(['status' => 'error', 'message' => 'Data input tidak lengkap.']);
    mysqli_close($conn);
    exit;
}

// Query UPDATE menggunakan Prepared Statement
$query = "UPDATE notulis SET nama_lengkap = ?, divisi = ?, peran = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("sssi", $nama, $divisi, $peran, $user_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Data profil berhasil diperbarui.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data: ' . $stmt->error]);
}

$stmt->close();
mysqli_close($conn);
?>