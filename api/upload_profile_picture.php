<?php

header('Content-Type: application/json');
session_start();

$conn = mysqli_connect("localhost", "root", "", "notulen_db");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];

    if (isset($_POST['action']) && $_POST['action'] === 'delete_photo') {
        $res = mysqli_query($conn, "SELECT foto_profile FROM pengguna WHERE id = '$user_id'");
        $row = mysqli_fetch_assoc($res);
        
        if ($row && !empty($row['foto_profile'])) {
            if (file_exists($row['foto_profile'])) {
                unlink($row['foto_profile']);
            }
        }

        $query = "UPDATE pengguna SET foto_profile = NULL WHERE id = '$user_id'";
        if (mysqli_query($conn, $query)) {
            echo json_encode(['status' => 'success', 'message' => 'Foto berhasil dihapus']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data di database']);
        }
        exit;
    }

    if (isset($_FILES['profile_picture'])) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $res = mysqli_query($conn, "SELECT foto_profile FROM pengguna WHERE id = '$user_id'");
        $row = mysqli_fetch_assoc($res);
        if ($row && !empty($row['foto_profile']) && file_exists($row['foto_profile'])) {
            unlink($row['foto_profile']);
        }

        $filename = 'profile_' . $user_id . '_' . time() . '.jpg';
        $target_file = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            mysqli_query($conn, "UPDATE pengguna SET foto_profile = '$target_file' WHERE id = '$user_id'");
            echo json_encode(['status' => 'success', 'message' => 'Foto berhasil diperbarui']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengunggah file']);
        }
        exit;
    }
}