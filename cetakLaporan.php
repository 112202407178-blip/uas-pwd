<?php
session_start();
require_once 'koneksi.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') { die('Akses ditolak'); }

// CSV export
if (isset($_GET['action']) && $_GET['action'] === 'csv') {
    $stmt = $pdo->query("SELECT b.id, u.username, s.name AS studio, b.booking_date, b.start_time, b.end_time, b.total_price, b.status, b.created_at FROM bookings b JOIN users u ON b.user_id = u.id JOIN studios s ON b.studio_id = s.id ORDER BY b.created_at DESC");
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="laporan_booking_' . date('Ymd_His') . '.csv"');
    $out = fopen('php://output', 'w');
    // BOM for Excel compatibility
    fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($out, ['ID','User','Studio','Tanggal','Start','End','Total','Status','Created At']);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($out, [$row['id'],$row['username'],$row['studio'],$row['booking_date'],$row['start_time'],$row['end_time'],$row['total_price'],$row['status'],$row['created_at']]);
    }
    fclose($out);
    exit;
}

// default: show printable report
$stmt = $pdo->query("SELECT b.*, u.username, s.name AS studio_name FROM bookings b JOIN users u ON b.user_id = u.id JOIN studios s ON b.studio_id = s.id ORDER BY b.created_at DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$page_title = 'Cetak Laporan - Reservasi';
require 'inc/header.php';
?>
<div class="page-header">
  <h1>Cetak Laporan Reservasi</h1>
  <div class="actions">
    <a class="btn" href="?action=csv"><i class="fa fa-file-csv"></i> Export CSV</a>
    <a class="btn btn-outline" href="#" onclick="window.print();return false;"><i class="fa fa-print"></i> Print</a>
    <a class="btn" href="tampilReservasi.php"><i class="fa fa-arrow-left"></i> Kembali</a>
  </div>
</div>
<div class="card">
<table class="table">
  <thead>
    <tr><th>#</th><th>User</th><th>Studio</th><th>Tanggal</th><th>Waktu</th><th>Total</th><th>Status</th><th>Created</th></tr>
  </thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr>
      <td><?php echo $r['id']; ?></td>
      <td><?php echo htmlspecialchars($r['username']); ?></td>
      <td><?php echo htmlspecialchars($r['studio_name']); ?></td>
      <td><?php echo $r['booking_date']; ?></td>
      <td><?php echo $r['start_time']; ?> - <?php echo $r['end_time']; ?></td>
      <td>Rp <?php echo number_format($r['total_price'],0,',','.'); ?></td>
      <td><?php echo htmlspecialchars(ucfirst($r['status'])); ?></td>
      <td><?php echo $r['created_at']; ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php require 'inc/footer.php'; ?>