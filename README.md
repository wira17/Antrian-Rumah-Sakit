# 🏥 Sistem Antrian Poli Klinik / Rumah Sakit

Aplikasi web sederhana untuk menampilkan **display antrian poli klinik** dengan:
- 🎥 Video/Youtube display di layar tunggu
- 📊 Statistik total antrian
- 👤 Informasi pasien yang sedang dipanggil
- 🔊 Notifikasi suara otomatis ketika nomor antrian baru dipanggil
- ⏰ Jam digital & running text di footer

---

## ⚙️ Fitur Utama
1. **Antrian Poli**
   - Data antrian tersimpan di tabel `antrian_poli_wira`
   - Kolom penting: `id`, `kd_poli`, `no_reg`, `nama_pasien`, `jam_panggil`
   - Menyimpan waktu kapan pasien dipanggil

2. **Display TV Poli (`display_tv_poli.php`)**
   - Menampilkan total antrian
   - Menampilkan nomor & nama pasien yang sedang dilayani
   - Auto update setiap 2 detik via AJAX
   - Membaca notifikasi suara dengan **Web Speech API**

3. **API (`get_antrian_poli.php`)**
   - Mengembalikan data JSON:
     ```json
     {
       "total_antrian": 12,
       "sedang_dilayani": {
         "no_reg": "A05",
         "nama_pasien": "Budi Santoso"
       }
     }
     ```

---

## 🗄️ Struktur Tabel
```sql
CREATE TABLE antrian_poli_wira (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kd_poli VARCHAR(10) NOT NULL,
    no_reg VARCHAR(10) NOT NULL,
    nama_pasien VARCHAR(100) NOT NULL,
    jam_panggil DATETIME DEFAULT CURRENT_TIMESTAMP
);
🚀 Cara Menjalankan
Clone repository ini ke htdocs (XAMPP) atau www (Laragon)

bash
Copy code
git clone https://github.com/username/antrian-poli.git
Import database:

Buat database antrian

Import file antrian.sql (jika tersedia)

Konfigurasi database di config.php

php
Copy code
<?php
$conn = new mysqli("localhost","root","","antrian");
if($conn->connect_error){
    die("Koneksi gagal: ".$conn->connect_error);
}
?>
Jalankan di browser:

arduino
Copy code
http://localhost/antrian/display_tv_poli.php?kd_poli=U001
📷 Tampilan
Display TV Poli

Panel kiri: Video informasi (YouTube)

Panel kanan: Total antrian & pasien sedang dilayani

Footer: Running text + jam digital

🔊 Notifikasi Suara
Jika ada nomor baru dipanggil, otomatis keluar suara:

"Nomor antrian A05, atas nama Budi Santoso, silahkan masuk ke ruang pemeriksaan."
