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


$message = '';

if(isset($_POST['simpan'])){
    $loket = $_POST['loket'];

    // Simpan ke tabel loket
    $conn->query("INSERT INTO loket (nama_loket) VALUES ('$loket')");
    $message = "Loket berhasil ditambahkan!";
}

// Ambil daftar loket
$lokets = $conn->query("SELECT * FROM loket");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Master Loket - Sistem Antrian RS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-5 bg-light">
    <h2>Master Loket</h2>
    <a href="index.php" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>

    <?php if($message != ''){ echo "<div class='alert alert-success'>$message</div>"; } ?>

    <form method="post" class="mb-4">
        <div class="mb-3">
            <label>Nama Loket</label>
            <input type="text" name="loket" class="form-control" required>
        </div>
        <button type="submit" name="simpan" class="btn btn-primary">Simpan Loket</button>
    </form>

    <h4>Daftar Loket</h4>
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Nama Loket</th>
        </tr>
        <?php 
        $no = 1;
        while($row = $lokets->fetch_assoc()){ 
        ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $row['nama_loket']; ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
