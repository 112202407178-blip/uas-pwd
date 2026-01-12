# Sistem Reservasi Studio Musik

**Deskripsi singkat**
Sistem Reservasi Studio Musik adalah aplikasi sederhana berbasis PHP + MySQL untuk memesan studio rekaman. Dilengkapi panel admin untuk mengelola studio dan reservasi, serta antarmuka pengguna responsif dengan tampilan modern dan fitur booking via modal.

---

## B. JUDUL PROYEK
**Judul Proyek:**
Aplikasi Reservasi Studio Musik Berbasis Web (PHP + MySQL)

Catatan:
- Judul harus spesifik dan menggambarkan aplikasi web.
- Contoh: "Aplikasi Reservasi Studio Musik Berbasis Web dengan PHP dan MySQL".



## C. LATAR BELAKANG
Jelaskan secara singkat:
- Masalah / kebutuhan:
  - Banyak studio kecil masih menerima pemesanan manual (telepon/WA), menyebabkan konflik jadwal dan dokumentasi yang buruk.
- Alasan memilih tema aplikasi:
  - Mempermudah manajemen jadwal, mengurangi double-booking, dan memberikan metode pencatatan yang rapi.
- Manfaat aplikasi bagi pengguna:
  - Pengguna dapat melihat ketersediaan, memesan online, dan mendapatkan riwayat booking; pemilik studio mendapat kontrol penuh atas jadwal dan laporan.

Contoh:
Banyak pemilik studio masih mengandalkan pencatatan manual sehingga sering terjadi double-booking. Aplikasi ini menyederhanakan proses reservasi, mengurangi konflik jadwal, dan menyediakan laporan yang mudah diekspor.

---

## D. DESKRIPSI SINGKAT APLIKASI
Jelaskan gambaran umum aplikasi:
- Fungsi utama:
  - Menampilkan daftar studio, cek ketersediaan, booking via modal (AJAX), manajemen studio & reservasi di panel admin, export laporan (CSV/print).
- Jenis pengguna (user):
  - User biasa (klien) — mendaftar, login, memesan studio, melihat riwayat, membatalkan booking.
  - Admin (pemilik/staff) — mengelola studio, melihat & mengubah status reservasi, mencetak laporan.
- Alur penggunaan singkat:
  1. User memilih studio → klik "Booking" → modal muncul.
  2. User memilih tanggal & jam → sistem cek ketersediaan → submit.
  3. Admin melihat reservasi → approve / reject / complete → user menerima pembaruan.

---

## E. FITUR UTAMA APLIKASI
Tuliskan fitur yang akan dibuat (minimal 4–6 fitur):
- Form booking via modal (AJAX) — frontend + backend (`simpanBooking.php`).
- Pengecekan ketersediaan real-time (`checkAvailability.php`) termasuk booking overnight.
- Manajemen studio (CRUD) di panel admin (`tambahStudio.php`, `koreksi.php`, `hapus.php`).
- Dashboard reservasi admin (`tampilReservasi.php`) dan update status (approve/reject/complete) dengan validasi konflik.
- Riwayat booking user dan kemampuan batal booking.
- Export laporan (CSV) dan printable view (`cetakLaporan.php`).
- UI responsif & interaktif (kartu studio, badges, validasi JS).

Catatan: fitur menunjukkan integrasi front-end (HTML/CSS/JS) dan back-end (PHP/MySQL).

---

## F. TEKNOLOGI YANG DIGUNAKAN
Komponen:
- Front-End: HTML5, CSS3 (`css/theme.css`).
- Interaktivitas: JavaScript (vanilla) (`js/app.js`).
- Back-End: PHP (file-based scripts: `simpanBooking.php`, `updateStatusBooking.php`, dll.).
- Database: MySQL / MariaDB (`setup_database.sql`).
- Tools: VS Code, Laragon/XAMPP.
- Version Control: Git & GitHub.

---

## 🔎 Ringkasan Fitur
- Autentikasi pengguna (register / login)
- Panel admin untuk: manajemen studio, melihat & mengubah status reservasi (pending → approved/rejected/completed)
- Booking via modal (AJAX) tanpa berpindah halaman
- Pengecekan ketersediaan otomatis (termasuk pemesanan yang melewati tengah malam)
- Tampilan daftar studio bergaya kartu (e-commerce style)
- Export laporan (CSV) dan view printable (`cetakLaporan.php`)
- Notifikasi/flash sederhana dan validasi input

---

## 📦 Struktur Proyek (penting)
Contoh file & folder utama:

- `index.php` (opsional) / `daftarStudio.php` — halaman utama daftar studio
- `login.php`, `register.php` — autentikasi
- `tampilStudio.php`, `tambahStudio.php`, `koreksi.php`, `hapus.php` — manajemen studio (admin)
- `tampilReservasi.php`, `updateStatusBooking.php` — manajemen reservasi (admin)
- `formBooking.php`, `simpanBooking.php`, `riwayatBooking.php`, `batalBooking.php` — alur booking (user)
- `cetakLaporan.php` — laporan & export CSV
- `checkAvailability.php` — endpoint pengecekan ketersediaan (dipakai oleh JS)
- `inc/header.php`, `inc/footer.php` — shared layout
- `css/theme.css`, `js/app.js` — styling & interaksi frontend
- `koneksi.php`, `config.php` — koneksi database / konfigurasi
- `setup_db.php`, `setup_database.sql` — skrip inisialisasi DB & sample data
- `img_studio/` — unggahan foto studio

