<?php
include "koneksi.php";

// Ambil data utama rapat
$judul        = $_POST['judul'];
$tanggal      = $_POST['tanggal'];
$waktu        = $_POST['waktu'];
$tempat       = $_POST['tempat'];
$penyelenggara= $_POST['penyelenggara'];
$notulis      = $_POST['notulis'];
$peserta      = $_POST['peserta'];
$catatan      = $_POST['catatan'];
$status       = $_POST['status'];

// Simpan ke tabel rapat
$query = "INSERT INTO rapat 
(judul, tanggal, waktu, tempat, penyelenggara, notulis, peserta, catatan, status)
VALUES 
('$judul','$tanggal','$waktu','$tempat','$penyelenggara','$notulis','$peserta','$catatan','$status')";

mysqli_query($conn, $query);

// Ambil ID rapat terakhir
$id_rapat = mysqli_insert_id($conn);

// Ambil array hasil rapat
$topik         = $_POST['topik'];
$pembahasan    = $_POST['pembahasan'];
$tindak_lanjut = $_POST['tindak_lanjut'];
$pic           = $_POST['pic'];

// Simpan detail hasil rapat
for ($i = 0; $i < count($topik); $i++) {

  if (!empty($topik[$i]) && !empty($pembahasan[$i])) {

    $sql = "INSERT INTO rapat_detail 
    (id_rapat, topik, pembahasan, tindak_lanjut, pic)
    VALUES
    ('$id_rapat', '$topik[$i]', '$pembahasan[$i]', '$tindak_lanjut[$i]', '$pic[$i]')";

    mysqli_query($conn, $sql);
  }
}

// Selesai
echo "
<script>
alert('✅ Data rapat berhasil disimpan!');
window.location='daftar_notulen.html';
</script>
";
?>
