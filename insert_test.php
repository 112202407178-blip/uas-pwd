<?php
require 'koneksi.php';
$pdo->exec("INSERT INTO bookings (user_id, studio_id, booking_date, start_time, end_time, total_price, status) VALUES (1, 1, '2026-01-15', '10:00', '12:00', 100000, 'pending')");
echo 'Inserted';
?>