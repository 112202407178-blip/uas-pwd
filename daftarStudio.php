<?php
session_start();
require_once 'koneksi.php';

$logged = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? null;

$studios = $pdo->query('SELECT * FROM studios ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<?php $page_title = 'Daftar Studio - Sistem Reservasi'; require 'inc/header.php'; ?>

<div class="page-header">
  <h1>Daftar Studio</h1>
  <div class="actions">
    <div class="availability-filters" style="display:flex;align-items:center">
      <button class="btn" id="open-filter"><i class="fa fa-calendar"></i> Cek Ketersediaan</button>
    </div>
    <?php if ($logged): ?>
      <span class="muted">Halo, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
      <a class="btn" href="riwayatBooking.php">Riwayat Booking</a>
      <?php if ($role === 'admin'): ?>
        <a class="btn" href="tampilStudio.php">Kelola Studio</a>
        <a class="btn" href="tampilReservasi.php">Reservasi (Admin)</a>
      <?php endif; ?>
      <a class="btn" href="logout.php">Logout</a>
    <?php else: ?>
      <a class="btn" href="login.php">Login</a>
      <a class="btn" href="register.php">Daftar</a>
    <?php endif; ?>
  </div>
</div>

<div class="studio-grid">
  <?php foreach ($studios as $s): ?>
    <article class="studio-card card" data-studio-id="<?php echo $s['id']; ?>">
      <div class="studio-image">
        <?php if ($s['image'] && file_exists('img_studio/' . $s['image'])): ?>
          <img src="img_studio/<?php echo htmlspecialchars($s['image']); ?>" alt="<?php echo htmlspecialchars($s['name']); ?>">
        <?php else: ?>
          <div class="studio-noimg">No image</div>
        <?php endif; ?>
      </div>
      <div class="studio-body">
        <h3 class="studio-title"><?php echo htmlspecialchars($s['name']); ?></h3>
        <p class="studio-desc"><?php echo htmlspecialchars(mb_strimwidth($s['description'] ?? '', 0, 160, '...')); ?></p>
      </div>
      <div class="studio-footer">
        <div class="studio-price">Rp <?php echo number_format($s['price_per_hour'], 0, ',', '.'); ?>/jam</div>
        <div class="studio-actions">
          <a class="btn open-booking" href="formBooking.php?studio_id=<?php echo $s['id']; ?>" data-studio-id="<?php echo $s['id']; ?>" data-studio-name="<?php echo htmlspecialchars($s['name']); ?>" data-studio-price="<?php echo number_format($s['price_per_hour'],0,',','.'); ?>"><i class="fa fa-calendar-plus"></i> Booking</a>
        </div>
      </div>
    </article>
  <?php endforeach; ?>
</div>
<?php require 'inc/footer.php'; ?>