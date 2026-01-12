<?php
session_start();
require_once 'koneksi.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') { die('Akses ditolak'); }
$bookings = $pdo->query('SELECT b.*, u.username, s.name AS studio_name FROM bookings b JOIN users u ON b.user_id = u.id JOIN studios s ON b.studio_id = s.id ORDER BY b.created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<?php $page_title = 'Reservasi - Admin'; require 'inc/header.php'; ?>

<div class="page-header">
  <h1>Daftar Reservasi</h1>
  <div class="actions">
    <a class="btn" href="tampilStudio.php">Kelola Studio</a>
    <a class="btn" href="cetakLaporan.php">Cetak Laporan</a>
    <a class="btn" href="logout.php">Logout</a>
  </div>
</div>
<div class="card">
<table class="table">
    <thead><tr><th>#</th><th>User</th><th>Studio</th><th>Tanggal</th><th>Waktu</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($bookings as $b): ?>
    <tr>
        <td><?php echo $b['id']; ?></td>
        <td><?php echo htmlspecialchars($b['username']); ?></td>
        <td><?php echo htmlspecialchars($b['studio_name']); ?></td>
        <td><?php echo $b['booking_date']; ?></td>
        <td><?php echo $b['start_time']; ?> - <?php echo $b['end_time']; ?></td>
        <td>Rp <?php echo number_format($b['total_price'],0,',','.'); ?></td>
        <td><?php
            $s = $b['status'];
            $map = ['pending'=>'badge-pending','approved'=>'badge-approved','rejected'=>'badge-rejected','completed'=>'badge-completed','cancelled'=>'badge-cancelled'];
            $cls = $map[$s] ?? 'badge-pending';
        ?>
        <span class="badge <?php echo $cls; ?>"><?php echo htmlspecialchars(ucfirst($s)); ?></span>
        </td>
        <td>
            <?php if ($b['status'] === 'pending'): ?>
                <a class="btn" href="updateStatusBooking.php?id=<?php echo $b['id']; ?>&status=approved"><i class="fa fa-check"></i> Setujui</a>
                <a class="btn btn-danger" data-confirm="Tolak booking ini?" href="updateStatusBooking.php?id=<?php echo $b['id']; ?>&status=rejected"><i class="fa fa-times"></i> Tolak</a>
            <?php elseif ($b['status'] === 'approved'): ?>
                <a class="btn" data-confirm="Tandai booking ini selesai?" href="updateStatusBooking.php?id=<?php echo $b['id']; ?>&status=completed"><i class="fa fa-flag-checkered"></i> Selesai</a>
            <?php else: ?>-
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php require 'inc/footer.php'; ?>