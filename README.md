# TELYUJI: SISTEM INFORMASI MANAJEMEN LAYANAN PENGUJIAN LABORATORIUM TERINTEGRASI

Tugas Besar Pengembangan Perangkat Lunak (PPL)  
Program Studi S1 Sistem Informasi - Fakultas Rekayasa Industri  
Telkom University  
Kelas: SI-48-07 | Kelompok: B-B 

---

## Tautan Sistem

- Live Deployment: https://telyuji-02704c185fa1.herokuapp.com
- Repositori GitHub: https://github.com/SI4807-PPL-KelompokF/PPL-Kelompok-B-B-SI4807

---

## Tim Pengembang

| No | Nama Lengkap | NIM | Peran Utama | Tanggung Jawab Modul & Fitur (PBI) |
| :--- | :--- | :--- | :--- | :--- |
| 1 | Hainur Fadillah | 102022430032 | Project Manager / Developer | Modul Hasil & Keamanan (PBI-13, PBI-14, PBI-15): Upload dokumen lab, portal unduh berkas terenkripsi (private storage), dan audit trail otomatis. |
| 2 | Galuh Shafa Auliana Rahman | 102022400131 | Developer | Modul Validasi Antrean (PBI-10, PBI-11, PBI-12): Logika penerimaan fisik sampel, penugasan operator, dan penolakan sampel cacat. |
| 3 | Helmi Fauzan | 102022400211 | Developer | Modul Katalog Lab (PBI-04, PBI-05, PBI-06): Katalog status mesin lab, parameter batas kuota harian/SLA, dan panduan SOP laboratorium. |
| 4 | Giri Pulung Nugroho | 102022400284 | Developer | Modul Pengajuan (PBI-07, PBI-08, PBI-09): Formulir pengajuan uji lab dinamis, generator resi E-Ticket PDF ber-barcode, dan visual timeline tracking. |
| 5 | Syalfa Oktaviana Wijaya | 102022400228 | Developer | Modul Akses & Institusi (PBI-01, PBI-02, PBI-03): Manajemen profil & validasi KTM, fitur suspend akun pelanggar, dan master data Fakultas/Prodi. |
| 6 | Nabila Rizky Putri | 102022400061 | Developer | Modul Pelaporan & Dasbor (PBI-16, PBI-17, PBI-18): Grafik statistik utilitas mesin di dasbor admin, ruang diskusi tiket, dan ekspor laporan riwayat ke Excel/PDF. |

---

## Latar Belakang & Masalah

Layanan pengujian laboratorium di lingkungan Telkom University merupakan fasilitas esensial bagi sivitas akademika (mahasiswa dan dosen) dalam aktivitas riset. Namun, proses konvensional yang berjalan memiliki beberapa kelemahan mendasar:
1. Pendaftaran antrean masih manual dan menggunakan pencatatan kertas.
2. Pemohon tidak memiliki kepastian waktu penyelesaian pengujian (Service Level Agreement / SLA).
3. Distribusi lembar hasil pengujian rentan hilang atau rusak karena mengandalkan dokumen fisik.

Telyuji hadir sebagai solusi sistem informasi manajemen layanan laboratorium terintegrasi berbasis web yang mendigitalisasi proses pengujian secara transparan, nirkertas (paperless), dan akuntabel.

---

## Tujuan & Luaran Sistem

### Tujuan Pengembangan
1. Memberikan kepastian waktu penyelesaian pengujian kepada pemohon riset.
2. Menyediakan visual tracking (linimasa antrean) untuk memantau pergerakan sampel uji secara real-time.
3. Mengamankan distribusi data hasil riset agar hanya dapat diakses oleh pihak yang terotorisasi.

### Luaran Produk (Output)
1. Aplikasi web Telyuji sebagai pusat manajemen layanan laboratorium terpadu.
2. E-Ticket (Resi PDF ber-barcode) untuk tanda terima dan pelacakan fisik spesimen.
3. Portal pengunduhan dokumen Laporan Hasil Uji (LHU) resmi dalam format PDF/Excel terproteksi.

### Batasan Ruang Lingkup (Out of Scope)
1. Tanpa Modul Pembayaran: Layanan merupakan fasilitas kampus bebas biaya transaksi gateway.
2. Tanpa Peminjaman Eksternal: Aplikasi difokuskan pada pengujian di dalam laboratorium kampus.
3. Tanpa Integrasi Sensor IoT: Pembaruan status mesin dan antrean divalidasi oleh laboran.
4. Tanpa Logistik Ekspedisi: Pengantaran sampel fisik dilakukan langsung oleh pemohon ke lab.

---

## Modul Sistem & Product Backlog Items (PBI)

