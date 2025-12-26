<?php
$conn = mysqli_connect("localhost", "root", "", "notulen_db");
$key = $_GET['key'];

$query = "SELECT nama_lengkap FROM pengguna WHERE nama_lengkap LIKE ? LIMIT 5";
$stmt = mysqli_prepare($conn, $query);
$search = "%$key%";
mysqli_stmt_bind_param($stmt, "s", $search);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $nama = $row['nama_lengkap'];

        echo "<a href='#' class='list-group-item list-group-item-action' onclick=\"pilihPeserta('$nama'); return false;\">$nama</a>";
    }
} else {
    echo "<div class='list-group-item text-muted small'>Nama tidak ditemukan</div>";
}
?>