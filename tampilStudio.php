<?php
session_start();
require_once 'koneksi.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') { die('Akses ditolak'); }
$studios = $pdo->query('SELECT * FROM studios ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<?php $page_title = 'Kelola Studio - Admin'; require 'inc/header.php'; ?>

<div class="page-header">
  <h1>Kelola Studio</h1>
  <div class="actions">
    <a class="btn" href="daftarStudio.php">Lihat Situs</a>
    <a class="btn" href="tambahStudio.php">Tambah Studio</a>
    <a class="btn" href="logout.php">Logout</a>
  </div>
</div>
<div class="card">
<table class="table">
    <thead><tr><th>#</th><th>Nama</th><th>Harga / jam</th><th>Foto</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($studios as $s): ?>
    <tr>
        <td><?php echo $s['id']; ?></td>
        <td><?php echo htmlspecialchars($s['name']); ?></td>
        <td>Rp <?php echo number_format($s['price_per_hour'],0,',','.'); ?></td>
        <td><?php if($s['image'] && file_exists('img_studio/'.$s['image'])): ?><img src="img_studio/<?php echo htmlspecialchars($s['image']); ?>" class="thumb"><?php else: ?>-<?php endif; ?></td>
        <td>
            <a class="btn" href="koreksi.php?id=<?php echo $s['id']; ?>"><i class="fa fa-edit"></i> Edit</a>
            <a class="btn btn-danger" data-confirm="Hapus studio ini?" href="hapus.php?id=<?php echo $s['id']; ?>"><i class="fa fa-trash"></i> Hapus</a>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php require 'inc/footer.php'; ?>