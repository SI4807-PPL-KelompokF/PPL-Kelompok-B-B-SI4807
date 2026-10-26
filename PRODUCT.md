# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users
Mahasiswa dan dosen internal kampus Telkom University (pemohon pengujian sampel), laboran/teknisi laboratorium (pemroses pengujian, verifikator dokumen, dan input hasil uji), serta admin sistem/kepala laboratorium (pengelola akun, konfigurasi layanan lab, dan monitoring pengujian).

## Product Purpose
Sistem informasi terpadu pengajuan, verifikasi, dan pelacakan pengujian sampel di laboratorium kampus Telkom University (Telyuji). Memungkinkan pemohon mengajukan sampel & mengunggah dokumen persyaratan, laboran mengelola antrean sampel serta mengunggah lembar hasil uji resmi, dan admin memantau seluruh proses operasional secara transparan dan akuntabel.

## Positioning
Sistem layanan laboratorium akademik Telkom University yang transparan, bebas friksi administrasi manual, dengan pelacakan status pengujian sampel secara real-time, validasi dokumen digital, serta antarmuka yang presisi dan bersih berbasis Tailwind CSS dan protokol desain Telyuji.

## Operating Context
Operasional laboratorium sains dan teknik di lingkungan Telkom University: penyerahan sampel fisik, pengunggahan berkas pendukung (surat pengantar/metode uji), verifikasi kelayakan sampel oleh laboran, proses pengujian alat laboratorium, dan penerbitan laporan hasil uji (LHU).

## Capabilities and Constraints
- Autentikasi dan Otorisasi Multi-peran (Role-Based Access Control): pemohon, laboran, admin.
- Alur pengajuan pengujian sampel mandiri dengan unggah dokumen pendukung.
- Verifikasi dan input hasil uji sampel oleh laboran.
- Dashboard operasional pelacakan progres pengujian (Draft, Diajukan, Sedang Diuji, Selesai, Ditolak).
- Kepatuhan protokol Telyuji: font Inter, utilitas murni Tailwind CSS (tanpa tag `<style>` kustom), palet warna Telyuji.
- Otomasi pengujian antarmuka dengan Laravel Dusk.

## Brand Commitments
- Nama Produk: Telyuji
- Tipografi: Inter (hierarki, judul, data, form)
- Palet Warna Telyuji:
  - Racing Red (`#ED1E28`): Sidebar, identitas brand utama, aksi krusial (Simpan/Kirim), penanda aktif.
  - Tiger Flame (`#F26A2D`): Efek hover tombol utama, metrik ringkasan, elemen sekunder.
  - Jasmine (`#F7D488`): Status pending/menunggu, kartu peringatan, badge informatif.
  - Cream (`#EAEFB1`): Latar belakang panel ringkasan, aksen ringan, zebra striping tabel.
  - Frosted Mint (`#E9F7CA`): Status sukses/selesai, metrik positif, indikator konfirmasi.

## Evidence on Hand
- Skema database tabel `users` dengan enum role `['admin', 'laboran', 'pemohon']`.
- Laravel Dusk framework testing terpasang di `tests/Browser`.
- Master layout Blade dan modul autentikasi siap dikonversi ke Tailwind CSS.

## Product Principles
1. Desain Presisi Tanpa Ornamen Semu (Anti-slop, larangan gradient/glow/glassmorphism tak berdasar, pemanfaatan warna untuk data dan status).
2. Transparansi & Ketertelusuran (Setiap permohonan memiliki rekam jejak status, tanggal, dan laboran penanggung jawab).
3. Efisiensi Kerja Laboratorium (Tindakan utama mudah diakses dalam 1-klik, form jelas dan minim redundansi input).

## Accessibility & Inclusion
- Rasio kontras teks minimum 4.5:1 untuk teks isi dan 3:1 untuk teks besar.
- Focus ring jelas pada seluruh elemen interaktif keyboard.
- Label form eksplisit dan semantic markup HTML5.
