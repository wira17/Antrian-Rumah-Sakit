# 🏥 Sistem Antrian Klinik

Aplikasi web sederhana untuk menampilkan dan mengelola **sistem antrian rumah sakit/klinik**.  
Mendukung:
- 📊 Antrian loket/admisi/farmasi (`antrian_wira`)
- 🏥 Antrian Poli Klinik (`antrian_poli_wira`)
- 🔊 Notifikasi suara otomatis saat antrian dipanggil
- 🎥 Tampilan display TV poli (YouTube + informasi pasien)
- ⏰ Jam digital & running text di layar

---

## ⚙️ Fitur Utama
1. **Antrian Umum (Tabel `antrian_wira`)**
   - Menyimpan antrian di loket, admisi, farmasi, dsb.
   - Status default = `Menunggu`
   - Kolom `waktu_panggil` otomatis terisi saat pasien dipanggil

2. **Antrian Poli (Tabel `antrian_poli_wira`)**
   - Menyimpan data pasien yang dipanggil ke poli
   - Menyimpan jam panggil untuk setiap nomor antrian
   - Digunakan untuk tampilan **display_tv_poli.php**

3. **Display TV Poli**
   - Panel kiri: Video informasi (YouTube)
   - Panel kanan: Total antrian + pasien sedang dilayani
   - Notifikasi suara otomatis
   - Auto-refresh setiap 2 detik

4. **API (`get_antrian_poli.php`)**
   - Format JSON:
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

## 🗄️ Struktur Tabel Database

### 1. Tabel `antrian_wira`
```sql
CREATE TABLE antrian_wira (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jenis VARCHAR(50) NOT NULL,          -- jenis antrian: admisi, farmasi, dll
    nomor INT NOT NULL,                  -- nomor antrian
    no_rkm_medis VARCHAR(20),            -- nomor rekam medis pasien
    loket_id INT,                        -- id loket
    status VARCHAR(50) DEFAULT 'Menunggu', -- status: Menunggu / Dipanggil / Selesai
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    waktu_panggil TIMESTAMP NULL
);
