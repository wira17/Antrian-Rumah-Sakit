<?php 
session_start();
if(!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Ambil poli untuk filter
$polis = $conn->query("SELECT kd_poli, nm_poli FROM poliklinik ORDER BY nm_poli");

// Filter poli
$filter_poli = isset($_GET['kd_poli']) ? $_GET['kd_poli'] : '';

// Statistik berdasarkan hari ini
$where = "WHERE rp.tgl_registrasi = CURDATE()"; 
if($filter_poli != ''){
    $where .= " AND rp.kd_poli = '$filter_poli'";
}

$total = $conn->query("SELECT COUNT(*) AS jml 
                       FROM reg_periksa rp $where")->fetch_assoc()['jml'];
$belum = $conn->query("SELECT COUNT(*) AS jml 
                       FROM reg_periksa rp $where AND rp.stts='Belum'")->fetch_assoc()['jml'];
$sudah = $conn->query("SELECT COUNT(*) AS jml 
                       FROM reg_periksa rp $where AND rp.stts='Sudah'")->fetch_assoc()['jml'];

// Aksi panggil poli
if(isset($_POST['panggil'])){
    $no_rawat = $_POST['no_rawat'];
    
    // update reg_periksa
    $conn->query("UPDATE reg_periksa SET stts='Sudah' WHERE no_rawat='$no_rawat'");
    
    // ambil data untuk disimpan ke antrian_poli_wira
    $q = $conn->query("SELECT rp.no_reg, rp.no_rawat, rp.no_rkm_medis, p.nm_pasien, 
                              rp.kd_poli, pl.nm_poli 
                       FROM reg_periksa rp 
                       JOIN pasien p ON rp.no_rkm_medis=p.no_rkm_medis
                       JOIN poliklinik pl ON rp.kd_poli=pl.kd_poli
                       WHERE rp.no_rawat='$no_rawat'");
    $data = $q->fetch_assoc();
    
    // simpan log ke antrian_poli_wira
    $stmt = $conn->prepare("INSERT INTO antrian_poli_wira 
        (no_rawat,no_reg,no_rkm_medis,nama_pasien,kd_poli,nm_poli,status,jam_panggil) 
        VALUES (?,?,?,?,?,?, 'Sudah', NOW())");
    $stmt->bind_param("ssssss", 
        $data['no_rawat'],
        $data['no_reg'],
        $data['no_rkm_medis'],
        $data['nm_pasien'],
        $data['kd_poli'],
        $data['nm_poli']
    );
    $stmt->execute();
    
    header("Location: antrian_poli.php?suara=$no_rawat&kd_poli=$filter_poli");
    exit;
}

// Aksi panggil lagi
if(isset($_POST['panggil_lagi'])){
    $no_rawat = $_POST['no_rawat'];

    // cek apakah sudah ada di antrian_poli_wira
    $cek = $conn->query("SELECT id FROM antrian_poli_wira WHERE no_rawat='$no_rawat' LIMIT 1");

    if($cek && $cek->num_rows > 0){
        // sudah ada → update jam_panggil terbaru
        $conn->query("UPDATE antrian_poli_wira 
                      SET jam_panggil = NOW(), status='Sudah' 
                      WHERE no_rawat='$no_rawat'");
    } else {
        // belum ada → ambil data dari reg_periksa lalu insert baru
        $q = $conn->query("SELECT rp.no_reg, rp.no_rawat, rp.no_rkm_medis, p.nm_pasien, 
                                  rp.kd_poli, pl.nm_poli 
                           FROM reg_periksa rp 
                           JOIN pasien p ON rp.no_rkm_medis=p.no_rkm_medis
                           JOIN poliklinik pl ON rp.kd_poli=pl.kd_poli
                           WHERE rp.no_rawat='$no_rawat'");
        if($q && $q->num_rows > 0){
            $data = $q->fetch_assoc();

            $stmt = $conn->prepare("INSERT INTO antrian_poli_wira 
                (no_rawat,no_reg,no_rkm_medis,nama_pasien,kd_poli,nm_poli,status,jam_panggil) 
                VALUES (?,?,?,?,?,?, 'Sudah', NOW())");
            $stmt->bind_param("ssssss", 
                $data['no_rawat'],
                $data['no_reg'],
                $data['no_rkm_medis'],
                $data['nm_pasien'],
                $data['kd_poli'],
                $data['nm_poli']
            );
            $stmt->execute();
        }
    }

    header("Location: antrian_poli.php?suara=$no_rawat&kd_poli=$filter_poli");
    exit;
}


// Daftar antrian hari ini
$result = $conn->query("
    SELECT rp.no_reg, rp.no_rawat, rp.no_rkm_medis, rp.tgl_registrasi, rp.jam_reg, 
           rp.stts, p.nm_pasien, pl.nm_poli
    FROM reg_periksa rp
    JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
    JOIN poliklinik pl ON rp.kd_poli = pl.kd_poli
    $where
    ORDER BY rp.no_reg ASC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Antrian Poli - RS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .page-title { font-weight: 600; margin-bottom: 25px; }
        .stat-card { border-radius: 12px; }
        .stat-icon { font-size: 2rem; opacity: 0.8; }
        .badge-belum { background: #ffc107; color: #212529; font-weight: 600; }
        .badge-sudah { background: #28a745; color: #fff; font-weight: 600; }
        .table thead { background: linear-gradient(45deg, #0d6efd, #0dcaf0); color: #fff; }
        .table-hover tbody tr:hover { background-color: #f1f8ff; }
        .btn-panggil { background: linear-gradient(45deg,#198754,#20c997); border: none; }
        .btn-panggil:hover { opacity: 0.9; }
    </style>
</head>
<body class="p-4">
<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title"><i class="bi bi-hospital"></i> Antrian Poli (<?php echo date('d-m-Y'); ?>)</h2>
        <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Dashboard</a>
    </div>

    <!-- Filter Poli -->
    <form method="GET" class="mb-3 d-flex align-items-center">
        <label class="me-2 fw-bold">Filter Poli:</label>
        <select name="kd_poli" class="form-select w-auto me-2" onchange="this.form.submit()">
            <option value="">-- Semua Poli --</option>
            <?php while($pl = $polis->fetch_assoc()): ?>
                <option value="<?php echo $pl['kd_poli']; ?>" 
                    <?php if($pl['kd_poli']==$filter_poli) echo 'selected'; ?>>
                    <?php echo $pl['nm_poli']; ?>
                </option>
            <?php endwhile; ?>
        </select>
        <noscript><button type="submit" class="btn btn-primary">Terapkan</button></noscript>
    </form>

    <!-- Statistik -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stat-card shadow-sm border-0 bg-primary text-white">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-list-nested stat-icon me-3"></i>
                    <div>
                        <h4 class="mb-0"><?php echo $total; ?></h4>
                        <small>Total Registrasi</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm border-0 bg-warning text-dark">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-hourglass-split stat-icon me-3"></i>
                    <div>
                        <h4 class="mb-0"><?php echo $belum; ?></h4>
                        <small>Belum Dipanggil</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm border-0 bg-success text-white">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-mic-fill stat-icon me-3"></i>
                    <div>
                        <h4 class="mb-0"><?php echo $sudah; ?></h4>
                        <small>Sudah Dipanggil</small>
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
                            <th>No. Antri Poli</th>
                            <th>No. RM</th>
                            <th>Nama Pasien</th>
                            <th>Poli</th>
                            <th>Status</th>
                            <th>Jam</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><span class="fw-bold"><?php echo $row['no_reg']; ?></span></td>
                            <td><?php echo $row['no_rkm_medis']; ?></td>
                            <td><?php echo $row['nm_pasien']; ?></td>
                            <td><?php echo $row['nm_poli']; ?></td>
                            <td>
                                <span class="badge <?php echo ($row['stts']=='Belum')?'badge-belum':'badge-sudah'; ?>">
                                    <?php echo $row['stts']; ?>
                                </span>
                            </td>
                            <td><?php echo $row['jam_reg']; ?></td>
                            <td>
                                <?php if($row['stts']=='Belum'): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="no_rawat" value="<?php echo $row['no_rawat']; ?>">
                                        <button type="submit" name="panggil" class="btn btn-panggil btn-sm text-white">
                                            <i class="bi bi-mic-fill"></i> Panggil
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="no_rawat" value="<?php echo $row['no_rawat']; ?>">
                                        <button type="submit" name="panggil_lagi" class="btn btn-warning btn-sm text-dark">
                                            <i class="bi bi-megaphone-fill"></i> Panggil Lagi
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted">Tidak ada data hari ini</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Notifikasi suara -->
<?php
if(isset($_GET['suara'])){
    $no_rawat = $_GET['suara'];
    $data = $conn->query("SELECT p.nm_pasien, pl.nm_poli 
                          FROM reg_periksa rp
                          JOIN pasien p ON rp.no_rkm_medis=p.no_rkm_medis
                          JOIN poliklinik pl ON rp.kd_poli=pl.kd_poli
                          WHERE rp.no_rawat='$no_rawat'")->fetch_assoc();
    if($data):
?>
<script>
    const nama = "<?php echo $data['nm_pasien']; ?>";
    const poli = "<?php echo $data['nm_poli']; ?>";
    const msg = `Pasien atas nama ${nama}, silakan menuju  ${poli}`;
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
