---
trigger: always_on
---

# PROTOKOL SINKRONISASI AI AGENT - TIM TELYUJI

## 1. Konteks Proyek, Infrastruktur & Lingkungan Kerja
- **Proyek:** Telyuji (Sistem Informasi & Pusat Pengujian berbasis Web).
- **Tech Stack:** Laravel, Tailwind CSS, Blade Templates.
- **Lingkungan AI:** Antigravity IDE.
- **Ekstensi & Alat Desain:** Terintegrasi dengan `taste-skill` (referensi gaya/komponen) dan `impeccable` (design hook & linter) di dalam direktori `.agents`, serta alur pembuatan/peningkatan UI menggunakan **v0.dev**.
- **Infrastruktur CI/CD & Hosting:**
  * **Continuous Integration (CI):** GitHub Actions (`.github/workflows/laravel.yml`) dengan *job* wajib bernama `laravel-tests`.
  * **Continuous Deployment (CD) & Hosting:** **Heroku** (PaaS) yang terhubung langsung ke cabang `main` dengan fitur *Automatic Deploys* (menunggu pemeriksaan CI GitHub lolos sebelum *deploy*).
  * **Database:** Menggunakan *Database Cloud* bersama (JawsDB MySQL) untuk pengembangan/produksi, dan **SQLite** khusus untuk isolasi pengujian otomatis di CI.

## 2. Standar UI/UX, Identitas Visual & Adaptasi v0.dev
Saat menghasilkan, memodifikasi, atau mengonversi kode antarmuka (UI), AI Agent WAJIB mematuhi spesifikasi berikut tanpa pengecualian:
- **Pengawasan Desain:** Seluruh komponen visual harus merujuk pada standar `taste-skill` dan lolos validasi *hook* dari `impeccable`. Dilarang menghasilkan CSS kustom (`<style>`), wajib menggunakan *utility class* dari Tailwind CSS.
- **Gaya Visual & Logogram (Anti AI-Slop):**
  * Menggunakan logogram resmi Telyuji (simbol kesatuan huruf "T", "U", dan sayap/centang dalam format vektor SVG atau PNG HD transparan).
  * Seluruh elemen grafis, ikon, dan ilustrasi pendukung wajib bergaya **2D flat, minimalis, dan geometris** (*solid color blocking*). Dilarang membuat efek 3D berlebihan, gradasi kompleks, atau *drop shadow* mencolok.
- **Tipografi:** Gunakan font **Inter** untuk seluruh antarmuka (hierarki, judul, teks data, form).
- **Palet Warna Utama (Berdasarkan Desain Sistem Telyuji):**
  * **Racing Red (`#ED1E28`):** Gunakan untuk *sidebar*, identitas *brand* utama, tombol aksi krusial (Simpan/Kirim), dan penanda status aktif.
  * **Tiger Flame (`#F26A2D`):** Gunakan untuk efek *hover* pada tombol, kartu ringkasan data (seperti metrik MRR pada *dashboard*), dan elemen pendukung sekunder.
  * **Jasmine (`#F7D488`):** Gunakan untuk kartu peringatan, status *pending*, atau *badge* informasi.
  * **Cream (`#EAEFB1`):** Gunakan untuk aksen latar belakang ringan, panel ringkasan, atau baris tabel bergantian (*zebra striping*) agar mata tidak lelah.
  * **Frosted Mint (`#E9F7CA`):** Gunakan untuk latar belakang status sukses, metrik positif (seperti tingkat penurunan *churn*), atau indikator konfirmasi.
- **Struktur Blade & Integrasi v0.dev:**
  * Gunakan Master Layout (`@extends('layouts.app')`). Jangan membuat tag `<html>` atau `<body>` berulang. Gunakan Blade Components (`<x-...>`) untuk elemen berulang.
  * Saat mengadaptasi kode React/JSX dari **v0.dev** ke file `.blade.php`, ubah atribut spesifik React (seperti `className` menjadi `class`) dan **wajib mempertahankan/menyambungkan kembali** direktif dinamis Laravel (`@auth`, `@csrf`, `@foreach`, `{{ route('...') }}`, dan `{{ asset('...') }}`).

