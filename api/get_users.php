<?php
$conn = mysqli_connect("localhost", "root", "", "notulen_db");

$search = mysqli_real_escape_string($conn, $_GET['q']);

$query = "SELECT nama_lengkap, email FROM pengguna 
          WHERE nama_lengkap LIKE '%$search%' 
          OR email LIKE '%$search%' 
          OR id LIKE '%$search%' LIMIT 10";

$result = mysqli_query($conn, $query);
$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}

header('Content-Type: application/json');
echo json_encode($users);
?>