<?php 
session_start();
if(!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';
date_default_timezone_set('Asia/Jakarta');

// Tanggal hari ini
$date_today = date("Y-m-d");

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Hitung total data hari ini
$total_data = $conn->query("SELECT COUNT(*) AS jml FROM antrian_wira WHERE jenis='admisi' AND DATE(created_at)='$date_today'")->fetch_assoc()['jml'];
$total_pages = ceil($total_data / $limit);

// Statistik
$total = $total_data;
$menunggu = $conn->query("SELECT COUNT(*) AS jml FROM antrian_wira WHERE jenis='admisi' AND status='Menunggu' AND DATE(created_at)='$date_today'")->fetch_assoc()['jml'];
$dipanggil = $conn->query("SELECT COUNT(*) AS jml FROM antrian_wira WHERE jenis='admisi' AND status='Dipanggil' AND DATE(created_at)='$date_today'")->fetch_assoc()['jml'];

// Loket
$lokets = $conn->query("SELECT * FROM loket");

// Aksi panggil
if(isset($_POST['panggil'])){
    $id = $_POST['id_antrian'];
    $loket_id = $_POST['loket_id'];
    $waktu_sekarang = date('Y-m-d H:i:s');

    $conn->query("UPDATE antrian_wira SET status='Dipanggil', waktu_panggil='$waktu_sekarang', loket_id='$loket_id' WHERE id='$id'");
    header("Location: antrian_admisi.php?suara=$id&page=$page");
    exit;
}

// Aksi simpan RM
if(isset($_POST['simpan_rm'])){
    $id = $_POST['id_antrian'];
    $no_rkm_medis = trim($_POST['no_rkm_medis']);
    if($no_rkm_medis == ""){
        header("Location: antrian_admisi.php?error=NoRMkosong&page=$page"); exit;
    }

    $row = $conn->query("SELECT nomor, created_at FROM antrian_wira WHERE id='$id'")->fetch_assoc();
    if(!$row){
        header("Location: antrian_admisi.php?error=AntrianTidakDitemukan&page=$page"); exit;
    }

    $stmt = $conn->prepare("INSERT INTO riwayat_antrian_wira (id_antrian, nomor_antrian, no_rkm_medis, waktu_masuk, waktu_panggil) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $id, $row['nomor'], $no_rkm_medis, $row['created_at'], date('Y-m-d H:i:s'));
    $stmt->execute(); $stmt->close();

    $stmt2 = $conn->prepare("UPDATE antrian_wira SET no_rkm_medis=? WHERE id=?");
    $stmt2->bind_param("si", $no_rkm_medis, $id);
    $stmt2->execute(); $stmt2->close();

    header("Location: antrian_admisi.php?notif=1&page=$page");
    exit;
}

// Reset antrian
if(isset($_POST['reset_antrian'])){
    $conn->query("DELETE FROM antrian_wira WHERE jenis='admisi' AND DATE(created_at)='$date_today'");
    header("Location: antrian_admisi.php?reset=1"); exit;
}

// Ambil daftar antrian
$result = $conn->query("SELECT a.id, a.nomor, a.no_rkm_medis, a.status, a.created_at, a.waktu_panggil, l.nama_loket
                        FROM antrian_wira a
                        LEFT JOIN loket l ON a.loket_id = l.id
                        WHERE a.jenis='admisi' AND DATE(a.created_at)='$date_today'
                        ORDER BY a.created_at ASC
                        LIMIT $limit OFFSET $offset");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Antrian Admisi - RS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background: #eef2f7; font-family: 'Segoe UI', sans-serif; }
        .page-title { font-weight: 700; }
        .stat-card { border-radius: 14px; transition: 0.3s; }
        .stat-card:hover { transform: translateY(-4px); }
        .table thead { background: linear-gradient(90deg,#0d6efd,#0dcaf0); color: #fff; }
        .table-striped tbody tr:nth-of-type(odd){ background-color: #f8fbff; }
        .badge-menunggu { background: #ffc107; color: #212529; }
        .badge-dipanggil { background: #28a745; }
        .btn-panggil { background: linear-gradient(45deg,#198754,#20c997); border:none; }
        .btn-save { background: linear-gradient(45deg,#0d6efd,#0dcaf0); border:none; }
    </style>
</head>
<body class="p-4">
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title text-primary"><i class="bi bi-people-fill"></i> Antrian Admisi</h2>
        <div>
            <form method="POST" class="d-inline">
                <button type="submit" name="reset_antrian" class="btn btn-danger">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </button>
            </form>
            <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-house-door"></i> Dashboard</a>
        </div>
    </div>

    <!-- Statistik -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stat-card shadow bg-primary text-white">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-list-ul fs-1 me-3"></i>
                    <div><h4><?php echo $total; ?></h4><small>Total Antrian</small></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow bg-warning text-dark">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-hourglass-split fs-1 me-3"></i>
                    <div><h4><?php echo $menunggu; ?></h4><small>Menunggu</small></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow bg-success text-white">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-mic-fill fs-1 me-3"></i>
                    <div><h4><?php echo $dipanggil; ?></h4><small>Dipanggil</small></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel -->
    <div class="card shadow border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>No. Antrian</th>
                            <th>No. RM</th>
                            <th>Loket</th>
                            <th>Status</th>
                            <th>Masuk</th>
                            <th>Panggil</th>
                            <th>Lama Tunggu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if($result->num_rows > 0): 
                        while($row = $result->fetch_assoc()):
                            $lama = "-";
                            $waktu_masuk = new DateTime($row['created_at']);
                            if($row['status']=="Dipanggil" && $row['waktu_panggil']){
                                $interval = $waktu_masuk->diff(new DateTime($row['waktu_panggil']));
                                $lama = $interval->format('%h jam %i mnt %s dtk');
                            } elseif($row['status']=="Menunggu"){
                                $interval = $waktu_masuk->diff(new DateTime());
                                $lama = $interval->format('%h jam %i mnt %s dtk');
                            }
                    ?>
                        <tr>
                            <td class="fw-bold"><?php echo $row['nomor']; ?></td>
                            <td><?php echo $row['no_rkm_medis'] ?: '-'; ?></td>
                            <td><?php echo $row['nama_loket'] ?: '-'; ?></td>
                            <td>
                                <span class="badge <?php echo ($row['status']=='Menunggu')?'badge-menunggu':'badge-dipanggil'; ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td><?php echo $waktu_masuk->format('d-m-Y H:i:s'); ?></td>
                            <td><?php echo ($row['waktu_panggil']) ? (new DateTime($row['waktu_panggil']))->format('d-m-Y H:i:s') : '-'; ?></td>
                            <td><?php echo $lama; ?></td>
                            <td>
                                <?php if($row['status']=="Menunggu"): ?>
                                    <form method="POST" class="d-flex gap-2">
                                        <input type="hidden" name="id_antrian" value="<?php echo $row['id']; ?>">
                                        <select name="loket_id" class="form-select form-select-sm" required>
                                            <option value="">Pilih Loket</option>
                                            <?php 
                                            $lokets2 = $conn->query("SELECT * FROM loket");
                                            while($l = $lokets2->fetch_assoc()): ?>
                                                <option value="<?php echo $l['id']; ?>"><?php echo $l['nama_loket']; ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                        <button type="submit" name="panggil" class="btn btn-panggil btn-sm text-white">
                                            <i class="bi bi-mic-fill"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" class="d-flex gap-2">
                                        <input type="hidden" name="id_antrian" value="<?php echo $row['id']; ?>">
                                        <input type="text" name="no_rkm_medis" class="form-control form-control-sm" placeholder="No. RM" required>
                                        <button type="submit" name="simpan_rm" class="btn btn-save btn-sm text-white">
                                            <i class="bi bi-save"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="8" class="text-center text-muted">Belum ada antrian hari ini</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if($total_pages > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php if($page <= 1) echo 'disabled'; ?>">
                        <a class="page-link" href="?page=<?php echo $page-1; ?>">&laquo;</a>
                    </li>
                    <?php for($i=1;$i<=$total_pages;$i++): ?>
                        <li class="page-item <?php if($page==$i) echo 'active'; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php if($page >= $total_pages) echo 'disabled'; ?>">
                        <a class="page-link" href="?page=<?php echo $page+1; ?>">&raquo;</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Toast -->
<?php if(isset($_GET['notif'])): ?>
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div class="toast align-items-center text-bg-success border-0 show">
        <div class="d-flex"><div class="toast-body"><i class="bi bi-check-circle-fill"></i> No. RM disimpan!</div></div>
    </div>
</div>
<?php endif; ?>
<?php if(isset($_GET['reset'])): ?>
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div class="toast align-items-center text-bg-danger border-0 show">
        <div class="d-flex"><div class="toast-body"><i class="bi bi-arrow-counterclockwise"></i> Antrian di-reset!</div></div>
    </div>
</div>
<?php endif; ?>

<!-- Notifikasi Suara -->
<?php
if(isset($_GET['suara'])){
    $id = $_GET['suara'];
    $data = $conn->query("SELECT a.nomor, l.nama_loket FROM antrian_wira a LEFT JOIN loket l ON a.loket_id=l.id WHERE a.id='$id'")->fetch_assoc();
    if($data):
?>
<script>
    const msg = `Antrian nomor <?php echo $data['nomor']; ?>, silakan ke <?php echo $data['nama_loket']; ?>`;
    const utter = new SpeechSynthesisUtterance(msg);
    utter.lang = "id-ID"; utter.rate = 0.9;
    setTimeout(()=>speechSynthesis.speak(utter),500);
</script>
<?php endif; } ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