---

## 🛠 Prasyarat (local dev)
- Windows: Laragon disarankan (atau XAMPP)
- PHP 7.4+ (direkomendasikan 8.x)
- MySQL / MariaDB
- Browser modern (Chrome/Firefox)

---

## Setup & Instalasi (Langkah demi langkah)
1. Salin folder proyek ke folder web server (mis. `C:\laragon\www\sistem_musik`).
2. Pastikan `config.php` berisi kredensial database yang benar (DB_HOST, DB_USER, DB_PASS, DB_NAME).

Contoh `config.php`:

```php
<?php
define('DB_HOST','127.0.0.1');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','sistem_musik');
?>
```

3. Akses `http://localhost/sistem_musik/setup_db.php` untuk membuat database, tabel, user admin default, dan contoh data.
   - Default admin: **username** `admin`, **password** `admin123` (ubah setelah setup).
   - Setelah selesai: hapus atau amankan `setup_db.php`.

4. Akses `http://localhost/sistem_musik/daftarStudio.php` untuk melihat aplikasi.

---

## Cara kerja booking & aturan penting
- Pengguna memilih studio → klik `Booking` → modal muncul. Modal menggunakan AJAX untuk submit ke `simpanBooking.php`.
- Server memeriksa konflik waktu pada level database/logic. Sistem mendukung booking yang melewati tengah malam (mis. mulai 23:00 selesai 01:00 berikutnya).
- Jika ada konflik, server mengembalikan pesan error (untuk AJAX: JSON dengan `success: false`).
- Admin dapat menyetujui (`approved`) atau menolak (`rejected`) booking melalui `tampilReservasi.php`. Saat menyetujui, sistem akan memeriksa ulang konflik sebelum mengubah status.

---

## Endpoint & utilitas penting
- `checkAvailability.php?date=YYYY-MM-DD&start=HH:MM&end=HH:MM` → mengembalikan daftar studio yang bentrok pada rentang waktu tersebut.
- `updateStatusBooking.php?id={id}&status=approved|rejected|completed` → handler update status (admin-only).
- `cetakLaporan.php` → view laporan + export CSV (`?action=csv`).

---

## Pengembangan & Kustomisasi
- Styling: ubah `css/theme.css` (variabel di :root seperti `--accent`) untuk mengganti tema warna
- Header/footer: `inc/header.php` & `inc/footer.php` (termasuk import `js/app.js`)
- Tambah validasi atau fitur: `js/app.js` berisi logic modal + availability checks – mudah diperluas

---

## Keamanan & Best Practices
- Hapus atau batasi akses `setup_db.php` setelah inisialisasi.
- Jangan menyimpan password plain text; gunakan `password_hash()` (sudah digunakan di setup).
- Batasi aksi admin dengan pemeriksaan `$_SESSION['role'] === 'admin'` (sudah ada di beberapa file) — periksa semua route yang sensitif.
- Sanitasi output: gunakan `htmlspecialchars()` untuk field yang ditampilkan (sudah diterapkan di banyak tempat).
- Pertimbangkan HTTPS/SSL saat deploy.

---

## Debugging & Troubleshooting
- 404 pada beberapa file → periksa nama file dan path yang diketikan di URL.
- Jika AJAX gagal: buka Console devtools (Network tab) untuk melihat response JSON atau error 500.
- Masalah timezone/durasi: pastikan `date.timezone` di php.ini telah diset (mis. `Asia/Jakarta`) atau gunakan `date_default_timezone_set()` di awal.

Contoh pemeriksaan manual (MySQL):
```sql
SELECT * FROM bookings WHERE studio_id = 1 ORDER BY booking_date, start_time;
```

---

## Rencana fitur (opsional)
- Notifikasi email ketika booking disetujui/ditolak
- Kalender per studio dengan tampilan hari/pekan/bulan
- Export PDF untuk laporan
- Fitur search/sort/filter pada daftar studio

---

## Catatan Developer (perubahan yang telah saya terapkan)
- Menambahkan `inc/header.php` / `inc/footer.php` untuk layout bersama.
- Membuat `css/theme.css` dan memperbarui tampilan menjadi modern dan responsif.
- Menambahkan modal booking (AJAX) + `checkAvailability.php` untuk cek overlap.
- Menambahkan `updateStatusBooking.php` untuk meng-handle aksi admin approve/reject secara aman.
- Menambahkan `cetakLaporan.php` untuk print & CSV export.


## Lisensi & Kontak
- Proyek ini bersifat contoh/sederhana untuk demo. Untuk produksi, tinjau dan tambahkan lisensi.
- Jika ingin bantuan lanjutan (fitur baru, integrasi email, atau deploy), hubungi: **(tambahkan kontak Anda di sini)**


 
 
