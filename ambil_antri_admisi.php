<?php 
session_start();
if(!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

// Koneksi ke database
include 'config.php';

// Ambil nomor terakhir hari ini untuk jenis 'admisi'
$last = $conn->query("
    SELECT nomor 
    FROM antrian_wira 
    WHERE jenis='admisi' 
      AND DATE(created_at) = CURDATE()
    ORDER BY nomor DESC 
    LIMIT 1
")->fetch_assoc();

$nomor_terakhir = $last['nomor'] ?? 0;

// Nomor baru = nomor terakhir + 1 (jika belum ada, mulai dari 1)
$nomor_baru = $nomor_terakhir + 1;

// Simpan ke tabel antrian
$stmt = $conn->prepare("
    INSERT INTO antrian_wira (nomor, jenis, status, created_at) 
    VALUES (?, 'admisi', 'Menunggu', NOW())
");
$stmt->bind_param("i", $nomor_baru);
$stmt->execute();
$stmt->close();

// Redirect kembali ke halaman display
header("Location: antrian_admisi_display.php");
exit;
?>
