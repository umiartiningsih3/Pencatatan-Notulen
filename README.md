# 📝 Sistem Pencatatan Notulen Rapat

Sistem berbasis web untuk mendokumentasikan hasil rapat secara digital, efisien, dan terorganisir. Aplikasi ini memungkinkan pengguna untuk mencatat detail rapat, daftar peserta, serta tindak lanjut (action items) dari setiap pembahasan.

## 🚀 Fitur Utama

* **Dashboard**: Ringkasan data notulen yang telah dibuat.
* **Input Notulen**: Formulir vertikal yang responsif untuk mencatat judul, waktu, tempat, dan penyelenggara rapat.
* **Manajemen Peserta**: Fitur pencarian dan penambahan peserta secara dinamis menggunakan AJAX.
* **Tabel Hasil Rapat**: Penginputan poin-poin pembahasan, tindak lanjut, dan PIC dengan fitur tambah baris otomatis.
* **Status Rapat**: Pelacakan status rapat (Selesai/Belum Selesai).
* **Manajemen Profil**: Pengaturan akun pengguna dan foto profil.

## 🛠️ Teknologi yang Digunakan

* **Frontend**: HTML5, CSS3 (Custom Poppins Fonts), Bootstrap 5.3.
* **Backend**: PHP.
* **Database**: MySQL.
* **Library**: 
    * Select2 (untuk dropdown dinamis).
    * Bootstrap Icons.

## 📁 Struktur Proyek

* `/UI Design (html)`: Berisi file tampilan antarmuka (PHP/HTML).
* `/api`: Endpoint untuk logika backend dan pengolahan data.
* `/database`: Berisi file SQL (`notulen_db.sql`) untuk struktur tabel.
* `vercel.json`: Konfigurasi untuk deployment di platform Vercel.

## ⚙️ Cara Instalasi (Lokal)

1.  **Clone Repositori**
    ```bash
    git clone [https://github.com/umiartiningsih3/Pencatatan-Notulen.git]
    ```
2.  **Persiapan Database**
    * Buka XAMPP dan aktifkan Apache serta MySQL.
    * Masuk ke `phpMyAdmin`.
    * Buat database baru bernama `notulen_db`.
    * Import file SQL yang ada di dalam folder `/database`.
3.  **Konfigurasi Koneksi**
    * Pastikan pengaturan koneksi di file PHP Anda sesuai:
        ```php
        $conn = mysqli_connect("localhost", "root", "", "notulen_db");
        ```
4.  **Jalankan Aplikasi**
    * Pindahkan folder proyek ke `C:/xampp/htdocs/`.
    * Akses melalui browser di `http://localhost/Pencatatan-Notulen/`.

---
© 2025 Notulen Tracker.
