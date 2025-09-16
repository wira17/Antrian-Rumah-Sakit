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

// Statistik
$total = $conn->query("SELECT COUNT(*) AS jml FROM antrian_wira WHERE jenis='admisi'")->fetch_assoc()['jml'];
$menunggu = $conn->query("SELECT COUNT(*) AS jml FROM antrian_wira WHERE jenis='admisi' AND status='Menunggu'")->fetch_assoc()['jml'];
$dipanggil = $conn->query("SELECT COUNT(*) AS jml FROM antrian_wira WHERE jenis='admisi' AND status='Dipanggil'")->fetch_assoc()['jml'];

// Loket
$lokets = $conn->query("SELECT * FROM loket");

// Aksi panggil
if(isset($_POST['panggil'])){
    $id = $_POST['id_antrian'];
    $loket_id = $_POST['loket_id'];
    $waktu_sekarang = date('Y-m-d H:i:s');

    $conn->query("UPDATE antrian_wira 
                  SET status='Dipanggil', waktu_panggil='$waktu_sekarang', loket_id='$loket_id' 
                  WHERE id='$id'");
    header("Location: antrian_admisi.php?suara=$id");
    exit;
}

// Aksi simpan RM
if(isset($_POST['simpan_rm'])){
    $id = $_POST['id_antrian'];
    $no_rkm_medis = $_POST['no_rkm_medis'];

    $row = $conn->query("SELECT created_at FROM antrian_wira WHERE id='$id'")->fetch_assoc();
    $waktu_masuk = $row['created_at'];
    $waktu_panggil = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO riwayat_antrian_wira (no_rkm_medis, waktu_masuk, waktu_panggil) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $no_rkm_medis, $waktu_masuk, $waktu_panggil);
    $stmt->execute();
    $stmt->close();

    $conn->query("UPDATE antrian_wira SET no_rkm_medis='$no_rkm_medis' WHERE id='$id'");
    header("Location: antrian_admisi.php?notif=1");
    exit;
}

// Daftar antrian
$result = $conn->query("SELECT a.id, a.nomor, a.no_rkm_medis, a.status, a.created_at, a.waktu_panggil, l.nama_loket
                        FROM antrian_wira a
                        LEFT JOIN loket l ON a.loket_id = l.id
                        WHERE a.jenis='admisi'
                        ORDER BY a.created_at ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Antrian Admisi - RS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .page-title { font-weight: 600; margin-bottom: 25px; }
        .stat-card { border-radius: 12px; }
        .stat-icon { font-size: 2rem; opacity: 0.8; }
        .badge-menunggu { background: #ffc107; color: #212529; font-weight: 600; }
        .badge-dipanggil { background: #28a745; color: #fff; font-weight: 600; }
        .table thead { background: linear-gradient(45deg, #0d6efd, #0dcaf0); color: #fff; }
        .table-hover tbody tr:hover { background-color: #f1f8ff; }
        .btn-panggil { background: linear-gradient(45deg,#198754,#20c997); border: none; }
        .btn-panggil:hover { opacity: 0.9; }
        .btn-save { background: linear-gradient(45deg,#0d6efd,#0dcaf0); border: none; }
        .btn-save:hover { opacity: 0.9; }
    </style>
</head>
<body class="p-4">
<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title"><i class="bi bi-person-lines-fill"></i> Antrian Admisi</h2>
        <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Dashboard</a>
    </div>

    <!-- Statistik -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stat-card shadow-sm border-0 bg-primary text-white">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-list-nested stat-icon me-3"></i>
                    <div>
                        <h4 class="mb-0"><?php echo $total; ?></h4>
                        <small>Total Antrian</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm border-0 bg-warning text-dark">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-hourglass-split stat-icon me-3"></i>
                    <div>
                        <h4 class="mb-0"><?php echo $menunggu; ?></h4>
                        <small>Menunggu</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm border-0 bg-success text-white">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-mic-fill stat-icon me-3"></i>
                    <div>
                        <h4 class="mb-0"><?php echo $dipanggil; ?></h4>
                        <small>Dipanggil</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No. Antrian</th>
                            <th>No. RM</th>
                            <th>Loket</th>
                            <th>Status</th>
                            <th>Waktu Masuk</th>
                            <th>Waktu Panggil</th>
                            <th>Lama Menunggu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($row = $result->fetch_assoc()):
                        $lama_menunggu = '-';
                        $waktu_masuk = new DateTime($row['created_at']);
                        if($row['status'] == 'Dipanggil' && $row['waktu_panggil']){
                            $waktu_panggil = new DateTime($row['waktu_panggil']);
                            $interval = $waktu_masuk->diff($waktu_panggil);
                            $lama_menunggu = $interval->format('%h jam %i menit %s detik');
                        } elseif($row['status'] == 'Menunggu') {
                            $waktu_sekarang = new DateTime();
                            $interval = $waktu_masuk->diff($waktu_sekarang);
                            $lama_menunggu = $interval->format('%h jam %i menit %s detik');
                        }
                    ?>
                        <tr>
                            <td><span class="fw-bold"><?php echo $row['nomor']; ?></span></td>
                            <td><?php echo $row['no_rkm_medis'] ?: '-'; ?></td>
                            <td><?php echo $row['nama_loket'] ?? '-'; ?></td>
                            <td>
                                <span class="badge <?php echo ($row['status']=='Menunggu')?'badge-menunggu':'badge-dipanggil'; ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td><?php echo $waktu_masuk->format('d-m-Y H:i:s'); ?></td>
                            <td><?php echo ($row['waktu_panggil']) ? (new DateTime($row['waktu_panggil']))->format('d-m-Y H:i:s') : '-'; ?></td>
                            <td><?php echo $lama_menunggu; ?></td>
                            <td>
                                <?php if($row['status'] == 'Menunggu'): ?>
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
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <?php if(isset($_GET['notif'])): ?>
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div class="toast align-items-center text-bg-success border-0 show">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-check-circle-fill"></i> No. RM berhasil disimpan!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Notifikasi suara -->
<?php
if(isset($_GET['suara'])){
    $id = $_GET['suara'];
    $data = $conn->query("SELECT a.nomor, l.nama_loket 
                          FROM antrian_wira a 
                          LEFT JOIN loket l ON a.loket_id=l.id 
                          WHERE a.id='$id'")->fetch_assoc();
    if($data):
?>
<script>
    const nomor = "<?php echo $data['nomor']; ?>";
    const loket = "<?php echo $data['nama_loket'] ?? '-'; ?>";
    const msg = `Antrian nomor ${nomor}, silahkan ke loket ${loket}`;
    function speak(text){
        const synth = window.speechSynthesis;
        let utterThis = new SpeechSynthesisUtterance(text);
        utterThis.lang = 'id-ID';
        utterThis.rate = 0.9;
        utterThis.pitch = 1;
        synth.speak(utterThis);
    }
    setTimeout(() => speak(msg), 500);
</script>
<?php endif; } ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
