<?php 
session_start();
if(!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

// Koneksi ke database
include 'config.php';

// Tanggal hari ini
$date_today = date("Y-m-d");

// Ambil nomor terakhir antrian Admisi **hanya untuk hari ini**
$last = $conn->query("SELECT a.nomor, a.status, l.nama_loket, a.created_at 
                      FROM antrian_wira a
                      LEFT JOIN loket l ON a.loket_id = l.id
                      WHERE a.jenis='admisi' 
                        AND DATE(a.created_at) = '$date_today'
                      ORDER BY a.nomor DESC
                      LIMIT 1")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Display Antrian Admisi - RS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
            padding: 20px;
        }
        .display-card {
            background: #fff;
            border-radius: 1.5rem;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-width: 600px;
            width: 100%;
            margin-bottom: 20px;
            animation: fadeIn 1s ease;
        }
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(30px);}
            to {opacity: 1; transform: translateY(0);}
        }
        .display-card h1 {
            font-size: 9rem;
            font-weight: 900;
            margin-bottom: 15px;
            color: #0d6efd;
            text-shadow: 2px 4px 6px rgba(0,0,0,0.2);
        }
        .display-card h3 {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #444;
        }
        .status {
            margin-top: 15px;
        }
        .badge-menunggu {
            background-color: #ffc107;
            color: #212529;
            font-weight: 600;
            font-size: 1.2rem;
            padding: 0.6em 1.2em;
            border-radius: 1rem;
        }
        .badge-dipanggil {
            background-color: #28a745;
            color: #fff;
            font-weight: 600;
            font-size: 1.2rem;
            padding: 0.6em 1.2em;
            border-radius: 1rem;
        }
        .icon-big {
            font-size: 4rem;
            color: #0d6efd;
            margin-bottom: 10px;
        }
        .btn-ambil {
            font-size: 1.4rem;
            font-weight: 600;
            padding: 12px 28px;
            border-radius: 1.2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transition: transform 0.2s ease;
        }
        .btn-ambil:hover {
            transform: scale(1.05);
        }
        footer {
            margin-top: 30px;
            font-size: 0.9rem;
            color: #f8f9fa;
        }
    </style>
</head>
<body>

<!-- Card Nomor Antrian Terakhir -->
<div class="display-card">
    <i class="bi bi-ticket-perforated-fill icon-big"></i>
    <h3>Nomor Antrian Terakhir</h3>
    <h1><?php echo $last['nomor'] ?? '-'; ?></h1>
    <div class="status">
        <?php if($last): ?>
            <?php if($last['status'] == 'Menunggu'): ?>
                <span class="badge badge-menunggu"><i class="bi bi-hourglass-split"></i> Menunggu</span>
            <?php else: ?>
                <span class="badge badge-dipanggil"><i class="bi bi-megaphone-fill"></i> Dipanggil (Loket <?php echo $last['nama_loket'] ?? '-'; ?>)</span>
            <?php endif; ?>
        <?php else: ?>
            <span class="text-muted">Belum ada antrian</span>
        <?php endif; ?>
    </div>
</div>

<!-- Form Ambil Nomor Antrian -->
<div class="display-card">
    <i class="bi bi-person-plus-fill icon-big"></i>
    <h3>Ambil Nomor Antrian Baru</h3>
    <form action="ambil_antri_admisi.php" method="post">
        <button type="submit" class="btn btn-primary btn-ambil">
            <i class="bi bi-plus-circle"></i> Ambil Nomor
        </button>
    </form>
    <small class="text-muted d-block mt-3"><i class="bi bi-info-circle"></i> Loket akan ditentukan oleh petugas saat pemanggilan</small>
</div>

<footer>
    <i class="bi bi-hospital"></i> Sistem Antrian Admisi - Rumah Sakit
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