| ID PBI | Kebutuhan Fungsional | Deskripsi Fitur |
| :--- | :--- | :--- |
| PBI-01 | Kelola Profil & Validasi Identitas | Pemohon melengkapi data profil akademik dan mengunggah kartu identitas (KTM/ID) serta surat pengantar riset. |
| PBI-02 | Manajemen Sanksi Pengguna | Laboran dapat menangguhkan (suspend) sementara akun pemohon yang melanggar ketentuan (no-show atau merusak alat). |
| PBI-03 | Kelola Master Data Institusi | Admin mengelola data referensi Fakultas dan Program Studi di lingkungan universitas. |
| PBI-04 | Katalog Status Mesin Lab | Admin dan Laboran mengelola daftar alat/mesin laboratorium serta status operasionalnya. |
| PBI-05 | Parameter Kuota & SLA | Pengaturan batas kuota harian pengujian dan estimasi waktu penyelesaian (SLA) per instrumen. |
| PBI-06 | Kelola SOP Laboratorium | Penyediaan editor panduan operasional standar penyiapan sampel uji bagi laboran. |
| PBI-07 | Formulir Pengajuan Uji Lab | Formulir dinamis bagi pemohon untuk memilih layanan lab dan memasukkan spesifikasi sampel. |
| PBI-08 | Cetak Resi E-Ticket PDF | Penerbitan otomatis lembar resi PDF ber-barcode untuk verifikasi penyerahan sampel fisik. |
| PBI-09 | Visual Timeline Tracking | Pelacakan linimasa status sampel secara real-time pada dasbor pemohon. |
| PBI-10 | Validasi & Status Antrean | Laboran memvalidasi penerimaan fisik sampel dan memperbarui status antrean pengujian. |
| PBI-11 | Penugasan Operator Lab | Penugasan laboran/operator spesifik sebagai penanggung jawab pengujian instrumen. |
| PBI-12 | Penolakan Pengajuan Sampel | Fitur penolakan sampel yang cacat atau melanggar SOP disertai pencatatan alasan resmi. |
| PBI-13 | Kelola Dokumen Hasil Uji | Fasilitas bagi laboran untuk mengunggah dan merevisi berkas Laporan Hasil Uji (LHU) resmi. |
| PBI-14 | Portal Unduh Dokumen Terproteksi | Gerbang unduh berkas hasil pengujian yang terenkripsi (AES-256-CBC) dan hanya dapat diakses akun pemilik tiket atau laboran. |
| PBI-15 | Log Aktivitas & Audit Trail | Pencatatan riwayat setiap unggahan dan revisi berkas hasil uji untuk transparansi pengawasan. |
| PBI-16 | Dasbor Analitik & Grafik | Visualisasi grafik statistik penggunaan alat laboratorium pada dasbor manajerial. |
| PBI-17 | Ruang Diskusi Tiket | Fitur pesan interaktif dua arah antara pemohon dan laboran pada setiap nomor tiket pengujian. |
| PBI-18 | Ekspor Laporan Operasional | Fitur filter tanggal untuk mengekspor rekapitulasi data pengujian ke format Excel dan PDF. |

---

## Karakteristik Pengguna, Hak Akses & Rute Utama

1. **Admin (`admin` — `/admin/pengguna`):**
   - Mengelola akun pengguna civitas akademika (tambah, ubah peran, perbarui data, dan hapus akun Pemohon, Laboran, maupun Admin).
   - Mengelola master data institusi serta pengawasan sistem.
2. **Laboran (`laboran` — `/laboran/pengujian`):**
   - Mengelola antrean pengujian sampel laboratorium.
   - Mengunggah dan merevisi dokumen Laporan Hasil Uji (LHU) berformat `.pdf`, `.xlsx`, atau `.xls` (maks. 5 MB) dengan validasi *magic bytes*, penyimpanan terenkripsi `AES-256-CBC` di *private storage*, checksum `SHA-256`, serta pencatatan otomatis ke *Audit Trail*.
3. **Pemohon (`pemohon` — `/home`):**
   - Mahasiswa (`@student.telkomuniversity.ac.id`) maupun Dosen/Staf (`@telkomuniversity.ac.id`) yang mengajukan sampel pengujian.
   - Memantau progres pengujian sampel secara *real-time* dan mengunduh dokumen LHU resmi miliknya sendiri melalui `/pengajuan/{nomorTiket}/unduh-lhu`.

---

## Arsitektur & Kebutuhan Teknis

