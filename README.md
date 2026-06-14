# 🕌 ETahfiz Academic - Monitoring Sistem Hafalan Santri
Sistem berbasis Web yang dirancang khusus untuk mempermudah Ustadz/Penguji dalam memonitoring perkembangan jurnal hafalan harian serta mengelola standarisasi sertifikasi kelulusan ujian tasmi' santri secara digital dan real-time.<br>
## Progres Implementasi Spesifikasi Komponen (UAS Terapan)
### 1. Sistem Autentikasi (Login)
* **Status:** [Selesai]
* **Lokasi File:** `login.php`
* **Keterangan:** Fitur pembatasan hak akses aman menggunakan PHP Session (`$_SESSION['role']`) untuk memisahkan wewenang fitur antara akun level `ustadz` dan akun level `santri`.

### 2. CRUD + Upload Multiple File
* **Status:** [Selesai]
* **Lokasi File:** `aksi_setoran.php` & `simpan_ujian.php`
* **Keterangan:** Manajemen data hafalan harian secara penuh (Create, Read, Update, Delete). Pada form input ujian kelulusan, ustadz dapat mengunggah beberapa dokumen pendukung sekaligus (*Multiple File Upload*) ke folder `uploads/` menggunakan perulangan array `$_FILES`.

### 3. Pencarian Data & Datatables Integrasi
* **Status:** [Selesai]
* **Lokasi File:** `index.php` (Tab Setoran & Tab Ujian)
* **Keterangan:** Penerapan library *DataTables Engine* pada tabel riwayat untuk menyediakan fitur pencarian instan (*live search*), pembagian halaman (*pagination*), serta tombol pintas ekspor data ke format **Excel** dan dokumen **Cetak (Print)**.

### 4. Canvas untuk Tanda Tangan Digital
* **Status:** [Selesai]
* **Lokasi File:** `modals.php` & `js/canvas-script.js`
* **Keterangan:** Pemanfaatan komponen HTML5 `<canvas>` pada modal input ujian yang memungkinkan ustadz menggoreskan tanda tangan langsung di layar gawai, lalu dikonversi secara *real-time* menjadi string Base64 untuk disimpan ke database MySQL.

### 5. Video / Animasi dan Audio Feedback
* **Status:** [Selesai]
* **Lokasi File:** `index.php` & `script_dashboard.js`
* **Keterangan Animasi:** Grafik distribusi kelulusan pekanan interaktif berbasis **Chart.js** dengan efek *Elastic-Easing Animation* saat dimuat, berjejer dengan *animated progress bar* capaian target 30 Juz santri.
* **Keterangan Audio:** Integrasi efek suara bel kelulusan lokal (`notify.wav`) yang otomatis berbunyi nyaring sesaat setelah user menekan tombol "OK" pada pop-up konfirmasi.

### 6. Penggunaan Bootstrap Modal
* **Status:** [Selesai]
* **Lokasi File:** `modals.php`
* **Keterangan:** Seluruh form penambahan data, proses edit data setoran, hingga ruang goresan tanda tangan digital dimuat di dalam komponen *Bootstrap 5 Modal* untuk menjaga estetika layout tetap rapi tanpa merusak alur halaman utama.

---
## Stack Teknologi

* **Backend:** PHP Native v8.x & MySQL Database
* **Frontend:** Bootstrap v5.3, JQuery v3.7, Font-Awesome Icons
* **Plugins:** Chart.js Library, DataTables Bootstrap-5 Extension, SweetAlert2 Pop-up Notification