<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') { die('Akses ditolak'); }
?>
<?php $page_title = 'Tambah Studio'; require 'inc/header.php'; ?>

<div class="page-header"><h1>Tambah Studio</h1></div>
<div class="card">
<form method="post" action="simpanStudio.php" enctype="multipart/form-data">
    <div class="form-group"><label>Nama</label><input type="text" name="name" required></div>
    <div class="form-group"><label>Deskripsi</label><textarea name="description"></textarea></div>
    <div class="form-group"><label>Harga per jam (angka)</label><input type="text" name="price_per_hour" required></div>
    <div class="form-group"><label>Foto (opsional)</label><input type="file" name="image"></div>
    <button class="btn" type="submit">Simpan</button>
</form>
</div>
<?php require 'inc/footer.php'; ?>