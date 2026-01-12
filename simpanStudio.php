<?php
session_start();
require_once 'koneksi.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') { die('Akses ditolak'); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: tampilStudio.php'); exit; }

$name = trim($_POST['name'] ?? '');
$desc = trim($_POST['description'] ?? '');
$price = floatval($_POST['price_per_hour'] ?? 0);
$imageName = null;

if (!empty($_FILES['image']['name'])) {
    $f = $_FILES['image'];
    $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
    $imageName = uniqid('studio_') . '.' . $ext;
    move_uploaded_file($f['tmp_name'], __DIR__ . '/img_studio/' . $imageName);
}

$ins = $pdo->prepare('INSERT INTO studios (name, description, price_per_hour, image) VALUES (?, ?, ?, ?)');
$ins->execute([$name, $desc, $price, $imageName]);
header('Location: tampilStudio.php');
exit;
?>