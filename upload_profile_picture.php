<?php
// TAMPILKAN ERROR UNTUK DEBUGGING. HAPUS BARIS INI JIKA SUDAH DI LINGKUNGAN PRODUKSI.
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ----------------------------------------------------
// DETAIL KONEKSI DATABASE (UBAH SESUAI KONFIGURASI ANDA)
// ----------------------------------------------------
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

// Pastikan request adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak diizinkan.']);
    mysqli_close($conn);
    exit;
}

// Cek File Upload (Nama input file adalah 'profile_picture' dari JavaScript)
if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
    $error_message = 'Tidak ada file diunggah atau terjadi error upload. Error code: ' . ($_FILES['profile_picture']['error'] ?? 'N/A');
    echo json_encode(['status' => 'error', 'message' => $error_message]);
    mysqli_close($conn);
    exit;
}

$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
if ($user_id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID pengguna tidak valid.']);
    mysqli_close($conn);
    exit;
}

$file = $_FILES['profile_picture'];
$target_dir = "uploads/profile_pics/"; // PASTIKAN FOLDER INI ADA DAN MEMILIKI IZIN TULIS!

// Cek dan buat direktori jika belum ada
if (!is_dir($target_dir)) {
    if (!mkdir($target_dir, 0755, true)) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal membuat direktori unggahan.']);
        mysqli_close($conn);
        exit;
    }
}

// Croppie diatur untuk output JPEG
$file_extension = 'jpeg'; 

// Beri nama file baru yang unik (user ID + timestamp)
$new_file_name = "user_" . $user_id . "_" . time() . "." . $file_extension;
$target_file = $target_dir . $new_file_name;
$db_path = $target_file; 

// --- 1. Ambil path foto lama sebelum update ---
$query_old = "SELECT foto_profile FROM notulis WHERE id = ?";
$stmt_old = $conn->prepare($query_old);
$stmt_old->bind_param("i", $user_id);
$stmt_old->execute();
$result_old = $stmt_old->get_result();
$old_pic_row = $result_old->fetch_assoc();
$old_pic_path = $old_pic_row ? $old_pic_row['foto_profile'] : null;
$stmt_old->close();

// --- 2. Pindahkan file yang diupload ---
if (move_uploaded_file($file['tmp_name'], $target_file)) {
    
    // --- 3. Update path file baru ke database ---
    $stmt = $conn->prepare("UPDATE notulis SET foto_profile = ? WHERE id = ?");
    $stmt->bind_param("si", $db_path, $user_id);

    if ($stmt->execute()) {
        // 4. Hapus foto lama jika ada dan bukan path default
        if ($old_pic_path && file_exists($old_pic_path) && basename($old_pic_path) !== 'user.png') {
            @unlink($old_pic_path);
        }

        echo json_encode(['status' => 'success', 'message' => 'Foto profil berhasil diunggah.', 'path' => $db_path]);
    } else {
        @unlink($target_file); // Hapus gambar baru jika update DB gagal
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui database: ' . $stmt->error]);
    }

    $stmt->close();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal memindahkan file. Periksa izin tulis folder.']);
}

mysqli_close($conn);
?>