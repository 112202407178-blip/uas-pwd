<?php
session_start();
require_once 'koneksi.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$studio_id = intval($_GET['studio_id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM studios WHERE id = ?');
$stmt->execute([$studio_id]);
$studio = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$studio) {
    die('Studio tidak ditemukan');
}
?>
<?php $page_title = 'Booking - ' . htmlspecialchars($studio['name']); require 'inc/header.php'; ?>

<div class="page-header"><h1>Booking: <?php echo htmlspecialchars($studio['name']); ?></h1></div>
<div class="card">
<form method="post" action="simpanBooking.php">
    <input type="hidden" name="studio_id" value="<?php echo $studio['id']; ?>">
    <div class="form-group">
        <label>Tanggal</label>
        <input type="date" name="booking_date" required>
    </div>
    <div class="form-group">
        <label>Waktu Mulai</label>
        <input type="time" name="start_time" required>
    </div>
    <div class="form-group">
        <label>Waktu Selesai</label>
        <input type="time" name="end_time" required>
    </div>
    <button class="btn" type="submit">Kirim Booking</button>
</form>
</div>
<?php require 'inc/footer.php'; ?>