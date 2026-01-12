<?php
session_start();
require_once 'koneksi.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'GET') { echo json_encode(['success'=>false,'message'=>'Method not allowed']); exit; }
$date = $_GET['date'] ?? '';
$start = $_GET['start'] ?? '';
$end = $_GET['end'] ?? '';
if (!$date || !$start || !$end) { echo json_encode(['success'=>false,'message'=>'Missing params']); exit; }
try {
    $startDT = new DateTime($date . ' ' . $start);
    $endDT = new DateTime($date . ' ' . $end);
    if ($endDT <= $startDT) $endDT->modify('+1 day');
} catch (Exception $ex) {
    echo json_encode(['success'=>false,'message'=>'Invalid date/time']); exit;
}
// Find conflicting bookings (pending or approved)
$sql = "SELECT b.id,b.studio_id,b.booking_date,b.start_time,b.end_time FROM bookings b WHERE b.status IN ('pending','approved')";
$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$conflicts = [];
foreach ($rows as $r) {
    $bkStart = new DateTime($r['booking_date'] . ' ' . $r['start_time']);
    $bkEnd = new DateTime($r['booking_date'] . ' ' . $r['end_time']);
    if ($bkEnd <= $bkStart) $bkEnd->modify('+1 day');
    if (!($endDT <= $bkStart || $startDT >= $bkEnd)) {
        $conflicts[$r['studio_id']][] = ['start'=>$r['start_time'],'end'=>$r['end_time'],'id'=>$r['id']];
    }
}
$out = [];
foreach ($conflicts as $sid => $list) {
    $out[] = ['studio_id'=>$sid,'bookings'=>$list];
}
echo json_encode(['success'=>true,'data'=>$out]);
exit;
?>