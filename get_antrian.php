<?php
include 'config.php';
header('Content-Type: application/json');

// Tanggal hari ini
$date_today = date("Y-m-d");

// Ambil total antrian HARI INI
$total_antrian = $conn->query("
    SELECT COUNT(*) AS total 
    FROM antrian_wira 
    WHERE jenis='admisi' 
      AND DATE(created_at)='$date_today'
")->fetch_assoc()['total'];

// Ambil nomor antrian terakhir yang sedang dipanggil HARI INI
$row = $conn->query("
    SELECT nomor 
    FROM antrian_wira 
    WHERE jenis='admisi' 
      AND status='Dipanggil' 
      AND DATE(waktu_panggil)='$date_today'
    ORDER BY waktu_panggil DESC 
    LIMIT 1
")->fetch_assoc();

$antrian_terlayani = $row['nomor'] ?? '-';

// Kembalikan sebagai JSON
echo json_encode([
    'total_antrian' => $total_antrian,
    'antrian_terlayani' => $antrian_terlayani
]);