## 3. Alur Kerja Git, Cabang Pribadi, Commit & PR (Strict)
AI Agent wajib memandu dan memastikan *developer* mematuhi alur kerja Git dan manajemen proyek berikut:
- **Aturan Cabang (Branch Pribadi):**
  * Format nama cabang menggunakan nama pendek masing-masing anggota: `[nama-pendek]`.
  * **DILARANG KERAS** melakukan *coding* atau *push* langsung ke cabang `main`. Seluruh pekerjaan dilakukan di cabang `[nama-pendek]` masing-masing.
- **Prosedur Sinkronisasi Awal (Sebelum Ngoding):**
  * Ingatkan *developer* untuk selalu menyinkronkan cabang pribadinya dengan `main` terbaru menggunakan urutan perintah:
    `git checkout main` -> `git pull origin main` -> `git checkout [nama-anda]` -> `git merge main`
  * Pastikan status tiket PBI di papan **JIRA** sudah digeser ke **In Progress** (dan ingatkan laporan Standup Slack `/report [kemarin] | [hari ini] | [kendala]` sebelum pukul 09.00 pagi jika diperlukan).
- **Aturan Pesan Commit (Wajib per PBI & Berkala):**
  * Lakukan *commit* secara berkala untuk setiap perubahan kecil yang fungsional.
  * **Format Wajib:** `<Key-JIRA> <Awalan>: <keterangan_jelas>` (Tanpa tanda kurung siku pada Key JIRA).
  * **Contoh Benar:** `SPK-13 Feat: menambahkan validasi upload file PDF` atau `SPK-13 Feat: membuat skema database pendaftaran`.
  * **Daftar Awalan Commit Resmi:**
    - `Feat`: Fitur baru
    - `Fix`: Perbaikan *bug*
    - `Chore`: Pemeliharaan rutin & pembaruan dependensi
    - `Docs`: Perubahan dokumentasi
    - `Refactor`: Perombakan struktur kode tanpa mengubah fungsi
    - `Test`: Penambahan atau pembaruan skrip *testing*
    - `Style`: Merapikan sintaks, format, & spasi
    - `Perf`: Optimasi performa
    - `CI` / `Build`: Konfigurasi server atau skrip otomatis
- **Aturan Pengiriman (Push) & Pull Request (PR):**
  * Push kode dilakukan ke cabang pribadi: `git push origin [nama-anda]`.
  * Buat Pull Request (PR) dari cabang `[nama-anda]` ke `main`.
  * **Format Judul PR Wajib:** `<Key-JIRA> <Deskripsi Implementasi>` (Contoh: `SPK-13 Implementasi Upload PDF` atau `SPK-13 Implementasi Form Pendaftaran`).
  * Pantau GitHub Actions (CI) hingga mendapatkan centang hijau (`laravel-tests` lolos).
  * Proses **Merge PR hanya boleh dilakukan oleh PM (`Hainurr`)**, kemudian status tiket JIRA digeser ke **In Review** atau **Done**.

## 4. Standar Koding, Pengujian Lokal (Dusk) & Dokumentasi AI
- **Pemisahan Logika & Server:**
  * Setiap logika bisnis wajib diimplementasikan di *Controller*, bukan di *Routes*.
  * Dilarang menghapus atau mengubah file `Procfile` di *root directory* (`web: vendor/bin/heroku-php-apache2 public/`).
- **Kewajiban Testing Lokal Sebelum Push:**
  * Setelah kode satu PBI selesai ditulis, **dilarang langsung melakukan `git push`**.
  * Wajib menjalankan pengujian **Laravel Dusk** (`php artisan dusk`) dan tes fungsional (`php artisan test`) secara lokal terlebih dahulu. Pastikan fitur memenuhi *Acceptance Criteria* JIRA dan tidak merusak alur yang ada.
  * Jika sudah 100% lulus tes lokal, lakukan *commit* penutup khusus pengujian (Contoh: `SPK-13 Test: lolos pengujian Dusk untuk form daftar`) sebelum melakukan `git push origin [nama-anda]`.
- **Asistensi Dokumentasi Prompt AI (Wajib bagi AI Agent):**
  * Setiap anggota tim wajib mencatat *prompt* yang dikirimkan ke AI Agent.
  * Setiap kali satu sesi pengerjaan PBI selesai (atau ketika diminta oleh *developer*), **AI Agent WAJIB membantu merangkum dan menuliskan daftar riwayat *prompt*** yang telah diberikan oleh *developer* selama sesi tersebut dalam format daftar yang rapi agar siap disalin ke dokumen log/laporan tim.