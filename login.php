<?php
session_start();
include 'config.php';

$message = '';

// Proses login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_user = $conn->real_escape_string($_POST['id_user']);
    $password = $conn->real_escape_string($_POST['password']);

    $sql = "SELECT * FROM user 
            WHERE id_user = AES_ENCRYPT('$id_user','nur') 
            AND password = AES_ENCRYPT('$password','windi') 
            LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $_SESSION['id_user'] = $id_user;

        $getPetugas = $conn->query("SELECT nip, nama FROM petugas WHERE nip = '$id_user'");
        if ($getPetugas && $getPetugas->num_rows > 0) {
            $petugas = $getPetugas->fetch_assoc();
            $_SESSION['nip'] = $petugas['nip'];
            $_SESSION['nama_petugas'] = $petugas['nama'];
        } else {
            $_SESSION['nip'] = 'ADMIN';
            $_SESSION['nama_petugas'] = 'Administrator';
        }

        header("Location: index.php");
        exit();
    } else {
        $message = "ID User atau Password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>System Antrian - Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Icon -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background: url('assets/img/hospital-bg.jpg') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .login-container {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      backdrop-filter: blur(4px);
    }
    .login-box {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 1.2rem;
      box-shadow: 0 8px 30px rgba(0,0,0,0.3);
      padding: 40px 35px;
      max-width: 400px;
      width: 100%;
      text-align: center;
    }
    .login-box .logo {
      font-size: 2rem;
      font-weight: 700;
      color: #007bff;
      margin-bottom: 10px;
    }
    .login-box .subtitle {
      font-size: 1.1rem;
      color: #666;
      margin-bottom: 25px;
    }
    .form-control {
      border-radius: 0.8rem;
      padding: 12px 15px;
    }
    .btn-login {
      border-radius: 0.8rem;
      font-weight: 600;
      font-size: 1.1rem;
      padding: 10px 0;
    }
    .icon-input {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #888;
    }
    .form-group {
      position: relative;
    }
  </style>
</head>
<body>
<div class="login-container">
  <div class="login-box">
    <div class="logo">
      <i class="fa-solid fa-hospital-user"></i> System Antrian
    </div>
    <div class="subtitle">RS Permata Hati - Bungo</div>
    <?php if ($message): ?>
      <div class="alert alert-danger"><?= $message ?></div>
    <?php endif; ?>
    <form action="login.php" method="post">
      <div class="form-group mb-3">
        <input type="text" name="id_user" class="form-control" placeholder="ID User" required>
        <i class="fa-solid fa-user icon-input"></i>
      </div>
      <div class="form-group mb-4">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
        <i class="fa-solid fa-lock icon-input"></i>
      </div>
      <button type="submit" class="btn btn-primary w-100 btn-login">
        <i class="fa-solid fa-right-to-bracket"></i> Login
      </button>
    </form>
    <div class="mt-3">
      <small class="text-muted">© <?= date("Y") ?> RS Permata Hati - Bungo</small>
    </div>
  </div>
</div>
</body>
</html>
