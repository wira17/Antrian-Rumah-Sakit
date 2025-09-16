<?php
include 'config.php';
session_start();

$message = '';

if(isset($_POST['register'])){
    $nik = $_POST['nik'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek apakah email atau NIK sudah terdaftar
    $check = $conn->query("SELECT * FROM users WHERE email='$email' OR nik='$nik'");
    if($check->num_rows > 0){
        $message = "NIK atau Email sudah terdaftar!";
    } else {
        $conn->query("INSERT INTO users (nik, nama, email, password) VALUES ('$nik','$nama','$email','$password')");
        $message = "Registrasi berhasil! <a href='login.php'>Login di sini</a>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Sistem Antrian RS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-5">
    <h2>Register Akun</h2>

    <?php if($message != ''){ echo "<div class='alert alert-info'>$message</div>"; } ?>

    <form method="post">
        <div class="mb-3">
            <label>NIK</label>
            <input type="text" name="nik" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" name="register" class="btn btn-primary">Register</button>
        <a href="login.php" class="btn btn-link">Login</a>
    </form>
</body>
</html>
