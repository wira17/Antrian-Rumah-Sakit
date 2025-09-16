<?php
include 'config.php';
header('Content-Type: application/json');

// Ambil total antrian
$total_antrian = $conn->query("SELECT COUNT(*) AS total FROM antrian WHERE jenis='admisi'")->fetch_assoc()['total'];

// Ambil nomor antrian terakhir yang sedang dilayani
$antrian_terlayani = $conn->query(
    "SELECT nomor FROM antrian 
     WHERE jenis='admisi' AND status='Dipanggil' 
     ORDER BY waktu_panggil DESC LIMIT 1"
)->fetch_assoc()['nomor'] ?: '-';

// Kembalikan sebagai JSON
echo json_encode([
    'total_antrian' => $total_antrian,
    'antrian_terlayani' => $antrian_terlayani
]);
