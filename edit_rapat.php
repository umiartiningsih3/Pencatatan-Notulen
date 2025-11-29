<?php
// edit_rapat.php
header('Content-Type: application/json'); // Mengatur header respons sebagai JSON

// 1. Koneksi ke Database
$conn = mysqli_connect("localhost", "root", "", "notulen_db");

if (!$conn) {
    // Jika koneksi gagal, kembalikan error
    echo json_encode(["status" => "error", "message" => "Koneksi database gagal: " . mysqli_connect_error()]);
    exit;
}

// 2. Memproses Request POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil data dari POST
    $id      = $_POST['id'] ?? '';
    $judul   = $_POST['judul'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    $notulis = $_POST['notulis'] ?? '';
    $status  = $_POST['status'] ?? '';

    // Validasi dasar
    if (empty($id) || empty($judul) || empty($tanggal) || empty($notulis) || empty($status)) {
        echo json_encode(["status" => "error", "message" => "Semua kolom wajib diisi."]);
        mysqli_close($conn);
        exit;
    }

    // 3. Sanitasi Data
    $id_safe      = mysqli_real_escape_string($conn, $id);
    $judul_safe   = mysqli_real_escape_string($conn, $judul);
    $tanggal_safe = mysqli_real_escape_string($conn, $tanggal);
    $notulis_safe = mysqli_real_escape_string($conn, $notulis);
    $status_safe  = mysqli_real_escape_string($conn, $status);

    // 4. Query UPDATE
    $sql = "UPDATE rapat SET 
            judul = '$judul_safe', 
            tanggal = '$tanggal_safe', 
            notulis = '$notulis_safe', 
            status = '$status_safe' 
            WHERE id = '$id_safe'";

    if (mysqli_query($conn, $sql)) {
        // Respons sukses
        echo json_encode(["status" => "success", "message" => "Perubahan notulen berhasil disimpan!"]);
    } else {
        // Respons error
        echo json_encode(["status" => "error", "message" => "Gagal menyimpan perubahan: " . mysqli_error($conn)]);
    }

    // Tutup koneksi
    mysqli_close($conn);
    exit;

} else {
    // Jika diakses selain dengan metode POST
    echo json_encode(["status" => "error", "message" => "Metode request tidak diizinkan."]);
    mysqli_close($conn);
    exit;
}
?>