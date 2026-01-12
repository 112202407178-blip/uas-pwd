<?php
session_start();
require_once 'koneksi.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$user_id = $_SESSION['user_id'];
$bookings = $pdo->prepare('SELECT b.*, s.name AS studio_name FROM bookings b JOIN studios s ON b.studio_id = s.id WHERE b.user_id = ? ORDER BY b.created_at DESC');
$bookings->execute([$user_id]);
$rows = $bookings->fetchAll(PDO::FETCH_ASSOC);
?>
<?php $page_title = 'Riwayat Booking'; require 'inc/header.php'; ?>

<div class="page-header">
  <h1>Riwayat Booking</h1>
  <div class="actions">
    <a class="btn" href="daftarStudio.php">Kembali</a>
    <a class="btn" href="logout.php">Logout</a>
  </div>
</div>
<div class="card">
<table class="table">
    <thead><tr><th>#</th><th>Studio</th><th>Tanggal</th><th>Waktu</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
        <tr>
            <td><?php echo $r['id']; ?></td>
            <td><?php echo htmlspecialchars($r['studio_name']); ?></td>
            <td><?php echo $r['booking_date']; ?></td>
            <td><?php echo $r['start_time']; ?> - <?php echo $r['end_time']; ?></td>
            <td>Rp <?php echo number_format($r['total_price'],0,',','.'); ?></td>
            <td><?php
                $s = $r['status'];
                $map = ['pending'=>'badge-pending','approved'=>'badge-approved','rejected'=>'badge-rejected','completed'=>'badge-completed','cancelled'=>'badge-cancelled'];
                $cls = $map[$s] ?? 'badge-pending';
            ?>
            <span class="badge <?php echo $cls; ?>"><?php echo htmlspecialchars(ucfirst($s)); ?></span>
            </td>
            <td>
                <?php if (in_array($r['status'], ['pending','approved'])): ?>
                    <a class="btn btn-danger" href="batalBooking.php?id=<?php echo $r['id']; ?>" onclick="return confirm('Batalkan booking?')">Batalkan</a>
                <?php else: ?>-
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php require 'inc/footer.php'; ?>