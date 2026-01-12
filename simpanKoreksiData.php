<?php
session_start();
require_once 'koneksi.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') { die('Akses ditolak'); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: tampilStudio.php'); exit; }

$id = intval($_POST['id']);
$name = trim($_POST['name'] ?? '');
$desc = trim($_POST['description'] ?? '');
$price = floatval($_POST['price_per_hour'] ?? 0);

// Ambil data lama
$stmt = $pdo->prepare('SELECT image FROM studios WHERE id = ?');
$stmt->execute([$id]);
$old = $stmt->fetch(PDO::FETCH_ASSOC);
$imageName = $old['image'];

if (!empty($_FILES['image']['name'])) {
    $f = $_FILES['image'];
    $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
    $imageName = uniqid('studio_') . '.' . $ext;
    move_uploaded_file($f['tmp_name'], __DIR__ . '/img_studio/' . $imageName);
    // hapus gambar lama
    if ($old['image'] && file_exists(__DIR__ . '/img_studio/' . $old['image'])) {
        @unlink(__DIR__ . '/img_studio/' . $old['image']);
    }
}

$u = $pdo->prepare('UPDATE studios SET name = ?, description = ?, price_per_hour = ?, image = ? WHERE id = ?');
$u->execute([$name, $desc, $price, $imageName, $id]);
header('Location: tampilStudio.php');
exit;
?>