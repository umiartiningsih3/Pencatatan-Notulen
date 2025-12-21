<?php
// upload_profile_picture.php
session_start();
header('Content-Type: application/json');

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "notulen_db";

// 1. Pastikan folder ini ADA di dalam folder HTDOCS Anda
$upload_dir = 'uploads/profile_pics/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if (!isset($_FILES['profile_picture']) || !isset($_POST['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    exit;
}

$user_id = (int)$_POST['user_id'];
$file = $_FILES['profile_picture'];
$new_file_name = $user_id . '_' . time() . '.jpg';
$target_file = $upload_dir . $new_file_name;

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// 2. Ambil foto lama untuk dihapus (Gunakan tabel 'pengguna')
$query_old = mysqli_query($conn, "SELECT foto_profile FROM pengguna WHERE id = $user_id");
$row_old = mysqli_fetch_assoc($query_old);
$old_foto = $row_old['foto_profile'] ?? null;

// 3. Proses Upload
if (move_uploaded_file($file['tmp_name'], $target_file)) {
    // Update ke tabel 'pengguna'
    $query_update = "UPDATE pengguna SET foto_profile = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query_update);
    mysqli_stmt_bind_param($stmt, "si", $target_file, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        // Hapus file fisik lama jika bukan default
        if ($old_foto && $old_foto != 'user.png' && file_exists($old_foto)) {
            @unlink($old_foto);
        }
        echo json_encode(['status' => 'success', 'message' => 'Foto berhasil diperbarui!', 'file_path' => $target_file]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal update database.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal memindahkan file ke folder uploads.']);
}
?>