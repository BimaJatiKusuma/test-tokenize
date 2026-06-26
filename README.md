# 🔑 SaaS Offline-First Activation Simulation MVP

Sistem ini adalah simulasi MVP (Minimum Viable Product) untuk aktivasi lisensi aplikasi SaaS dengan arsitektur **Offline-First**. 
Menggunakan **Laravel** sebagai Backend Server (tanpa Auth) dan **Flutter** sebagai Client Mobile App. Kedua pihak menggunakan **SQLite** sebagai database untuk mendukung mode luring.

---

## 🛠️ Cara Kerja SQLite di Jaringan Lokal (XAMPP & HP Fisik)

**Apakah SQLite bisa di-host via XAMPP agar diakses HP lokal?**
> **Bisa!** Namun perlu dipahami: SQLite bukanlah server database standalone (seperti MySQL). SQLite hanyalah file database lokal yang dibaca langsung oleh kode Laravel. 
> Untuk mengeksposnya ke HP lokal, kita membagikan **Web Server Laravel (PHP)** di jaringan lokal menggunakan IP komputer Anda. Laravel-lah yang nantinya membaca SQLite dan membalas request API dari HP Anda.

### Langkah-langkah Konfigurasi Koneksi Jaringan Lokal:

1. **Hubungkan HP dan Laptop/PC** ke jaringan Wi-Fi yang sama.
2. **Cari IP Address Lokal komputer Anda**:
   - Di Windows (PowerShell/CMD): Jalankan `ipconfig`
   - Temukan `IPv4 Address` pada adapter yang aktif (contoh: `192.168.1.15`).
3. **Jalankan Server Laravel dengan IP Lokal tersebut**:
   Buka terminal di direktori `backend/` dan jalankan:
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```
   *Catatan: `--host=0.0.0.0` akan mendengarkan request dari semua interface jaringan lokal, bukan hanya localhost.*
4. **Perbarui Endpoint API di Aplikasi Flutter**:
   Buka file [lib/main.dart](file:///d:/xampp/htdocs/saas-security/frontend/lib/main.dart#L73) dan ubah `_serverBaseUrl` menggunakan IP Lokal komputer Anda:
   ```dart
   final String _serverBaseUrl = "http://192.168.1.15:8000"; // Ganti dengan IP lokal Anda!
   ```

---

## 🚀 Panduan Memulai Cepat (Quickstart Guide)

### 💻 1. Persiapan Backend (Laravel)
1. Buka folder `backend/` pada terminal.
2. Pastikan file `.env` menggunakan SQLite:
   ```env
   DB_CONNECTION=sqlite
   ```
3. Buat file database kosong (jika belum ada):
   - PowerShell: `New-Item -ItemType File -Path database/database.sqlite -Force`
   - CMD / Unix: `touch database/database.sqlite`
4. Jalankan migrasi database:
   ```bash
   php artisan migrate --force
   ```
5. Nyalakan server lokal:
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```
6. Buka browser di http://localhost:8000 (atau http://IP_LOKAL:8000) untuk mengakses Dashboard License Generator.

### 📱 2. Persiapan Frontend (Flutter)
1. Buka folder `frontend/` pada terminal.
2. Update IP Server pada `lib/main.dart` sesuai instruksi di atas.
3. Jalankan aplikasi Flutter:
   ```bash
   flutter run
   ```

---

## 🧪 Skenario Pengujian & Uji Coba Simulasi

Ikuti langkah-langkah di bawah ini untuk mensimulasikan fitur offline-first dan keamanan lisensi:

### Skenario 1: Aktivasi Awal (Normal)
1. Buka Dashboard Web Laravel, klik tombol **"Generate New Key"** untuk mendapatkan License Key baru.
2. Salin key tersebut (format: `LIC-XXXX-XXXX-XXXX`).
3. Masuk ke aplikasi Flutter, masukkan key pada kolom input, lalu klik **"Aktivasi Melalui Server"**.
4. **Hasil**: Status aplikasi berubah menjadi hijau: **"APLIKASI AKTIF - Aman Digunakan Offline"**.

### Skenario 2: Simulasi Kloning Perangkat (Anti-Clone)
1. Setelah aplikasi berstatus aktif, pada bagian bawah **Simulation Control**, klik tombol **"Simulasikan Kloning Aplikasi"**.
2. Fungsi ini akan memanipulasi Device ID di sisi Flutter (seolah aplikasi dicadangkan dan dipulihkan di HP lain dengan ID berbeda).
3. **Hasil**: Status aplikasi seketika berubah menjadi merah: **"APLIKASI TERKUNCI: Perangkat Kloning Terdeteksi!"**.
4. Klik **"Reset ke Normal"** untuk mengembalikannya ke kondisi valid.

### Skenario 3: Masa Aktif Lisensi Habis (Time Expiry)
1. Di Dashboard Web Laravel, temukan lisensi aktif Anda dan klik tombol **"Force Expire"**.
2. Kembali ke Flutter, klik tombol **"Tombol Sinkronisasi Manual"** untuk memperbarui token dari server.
3. **Hasil**: Status berubah menjadi Orange: **"MASA AKTIF HABIS: Silakan Perpanjang Langganan."**.
4. *Alternatif Offline*: Anda juga bisa menguji ini secara offline (tanpa ubah data server) dengan menekan tombol **"Simulasikan Waktu Habis"** pada panel simulasi Flutter untuk memajukan jam internal mock melampaui 30 hari.

### Skenario 4: Kecurangan Manipulasi Jam (Time Tampering)
1. Pastikan aplikasi dalam status Aktif (Hijau).
2. Pada panel simulasi Flutter, tekan tombol **"Simulasikan Mundurkan Jam"**.
3. Tombol ini akan memundurkan jam sistem mock ke masa lampau (sebelum transaksi database SQLite lokal terakhir dicatat).
4. **Hasil**: Status berubah menjadi merah tua: **"ASET AMAN: Terdeteksi Kecurangan Manipulasi Jam!"** untuk melindungi data lokal dari eksploitasi perpanjangan manual lewat ubah tanggal HP.
5. Klik **"Reset ke Normal"** untuk memulihkan jam.
