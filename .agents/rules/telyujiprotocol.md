---
trigger: always_on
---

# PROTOKOL SINKRONISASI AI AGENT - TIM TELYUJI

## 1. Konteks Proyek & Lingkungan Kerja
* **Proyek:** Telyuji (Sistem Informasi berbasis Web).
* **Tech Stack:** Laravel 8, Tailwind CSS, Blade Templates.
* **Lingkungan AI:** Antigravity IDE.
* **Ekstensi Desain:** Terintegrasi dengan `taste-skill` (referensi gaya/komponen) dan `impeccable` (design hook & linter) yang terpasang di dalam direktori `.agents`.

## 2. Standar UI/UX & Desain Visual
Saat menghasilkan atau memodifikasi kode antarmuka (UI), AI Agent WAJIB mematuhi spesifikasi desain berikut tanpa pengecualian:
* **Pengawasan Desain:** Seluruh komponen visual harus merujuk pada standar `taste-skill` dan lolos validasi _hook_ dari `impeccable`. Dilarang menghasilkan CSS kustom (`<style>`), wajib menggunakan utility class dari Tailwind CSS.
* **Tipografi:** Gunakan font **Inter** untuk seluruh antarmuka (hierarki, judul, teks data, form).
* **Palet Warna Utama (Berdasarkan Desain Sistem Telyuji):**
  * **Racing Red (#ED1E28):** Gunakan untuk sidebar, identitas brand utama, tombol aksi krusial (Simpan/Kirim), dan penanda status aktif[cite: 1].
  * **Tiger Flame (#F26A2D):** Gunakan untuk efek _hover_ pada tombol, kartu ringkasan data (seperti metrik MRR pada dashboard), dan elemen pendukung sekunder[cite: 1].
  * **Jasmine (#F7D488):** Gunakan untuk kartu peringatan, status pending, atau _badge_ informasi[cite: 1].
  * **Cream (#EAEFB1):** Gunakan untuk aksen latar belakang ringan, panel ringkasan, atau baris tabel bergantian (zebra striping) agar mata tidak lelah[cite: 1].
  * **Frosted Mint (#E9F7CA):** Gunakan untuk latar belakang status sukses, metrik positif (seperti tingkat penurunan churn), atau indikator konfirmasi[cite: 1].
* **Struktur Blade:** Gunakan Master Layout (`@extends('layouts.app')`). Jangan membuat tag `<html>` atau `<body>` berulang. Gunakan Blade Components (`<x-...>`) untuk elemen yang dipakai berulang.

## 3. Alur Kerja Git, Jira & Percabangan (Strict)
AI Agent harus memahami dan membantu _developer_ dalam menerapkan siklus Git berikut:
* **Cabang (Branch):** Dilarang bekerja di `main`. Setiap tiket PBI wajib memiliki cabang tersendiri.
* **Format Nama Cabang:** `<tipe>/<ID-JIRA>-<deskripsi>` (Contoh: `feature/SPK-13-upload-dokumen` atau `bugfix/SPK-07-error-form`).
* **Siklus Hidup Cabang:** Gunakan prinsip "Hapus dan Perbarui". Setelah _Pull Request_ di-merge, cabang lokal harus dihapus. Pekerjaan baru atau perbaikan _bug_ pasca-rilis memerlukan cabang baru yang ditarik dari kondisi `main` terbaru.
* **Pesan Commit:** Wajib menggunakan Smart Commits (Contoh: `[PBI-13] Feat: tambah form hasil test`).

## 4. Standar Koding & Pengujian
* Setiap logika bisnis harus diimplementasikan di Controller, bukan di Routes.
* Setiap fitur yang berinteraksi dengan antarmuka pengguna wajib disertai atau mendukung pembuatan pengujian otomatis menggunakan **Laravel Dusk**.