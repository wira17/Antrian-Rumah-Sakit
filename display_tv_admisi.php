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



// Ambil data awal
$total_antrian = $conn->query("SELECT COUNT(*) AS total FROM antrian_wira WHERE jenis='admisi'")->fetch_assoc()['total'];
$antrian_terlayani = $conn->query(
    "SELECT nomor FROM antrian_wira 
     WHERE jenis='admisi' AND status='Dipanggil' 
     ORDER BY waktu_panggil DESC LIMIT 1"
)->fetch_assoc()['nomor'] ?: '-';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Display Antrian - RS Permata Hati</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap');

        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg,#1c1c1c,#2c2c2c);
            color: #fff;
        }

        .header {
            text-align: center;
            padding: 15px 0;
            background: linear-gradient(90deg, #00ffc3, #1e90ff);
            box-shadow: 0 5px 15px rgba(0,0,0,0.5);
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 900;
            color: #fff;
        }

        .main-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        .video-box {
            flex: 2 1 600px;
            background: #000;
            border-radius: 20px;
            overflow: hidden;
            min-height: 340px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px rgba(0,255,195,0.5);
        }

        .video-box iframe {
            width: 100%;
            height: 100%;
            border: 0;
            border-radius: 20px;
        }

        .info-boxes {
            flex: 1 1 300px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .info-card {
            padding: 7px;
            border-radius: 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.6);
        }

        .info-card.total { 
            background: linear-gradient(135deg,#0a0a0a,#1c1c1c); 
            border: 2px solid #1e90ff;
        }
        .info-card.dilayani { 
            background: linear-gradient(135deg,#0a0a0a,#1c1c1c); 
            border: 2px solid #ff6347;
        }

        .info-card i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #00ffc3;
        }

        .info-card div {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #fff;
        }

        .info-card h3 {
            font-size: 5rem;
            font-weight: 900;
            color: #00ffc3;
        }

        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: #111;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.1rem;
            box-shadow: 0 -5px 15px rgba(0,0,0,0.5);
        }

        .running-text {
            overflow: hidden;
            white-space: nowrap;
            flex: 1;
        }

        .running-text span {
            display: inline-block;
            padding-left: 100%;
            animation: marquee 20s linear infinite;
            font-weight: 600;
            color: #00ffc3;
        }

        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-100%); }
        }

        .clock-copyright {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .clock {
            font-weight: bold;
            font-size: 1.3rem;
            color: #00ffc3;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .copyright {
            font-size: 0.95rem;
            color: #ccc;
            margin-top: 2px;
        }

        @media (max-width: 992px) {
            .main-container {
                flex-direction: column;
                align-items: center;
            }
            .video-box, .info-boxes {
                flex: 1 1 100%;
            }
            footer {
                flex-direction: column;
                gap: 8px;
                align-items: flex-start;
            }
            .clock-copyright {
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>

<div class="header">
    <h1><i class="bi bi-display"></i> Display Antrian RS Permata Hati</h1>
</div>

<div class="main-container">
    <!-- Video Box -->
    <div class="video-box">
        <iframe 
            src="https://www.youtube.com/embed/vhWC_wVvwWU?autoplay=1&loop=1&playlist=vhWC_wVvwWU&controls=1" 
            allow="autoplay; fullscreen" 
            allowfullscreen>
        </iframe>
    </div>

    <!-- Info Box -->
    <div class="info-boxes">
        <div class="info-card total">
            <i class="bi bi-list-task"></i>
            <div>Total Antrian</div>
            <h3 id="total_antrian"><?php echo $total_antrian; ?></h3>
        </div>
        <div class="info-card dilayani">
            <i class="bi bi-person-check"></i>
            <div>Sedang Dilayani</div>
            <h3 id="antrian_terlayani"><?php echo $antrian_terlayani; ?></h3>
        </div>
    </div>
</div>

<footer>
    <div class="running-text">
        <span>Selamat datang di Rumah Sakit Permata Hati - Mohon tetap menjaga ketertiban dan protokol kesehatan.</span>
    </div>
    <div class="clock-copyright">
        <div class="clock"><i class="bi bi-clock-history"></i> <span id="clock"></span></div>
        <div class="copyright">&copy; 2025 RS Permata Hati</div>
    </div>
</footer>

<script>
let lastAntrian = "<?php echo $antrian_terlayani; ?>"; // simpan nilai awal

// Jam digital
function updateClock(){
    const now = new Date();
    const hours = String(now.getHours()).padStart(2,'0');
    const minutes = String(now.getMinutes()).padStart(2,'0');
    const seconds = String(now.getSeconds()).padStart(2,'0');
    document.getElementById('clock').textContent = hours + ':' + minutes + ':' + seconds;
}
setInterval(updateClock,1000);
updateClock();

// Fungsi untuk Text-to-Speech
function speak(text) {
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'id-ID';  // Bahasa Indonesia
    utterance.rate = 0.9;      // Kecepatan bicara
    speechSynthesis.speak(utterance);
}

// Update nomor antrian otomatis
function updateAntrian() {
    fetch('get_antrian.php')
    .then(response => response.json())
    .then(data => {
        document.getElementById('total_antrian').textContent = data.total_antrian;
        document.getElementById('antrian_terlayani').textContent = data.antrian_terlayani;

        // Jika ada nomor baru dipanggil → TTS bicara
        if (data.antrian_terlayani !== lastAntrian && data.antrian_terlayani !== '-') {
           let pesan = "Antrian nomor " + data.antrian_terlayani + ", silahkan ke loket 1";

            speak(pesan);
            lastAntrian = data.antrian_terlayani;
        }
    })
    .catch(err => console.error(err));
}

// Update setiap 2 detik
setInterval(updateAntrian, 2000);
updateAntrian();
</script>


</body>
</html>
