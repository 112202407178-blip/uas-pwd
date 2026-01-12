<?php
session_start();
require_once 'koneksi.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: daftarStudio.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$studio_id = intval($_POST['studio_id'] ?? 0);
$booking_date = $_POST['booking_date'] ?? '';
$start_time = $_POST['start_time'] ?? '';
$end_time = $_POST['end_time'] ?? '';

// Basic validation
$errors = [];
if (!$studio_id) $errors[] = 'Studio tidak valid.';
if (!$booking_date || !$start_time || !$end_time) $errors[] = 'Semua field harus diisi.';

if ($errors) {
    if (!empty($_POST['ajax']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
        header('Content-Type: application/json');
        echo json_encode(['success'=>false,'message'=>implode(' ', $errors)]);
        exit;
    }
    foreach ($errors as $e) echo '<p style="color:red">'.htmlspecialchars($e).'</p>'; 
    echo '<p><a href="javascript:history.back()">Kembali</a></p>';
    exit;
}

// Parse datetimes and support overnight bookings (end next day if earlier than start)
try {
    $startDateTime = new DateTime($booking_date . ' ' . $start_time);
    $endDateTime = new DateTime($booking_date . ' ' . $end_time);
    if ($endDateTime <= $startDateTime) {
        // assume booking goes past midnight into the next day
        $endDateTime->modify('+1 day');
    }
} catch (Exception $ex) {
    if (!empty($_POST['ajax']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
        header('Content-Type: application/json');
        echo json_encode(['success'=>false,'message'=>'Format tanggal/waktu tidak valid.']);
        exit;
    }
    echo '<p style="color:red">Format tanggal/waktu tidak valid.</p><p><a href="javascript:history.back()">Kembali</a></p>';
    exit;
}

// Use SQL-based overlap check (handles overnight via DATE_ADD)
$startDT = $startDateTime->format('Y-m-d H:i:s');
$endDT = $endDateTime->format('Y-m-d H:i:s');
$sql = "SELECT COUNT(*) FROM bookings b WHERE b.studio_id = ? AND b.status IN ('pending','approved') AND NOT ( DATE_ADD(CONCAT(b.booking_date,' ',b.end_time), INTERVAL (b.end_time <= b.start_time) DAY) <= ? OR CONCAT(b.booking_date,' ',b.start_time) >= ? )";
$ch = $pdo->prepare($sql);
$ch->execute([$studio_id, $startDT, $endDT]);
if ($ch->fetchColumn() > 0) {
    $msg = 'Waktu sudah terisi/terkonflik, silakan pilih waktu lain.';
    if (!empty($_POST['ajax']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
        header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>$msg]); exit;
    }
    echo '<p style="color:red">'.$msg.'</p>';
    echo '<p><a href="javascript:history.back()">Kembali</a></p>';
    exit;
}

// Dapatkan harga studio
$s = $pdo->prepare('SELECT price_per_hour FROM studios WHERE id = ?');
$s->execute([$studio_id]);
$studio = $s->fetch(PDO::FETCH_ASSOC);
if (!$studio) die('Studio tidak ditemukan');

// Hitung durasi (dalam jam) — dukung jika melewati tengah malam
$startDT = new DateTime($booking_date . ' ' . $start_time);
$endDT = new DateTime($booking_date . ' ' . $end_time);
if ($endDT <= $startDT) {
    $endDT->modify('+1 day');
}
$diffSec = $endDT->getTimestamp() - $startDT->getTimestamp();
if ($diffSec <= 0) {
    echo '<p style="color:red">Waktu tidak valid.</p><p><a href="javascript:history.back()">Kembali</a></p>';
    exit;
}
$hours = $diffSec / 3600.0;
$total_price = round($hours * floatval($studio['price_per_hour']), 2);

$ins = $pdo->prepare('INSERT INTO bookings (user_id, studio_id, booking_date, start_time, end_time, total_price, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
$ins->execute([$user_id, $studio_id, $booking_date, $start_time, $end_time, $total_price, 'pending']);

if (!empty($_POST['ajax']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
    header('Content-Type: application/json');
    echo json_encode(['success'=>true,'message'=>'Booking berhasil.']);
    exit;
}
header('Location: riwayatBooking.php');
exit;
?>