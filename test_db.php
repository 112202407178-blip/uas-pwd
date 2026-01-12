<?php
require 'koneksi.php';
$stmt = $pdo->query('SELECT id, status FROM bookings LIMIT 5');
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['id'] . ' - ' . $row['status'] . PHP_EOL;
}
?>