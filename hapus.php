<?php
session_start();
require_once 'koneksi.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') { die('Akses ditolak'); }
$id = intval($_GET['id'] ?? 0);
if (!$id) die('ID tidak valid');
$stmt = $pdo->prepare('SELECT image FROM studios WHERE id = ?');
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row && $row['image'] && file_exists(__DIR__ . '/img_studio/' . $row['image'])) {
    @unlink(__DIR__ . '/img_studio/' . $row['image']);
}
$del = $pdo->prepare('DELETE FROM studios WHERE id = ?');
$del->execute([$id]);
header('Location: tampilStudio.php');
exit;
?>