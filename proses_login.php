<?php
// 1. Memulai Sesi
session_start();

// 2. Koneksi ke Database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "notulen_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil input dan membersihkan spasi liar
    $nim_input = trim($_POST['nim']); 
    $pass_input = trim($_POST['password']);

    if (empty($nim_input) || empty($pass_input)) {
        echo "<script>alert('Harap isi NIM dan Password!'); window.location='login.php';</script>";
        exit();
    }

    // 3. Query mencari user berdasarkan NIM
    // Mengambil 'bergabung_sejak' untuk pengecekan otomatis
    $query = "SELECT id, nim, nama_lengkap, password, bergabung_sejak FROM pengguna WHERE nim = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $nim_input);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        
        // 4. Verifikasi Password (Mendukung Teks Biasa & Hash)
        // password_verify() digunakan jika pass di DB sudah di-hash
        // Perbandingan langsung ($pass_input === ...) digunakan jika pass masih teks biasa (NIM)
        if (password_verify($pass_input, $row['password']) || $pass_input === $row['password']) {
            
            // --- FITUR AUTO-HASH ---
            // Jika password masih teks biasa (belum di-hash), otomatis ubah menjadi hash untuk keamanan
            if ($pass_input === $row['password'] && !password_get_info($row['password'])['algo']) {
                $new_hash = password_hash($pass_input, PASSWORD_BCRYPT);
                $update_hash_query = "UPDATE pengguna SET password = ? WHERE id = ?";
                $stmt_hash = mysqli_prepare($conn, $update_hash_query);
                mysqli_stmt_bind_param($stmt_hash, "si", $new_hash, $row['id']);
                mysqli_stmt_execute($stmt_hash);
            }

            // --- FITUR AUTO-TANGGAL BERGABUNG ---
            // Jika kolom bergabung_sejak masih kosong/NULL, isi dengan tanggal hari ini
            if (empty($row['bergabung_sejak']) || $row['bergabung_sejak'] == '0000-00-00') {
                $today = date('Y-m-d');
                $update_date_query = "UPDATE pengguna SET bergabung_sejak = ? WHERE id = ?";
                $stmt_date = mysqli_prepare($conn, $update_date_query);
                mysqli_stmt_bind_param($stmt_date, "si", $today, $row['id']);
                mysqli_stmt_execute($stmt_date);
            }

            // --- PENGATURAN SESSION ---
            $_SESSION['id'] = $row['id']; 
            $_SESSION['nama'] = $row['nama_lengkap'];
            $_SESSION['nim'] = $row['nim'];

            // Regenerasi ID session agar terhindar dari Session Fixation
            session_regenerate_id(true);

            // Arahkan ke dashboard
            header("Location: dashboard.php");
            exit();
            
        } else {
            echo "<script>alert('Password salah!'); window.location='login.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('NIM tidak terdaftar!'); window.location='login.php';</script>";
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>