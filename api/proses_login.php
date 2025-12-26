<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "notulen_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_input = trim($_POST['nim']); 
    $pass_input = trim($_POST['password']);

    if (empty($user_input) || empty($pass_input)) {
        echo "<script>alert('Harap isi kolom login dan Password!'); window.location='login.php';</script>";
        exit();
    }

    $query = "SELECT id, nim, nama_lengkap, password, bergabung_sejak FROM pengguna WHERE nim = ? OR email = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    mysqli_stmt_bind_param($stmt, "ss", $user_input, $user_input);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        
        if (password_verify($pass_input, $row['password']) || $pass_input === $row['password']) {

            if ($pass_input === $row['password'] && !password_get_info($row['password'])['algo']) {
                $new_hash = password_hash($pass_input, PASSWORD_BCRYPT);
                $update_hash_query = "UPDATE pengguna SET password = ? WHERE id = ?";
                $stmt_hash = mysqli_prepare($conn, $update_hash_query);
                mysqli_stmt_bind_param($stmt_hash, "si", $new_hash, $row['id']);
                mysqli_stmt_execute($stmt_hash);
            }

            if (empty($row['bergabung_sejak']) || $row['bergabung_sejak'] == '0000-00-00') {
                $today = date('Y-m-d');
                $update_date_query = "UPDATE pengguna SET bergabung_sejak = ? WHERE id = ?";
                $stmt_date = mysqli_prepare($conn, $update_date_query);
                mysqli_stmt_bind_param($stmt_date, "si", $today, $row['id']);
                mysqli_stmt_execute($stmt_date);
            }

            $_SESSION['id'] = $row['id']; 
            $_SESSION['nama'] = $row['nama_lengkap'];
            $_SESSION['nim'] = $row['nim'];

            session_regenerate_id(true);

            header("Location: dashboard.php");
            exit();
            
        } else {
            echo "<script>alert('Password salah!'); window.location='login.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Akun tidak terdaftar!'); window.location='login.php';</script>";
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>