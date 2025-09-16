🏥 Sistem Antrian Klinik/Rumah Sakit

Aplikasi web sederhana untuk sistem antrian rumah sakit/klinik.
Mendukung:

📊 Antrian loket/admisi/farmasi

🏥 Antrian Poli Klinik

🔊 Notifikasi suara otomatis saat antrian dipanggil

🎥 Tampilan display TV poli (YouTube + informasi pasien)

⏰ Jam digital & running text di layar

⚙️ Fitur Utama
1. Antrian Admisi

Menangani antrian di loket, admisi, farmasi, dsb.

Menyimpan waktu panggil untuk setiap pasien.

📷 Tampilan Ambil Antrian Admisi
<img width="1433" height="788" alt="Screen Shot 2025-09-16 at 21 28 10" src="https://github.com/user-attachments/assets/2a02c985-57fd-44dd-a9ad-cbf857fa683a" />


📷 Tampilan Panggil Antrian Admisi
<img width="1440" height="809" alt="Screen Shot 2025-09-16 at 21 29 00" src="https://github.com/user-attachments/assets/4e1141b1-aa5f-464c-8b07-a34ea5fcf620" />


📷 Tampilan Display Antrian Admisi
<img width="1440" height="815" alt="Screen Shot 2025-09-16 at 20 49 40" src="https://github.com/user-attachments/assets/b72581de-43df-4d7e-8fda-be79bce4a8af" />


2. Antrian Poli

Menampilkan pasien yang dipanggil di layar TV poli.

Menyimpan nomor antrian dan nama pasien.

Mendukung suara otomatis ketika pasien dipanggil.

📷 Tampilan Panggil Antrian Poli
<img width="1439" height="625" alt="Screen Shot 2025-09-16 at 21 30 42" src="https://github.com/user-attachments/assets/b24f1808-899c-4084-89d7-b6ab0db5554e" />


📷 Tampilan Display Antrian Poli
<img width="1440" height="812" alt="Screen Shot 2025-09-16 at 21 29 25" src="https://github.com/user-attachments/assets/c29bd475-02c6-4f76-8d66-5b8fd4fc9239" />



🚀 Cara Menjalankan

Clone repository ke folder webserver (XAMPP: htdocs/ atau Laragon: www/).

Import database (struktur tabel disediakan terpisah).

Sesuaikan konfigurasi database di config.php.

Jalankan aplikasi:

Display TV Poli

http://localhost/antrian/display_tv_poli.php?kd_poli=U001


API antrian poli

http://localhost/antrian/get_antrian_poli.php?kd_poli=U001

🔊 Notifikasi Suara

Jika ada antrian baru dipanggil, sistem otomatis berbicara:

"Pasien atasa nama NAMA PASIEN,Silahkan ke poliklinik NAMA POLI."

🔧 Proses Pengembangan

 Antrian Farmasi + Display Antrian + Pemanggil Antrian (sedang dikembangkan)

📜 Catatan

Untuk tambahan detail tabel database, silakan hubungi via Japri: 082177846209
