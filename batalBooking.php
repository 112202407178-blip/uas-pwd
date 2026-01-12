<?php
session_start();
require_once 'koneksi.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$id = intval($_GET['id'] ?? 0);
if (!$id) die('ID booking tidak valid');

// Pastikan booking milik user
$stmt = $pdo->prepare('SELECT user_id, status FROM bookings WHERE id = ?');
$stmt->execute([$id]);
$bk = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$bk) die('Booking tidak ditemukan');
if ($bk['user_id'] != $_SESSION['user_id']) die('Anda tidak berhak membatalkan booking ini');
if (!in_array($bk['status'], ['pending','approved'])) die('Booking tidak dapat dibatalkan');

$u = $pdo->prepare('UPDATE bookings SET status = ? WHERE id = ?');
$u->execute(['cancelled', $id]);
header('Location: riwayatBooking.php');
exit;
?>