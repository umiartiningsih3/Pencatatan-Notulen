<?php
include 'koneksi.php';

$id = $_GET['id'];
$r = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM rapat WHERE id='$id'"));
$h = mysqli_query($conn,"SELECT * FROM hasil_rapat WHERE rapat_id='$id'");
?>
<html>
<head>
<title>Detail Notulen</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
<h3><?= $r['judul'] ?></h3>
<p><b>Tanggal:</b> <?= $r['tanggal'] ?></p>
<p><b>Notulis:</b> <?= $r['notulis'] ?></p>

<table class="table table-bordered">
<tr><th>No</th><th>Topik</th><th>Pembahasan</th><th>Tindak</th><th>PIC</th></tr>

<?php $no=1; while($row=mysqli_fetch_assoc($h)) { ?>
<tr>
  <td><?= $no++ ?></td>
  <td><?= $row['topik'] ?></td>
  <td><?= $row['pembahasan'] ?></td>
  <td><?= $row['tindak_lanjut'] ?></td>
  <td><?= $row['pic'] ?></td>
</tr>
<?php } ?>

</table>

<a href="daftar_notulen.php" class="btn btn-secondary">Kembali</a>
</div>
</body>
</html>