- Bahasa Pemrograman: PHP 8.2 (Backend), JavaScript ES6, HTML5, CSS3 (Frontend)
- Framework Backend: Laravel 8 (Arsitektur Clean Code: Controller, Service Layer, FormRequest, Middleware, & Domain Model)
- Framework Frontend & Styling: Tailwind CSS 3 (Utility-First, mendukung **Light & Dark Mode** tanpa tag `<style>` inline) dan Laravel Mix 6 (Webpack 5)
- Basis Data: MySQL / JawsDB (Pengembangan & Produksi), SQLite (Isolasi Pengujian Otomatis di CI)
- Keamanan Berkas & Autentikasi:
  - Validasi email resmi civitas akademika Telkom University (`@telkomuniversity.ac.id` beserta subdomainnya) dan kombinasi kata sandi kuat.
  - Enkripsi dokumen hasil uji `AES-256-CBC` pada *private storage* disertai verifikasi integritas `SHA-256`.
  - Pemisahan hak akses ketat berbasis peran melalui middleware `admin` dan `laboran`.
- **Pengujian Otomatis:** PHPUnit (Feature Testing: 19 skenario pengujian) & Laravel Dusk (Browser Automation Testing)
- Otomasi CI/CD: GitHub Actions (`.github/workflows/laravel.yml` — job `laravel-tests`) & Heroku Automatic Deploys

---

## Standar Desain Sistem (Protokol Telyuji)

Antarmuka sistem Telyuji menerapkan gaya **2D flat, minimalis, dan geometris** (*solid color blocking*, anti AI-slop) dengan panduan tipografi dan palet warna resmi:
- **Tipografi:** **General Sans** (Indian Type Foundry) dengan *system-font fallback* (`-apple-system, BlinkMacSystemFont, Segoe UI, sans-serif`) dan angka tabular (`tabular-nums`).
- **Racing Red (`#ED1E28`):** Identitas brand utama, tombol aksi primer (Simpan/Kirim/Unggah), dan penanda status aktif.
- **Tiger Flame (`#F26A2D`):** Efek hover tombol aksi, metrik pengujian berlangsung (`Sedang Diuji`), dan aksen sekunder.
- **Jasmine (`#F7D488`):** Kartu peringatan mode revisi, badge peran Admin, dan status menunggu verifikasi.
- **Cream (`#EAEFB1`):** Latar belakang panel ringkasan, badge peran Pemohon, dan aksen permukaan ringan.
- **Frosted Mint (`#E9F7CA`):** Indikator status pengujian `Selesai`, validasi berkas berhasil, dan konfirmasi positif.

---

## Panduan Menjalankan Sistem di Lingkungan Lokal

### 1. Prasyarat Sistem
- PHP >= 8.2 (ekstensi: pdo, pdo_mysql, sqlite3, pdo_sqlite, mbstring, curl, libxml, zip, fileinfo)
- Composer >= 2.0
- Node.js >= 18.x dan NPM >= 9.x
- Google Chrome Browser (untuk pengujian Laravel Dusk)

### 2. Kloning Repositori
```bash
git clone https://github.com/SI4807-PPL-KelompokF/PPL-Kelompok-B-B-SI4807.git
cd PPL-Kelompok-B-B-SI4807
```

### 3. Instalasi Dependensi & Kompilasi Aset
```bash
composer install
npm install --legacy-peer-deps
npm run prod
```

### 4. Konfigurasi Environment File
```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan parameter database pada file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=telyuji
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Migrasi Skema Database & Seeding Akun Default
```bash
php artisan migrate --seed
```

Perintah `--seed` akan menyiapkan sampel antrean pengujian awal beserta 3 akun default sesuai peran:

| Peran | Email Login | Kata Sandi | Halaman Tujuan |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@telkomuniversity.ac.id` | `Telyuji#2026` | `/admin/pengguna` |
| **Laboran** | `laboran@telkomuniversity.ac.id` | `Telyuji#2026` | `/laboran/pengujian` |
| **Pemohon** | `pemohon@telkomuniversity.ac.id` | `Telyuji#2026` | `/home` |

### 6. Menjalankan Server Pengembangan
```bash
php artisan serve
```
Aplikasi dapat diakses melalui peramban pada alamat: `http://127.0.0.1:8000`

---

## Eksekusi Pengujian Otomatis (Automated Testing)

### Feature Tests (PHPUnit)
```bash
php artisan test
```

### Browser E2E Automation Tests (Laravel Dusk)
Pastikan server lokal sedang berjalan pada port 8000, lalu jalankan:
```bash
php artisan dusk
```

---

## Spesifikasi Deployment Produksi (Heroku)

Aplikasi dikonfigurasi menggunakan file `Procfile`:
```Procfile
web: vendor/bin/heroku-php-apache2 public/
```

Konfigurasi Environment Heroku (Config Vars):
- APP_ENV: `production`
- APP_DEBUG: `false`
- APP_KEY: Key enkripsi aplikasi Laravel
- APP_URL: `https://telyuji-02704c185fa1.herokuapp.com`
- DATABASE_URL / JAWSDB_URL: Connection string MySQL dari JawsDB
- LOG_CHANNEL: `errorlog`
