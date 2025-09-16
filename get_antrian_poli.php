<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';

// Ambil parameter poli
$kd_poli = isset($_GET['kd_poli']) ? $_GET['kd_poli'] : '';

$total_antrian = 0;
$no_reg = "-";
$nama_pasien = "-";

// Hitung total antrian per poli (hari ini)
$sql = "SELECT COUNT(*) AS total 
        FROM antrian_poli_wira 
        WHERE DATE(jam_panggil)=CURDATE()";
if($kd_poli != ''){
    $sql .= " AND kd_poli='$kd_poli'";
}
$result = $conn->query($sql);
if($result){
    $row = $result->fetch_assoc();
    $total_antrian = $row['total'] ?? 0;
}

// Ambil terakhir dipanggil
$sql2 = "SELECT no_reg, nama_pasien 
         FROM antrian_poli_wira 
         WHERE DATE(jam_panggil)=CURDATE()";
if($kd_poli != ''){
    $sql2 .= " AND kd_poli='$kd_poli'";
}
$sql2 .= " ORDER BY jam_panggil DESC LIMIT 1";

$result2 = $conn->query($sql2);
if($result2 && $result2->num_rows > 0){
    $row2 = $result2->fetch_assoc();
    $no_reg = $row2['no_reg'] ?? "-";
    $nama_pasien = $row2['nama_pasien'] ?? "-";
}

// Output JSON
echo json_encode([
    "total_antrian" => $total_antrian,
    "sedang_dilayani" => [
        "no_reg" => $no_reg,
        "nama_pasien" => $nama_pasien
    ]
]);
