<?php
session_start();
require_once 'koneksi.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') { die('Akses ditolak'); }
$id = intval($_GET['id'] ?? 0);
if (!$id) die('ID tidak valid');
$stmt = $pdo->prepare('SELECT * FROM studios WHERE id = ?');
$stmt->execute([$id]);
$studio = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$studio) die('Studio tidak ditemukan');
?>
<?php $page_title = 'Edit Studio'; require 'inc/header.php'; ?>

<div class="page-header"><h1>Edit Studio</h1></div>
<div class="card">
<form method="post" action="simpanKoreksiData.php" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $studio['id']; ?>">
    <div class="form-group"><label>Nama</label><input type="text" name="name" value="<?php echo htmlspecialchars($studio['name']); ?>" required></div>
    <div class="form-group"><label>Deskripsi</label><textarea name="description"><?php echo htmlspecialchars($studio['description']); ?></textarea></div>
    <div class="form-group"><label>Harga per jam</label><input type="text" name="price_per_hour" value="<?php echo htmlspecialchars($studio['price_per_hour']); ?>" required></div>
    <div class="form-group"><label>Foto (ganti jika perlu)</label><input type="file" name="image"></div>
    <?php if ($studio['image'] && file_exists('img_studio/'.$studio['image'])): ?><p>Foto saat ini:<br><img src="img_studio/<?php echo htmlspecialchars($studio['image']); ?>" class="thumb"></p><?php endif; ?>
    <button class="btn" type="submit">Simpan Perubahan</button>
</form>
</div>
<?php require 'inc/footer.php'; ?>