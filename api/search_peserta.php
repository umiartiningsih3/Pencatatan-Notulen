<?php
header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "", "notulen_db");

if (!$conn) { echo json_encode([]); exit; }

$search = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';

$query = "SELECT nama_lengkap, email FROM pengguna 
          WHERE nama_lengkap LIKE '%$search%' 
          OR email LIKE '%$search%' 
          LIMIT 10";

$result = mysqli_query($conn, $query);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        'id' => $row['nama_lengkap'], 
        'text' => $row['nama_lengkap'] . " (" . $row['email'] . ")"
    ];
}

echo json_encode($data);
?>