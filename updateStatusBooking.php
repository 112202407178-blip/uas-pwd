<?php
session_start();
require_once 'koneksi.php';
$log = function($msg){ file_put_contents(__DIR__.'/debug_update_status.log', date('[Y-m-d H:i:s] ').$msg.PHP_EOL, FILE_APPEND); };
$log('REQUEST: '.json_encode(['ip'=>$_SERVER['REMOTE_ADDR'] ?? '', 'user'=>$_SESSION['user_id'] ?? null, 'role'=>$_SESSION['role'] ?? null, 'get'=>$_GET]));
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') { $log('ACCESS_DENIED: user='.($_SESSION['user_id'] ?? 'null').' role='.($_SESSION['role'] ?? 'null')); die('Akses ditolak'); }
$id = intval($_GET['id'] ?? 0);
$status = $_GET['status'] ?? '';
$allowed = ['approved','rejected','completed'];
if (!$id || !in_array($status, $allowed)) {
    $log('INVALID_REQUEST: id='.$id.' status='.($status));
    $_SESSION['flash'] = 'Permintaan tidak valid.';
    header('Location: tampilReservasi.php'); exit;
} 
// ambil booking
$stmt = $pdo->prepare('SELECT * FROM bookings WHERE id = ?');
$stmt->execute([$id]);
$bk = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$bk) {
    $log('NOT_FOUND: id='.$id);
    $_SESSION['flash'] = 'Booking tidak ditemukan.';
    header('Location: tampilReservasi.php'); exit;
} 
// jika setuju, cek konflik dengan booking yang sudah approved
if ($status === 'approved') {
    $check = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE studio_id = ? AND booking_date = ? AND status = 'approved' AND NOT (end_time <= ? OR start_time >= ?) AND id != ?");
    $check->execute([$bk['studio_id'], $bk['booking_date'], $bk['start_time'], $bk['end_time'], $id]);
    $cnt = (int) $check->fetchColumn();
    if ($cnt > 0) {
        $log('CONFLICT: id='.$id.' cnt='.$cnt.' studio='.$bk['studio_id'].' date='.$bk['booking_date'].' start='.$bk['start_time'].' end='.$bk['end_time']);
        $_SESSION['flash'] = 'Tidak dapat menyetujui: ada booking lain yang sudah disetujui pada waktu yang sama.';
        header('Location: tampilReservasi.php'); exit;
    } 
}
$u = $pdo->prepare('UPDATE bookings SET status = ? WHERE id = ?');
$u->execute([$status, $id]);
$log('UPDATED: id='.$id.' status='.$status.' rows='.$u->rowCount());
$_SESSION['flash'] = 'Status booking diubah menjadi ' . $status . '.';
header('Location: tampilReservasi.php'); exit;
?>