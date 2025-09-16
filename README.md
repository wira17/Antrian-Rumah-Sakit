# 🏥 Sistem Antrian Klinik/Rumah Sakit

Aplikasi web sederhana untuk **sistem antrian rumah sakit/klinik**.  
Mendukung:
- 📊 Antrian loket/admisi/farmasi
- 🏥 Antrian Poli Klinik
- 🔊 Notifikasi suara otomatis saat antrian dipanggil
- 🎥 Tampilan display TV poli (YouTube + informasi pasien)
- ⏰ Jam digital & running text di layar

---

## ⚙️ Fitur Utama
1. **Antrian Admisi**
   - Menangani antrian di loket, admisi, farmasi, dsb.
   - Menyimpan waktu panggil untuk setiap pasien.

2. **Antrian Poli**
   - Menampilkan pasien yang dipanggil di layar TV poli.
   - Menyimpan nomor antrian dan nama pasien.
   - Mendukung suara otomatis ketika pasien dipanggil.

3. **Display TV Poli**
   - Panel kiri: Video informasi (YouTube)
   - Panel kanan: Total antrian + pasien sedang dilayani
   - Running text di bagian bawah
   - Jam digital real-time
   - Auto-refresh data setiap 2 detik

4. **API Antrian Poli**
   - Disediakan endpoint JSON untuk mengambil data antrian terbaru.
   - Dipakai oleh `display_tv_poli.php` untuk update data otomatis.

---

## 🚀 Cara Menjalankan
1. Clone repository ke folder webserver (XAMPP: `htdocs/` atau Laragon: `www/`).
Import database (struktur tabel disediakan terpisah).

Sesuaikan konfigurasi database di config.php

Jalankan aplikasi:

Display TV Poli

arduino
Copy code
http://localhost/antrian/display_tv_poli.php?kd_poli=U001
API antrian poli

arduino
Copy code
http://localhost/antrian/get_antrian_poli.php?kd_poli=U001
🔊 Notifikasi Suara
Jika ada antrian baru dipanggil, sistem otomatis berbicara:

"Nomor antrian poli A05, silahkan masuk ke ruang pemeriksaan."

Proses pengembangan antrian farmasi + display antrian + pemanggil antrian
Untuk tambahan tabel via Japri : 082177846209
