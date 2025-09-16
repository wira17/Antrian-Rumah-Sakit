<?php
session_start();
if(!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';

// Ambil poli dari URL
$kd_poli = isset($_GET['kd_poli']) ? $_GET['kd_poli'] : '';
if($kd_poli == ''){
    die("⚠️ Harap sertakan parameter ?kd_poli=xxx di URL");
}

// Ambil nama poli
$poli = $conn->query("SELECT nm_poli FROM poliklinik WHERE kd_poli='$kd_poli'")->fetch_assoc();
$nm_poli = $poli['nm_poli'] ?? $kd_poli;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Display Antrian Poli <?php echo $nm_poli; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            margin:0; padding:0; font-family:'Roboto', sans-serif;
            background:linear-gradient(135deg,#1c1c1c,#2c2c2c); color:#fff;
        }
        .header { text-align:center; padding:15px; background:linear-gradient(90deg,#ff9f1c,#ff4040);}
        .header h1 { font-size:2rem; font-weight:900; color:#fff;}
        .header h2 { font-size:1.2rem; margin-top:5px; color:#fff;}
        .main-container { display:flex; flex-wrap:wrap; justify-content:center; gap:20px; padding:20px;}
        .video-box { flex:2 1 600px; background:#000; border-radius:20px; overflow:hidden; min-height:340px;
            display:flex; align-items:center; justify-content:center; box-shadow:0 0 20px rgba(255,159,28,0.6);}
        .video-box iframe { width:100%; height:100%; border:0; border-radius:20px;}
        .info-boxes { flex:1 1 300px; display:flex; flex-direction:column; gap:20px;}
        .info-card { padding:7px; border-radius:20px; text-align:center; display:flex; flex-direction:column; justify-content:center;
            box-shadow:0 5px 20px rgba(0,0,0,0.6);}
        .info-card.total { background:linear-gradient(135deg,#0a0a0a,#1c1c1c); border:2px solid #ff9f1c;}
        .info-card.dilayani { background:linear-gradient(135deg,#0a0a0a,#1c1c1c); border:2px solid #ff4040;}
        .info-card i { font-size:3rem; margin-bottom:15px; color:#ff9f1c;}
        .info-card div { font-size:1.4rem; font-weight:700; margin-bottom:15px; color:#fff;}
        .info-card h3 { font-size:5rem; font-weight:900; color:#ff9f1c; margin:0;}
        .info-card h4 { font-size:2rem; font-weight:700; color:#fff; margin-top:10px;}
        footer { position:fixed; bottom:0; width:100%; background:#111; padding:10px 20px; display:flex; justify-content:space-between; align-items:center; font-size:1.1rem;}
        .running-text { overflow:hidden; white-space:nowrap; flex:1;}
        .running-text span { display:inline-block; padding-left:100%; animation:marquee 20s linear infinite; font-weight:600; color:#ff9f1c;}
        @keyframes marquee { 0%{transform:translateX(0);} 100%{transform:translateX(-100%);} }
        .clock { font-weight:bold; font-size:1.3rem; color:#ff9f1c; display:flex; align-items:center; gap:5px;}
    </style>
</head>
<body>

<div class="header">
    <h1><i class="bi bi-hospital"></i> Display Antrian Poli</h1>
    <h2><?php echo strtoupper($nm_poli); ?></h2>
</div>

<div class="main-container">
    <div class="video-box">
        <iframe 
            src="https://www.youtube.com/embed/vhWC_wVvwWU?autoplay=1&loop=1&playlist=vhWC_wVvwWU&controls=1" 
            allow="autoplay; fullscreen" allowfullscreen>
        </iframe>
    </div>
    <div class="info-boxes">
        <div class="info-card total">
            <i class="bi bi-list-task"></i>
            <div>Total Antrian</div>
            <h3 id="total_antrian">0</h3>
        </div>
        <div class="info-card dilayani">
            <i class="bi bi-person-check"></i>
            <div>Sedang Dilayani</div>
            <h3 id="antrian_terlayani">-</h3>
            <h4 id="nama_pasien">-</h4>
        </div>
    </div>
</div>

<footer>
    <div class="running-text">
        <span>Selamat datang di Poli <?php echo $nm_poli; ?> - Harap menunggu dengan tertib.</span>
    </div>
    <div class="clock"><i class="bi bi-clock-history"></i> <span id="clock"></span></div>
</footer>

<script>
let lastAntrian = "-";
const kd_poli = "<?php echo $kd_poli; ?>";

function updateClock(){
    const now = new Date();
    document.getElementById('clock').textContent = 
        String(now.getHours()).padStart(2,'0')+":"+String(now.getMinutes()).padStart(2,'0')+":"+String(now.getSeconds()).padStart(2,'0');
}
setInterval(updateClock,1000); updateClock();

function speak(text) {
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'id-ID';
    utterance.rate = 0.9;
    speechSynthesis.speak(utterance);
}

function updateAntrian() {
    fetch('get_antrian_poli.php?kd_poli='+kd_poli)
    .then(response => response.json())
    .then(data => {
        document.getElementById('total_antrian').textContent = data.total_antrian;
        document.getElementById('antrian_terlayani').textContent = data.sedang_dilayani.no_reg;
        document.getElementById('nama_pasien').textContent = data.sedang_dilayani.nama_pasien;

        if (data.sedang_dilayani.no_reg !== lastAntrian && data.sedang_dilayani.no_reg !== '-') {
            let pesan = "Nomor antrian " + data.sedang_dilayani.no_reg + 
                        ", atas nama " + data.sedang_dilayani.nama_pasien + 
                        ", silahkan masuk ke ruang pemeriksaan";
            speak(pesan);
            lastAntrian = data.sedang_dilayani.no_reg;
        }
    })
    .catch(err => console.error(err));
}
setInterval(updateAntrian, 2000);
updateAntrian();
</script>
</body>
</html>
