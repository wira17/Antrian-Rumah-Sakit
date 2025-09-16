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


?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Sistem Antrian RS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            transition: 0.3s;
        }
        .card-icon {
            font-size: 2.5rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Selamat datang, <?php echo $_SESSION['nama']; ?></h2>
            <a href="logout.php" class="btn btn-danger">Logout <i class="bi bi-box-arrow-right"></i></a>
        </div>

        <h4 class="mb-3">Menu Antrian</h4>
        <div class="row g-4">

            <!-- Antrian Admisi -->
            <div class="col-md-3">
                <a href="antrian_admisi.php" class="text-decoration-none text-dark">
                    <div class="card card-hover text-center p-3">
                        <i class="bi bi-person-lines-fill card-icon"></i>
                        <h5 class="mt-3">Antrian Admisi</h5>
                    </div>
                </a>
            </div>

            <!-- Antrian Poli -->
            <div class="col-md-3">
                <a href="antrian_poli.php" class="text-decoration-none text-dark">
                    <div class="card card-hover text-center p-3">
                        <i class="bi bi-hospital card-icon"></i>
                        <h5 class="mt-3">Antrian Poli</h5>
                    </div>
                </a>
            </div>

            <!-- Antrian Farmasi -->
            <div class="col-md-3">
                <a href="antrian_farmasi.php" class="text-decoration-none text-dark">
                    <div class="card card-hover text-center p-3">
                        <i class="bi bi-capsule card-icon"></i>
                        <h5 class="mt-3">Antrian Farmasi</h5>
                    </div>
                </a>
            </div>

            <!-- Display Antrian Admisi -->
            <div class="col-md-3">
                <a href="antrian_admisi_display.php" class="text-decoration-none text-dark" target="_blank">
                    <div class="card card-hover text-center p-3">
                        <i class="bi bi-eye card-icon"></i>
                        <h5 class="mt-3">Display Antrian Admisi</h5>
                    </div>
                </a>
            </div>

               <div class="col-md-3">
                <a href="display_tv_admisi.php" class="text-decoration-none text-dark" target="_blank">
                    <div class="card card-hover text-center p-3">
                        <i class="bi bi-eye card-icon"></i>
                        <h5 class="mt-3">Display TV Admisi</h5>
                    </div>
                </a>
            </div>


               <div class="col-md-3">
                <a href="display_tv_poli.php" class="text-decoration-none text-dark" target="_blank">
                    <div class="card card-hover text-center p-3">
                        <i class="bi bi-eye card-icon"></i>
                        <h5 class="mt-3">Display TV Poli</h5>
                    </div>
                </a>
            </div>



            <!-- Master Loket -->
            <div class="col-md-3">
                <a href="master_loket.php" class="text-decoration-none text-dark">
                    <div class="card card-hover text-center p-3">
                        <i class="bi bi-building card-icon"></i>
                        <h5 class="mt-3">Master Loket</h5>
                    </div>
                </a>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
