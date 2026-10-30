---
name: Telyuji
description: Sistem Informasi Pengujian Laboratorium Telkom University
colors:
  primary: "#ED1E28"
  primary-hover: "#F26A2D"
  warning: "#F7D488"
  accent-subtle: "#EAEFB1"
  success-subtle: "#E9F7CA"
  neutral-bg: "#F9FAFB"
  neutral-surface: "#FFFFFF"
  neutral-border: "#E5E7EB"
  neutral-text: "#111827"
  neutral-text-muted: "#6B7280"
typography:
  display:
    fontFamily: "'General Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif"
    fontSize: "clamp(2rem, 5vw, 3rem)"
    fontWeight: 700
    lineHeight: 1.1
    letterSpacing: "-0.015em"
  headline:
    fontFamily: "'General Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif"
    fontSize: "1.5rem"
    fontWeight: 700
    lineHeight: 1.25
    letterSpacing: "-0.015em"
  title:
    fontFamily: "'General Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif"
    fontSize: "1.125rem"
    fontWeight: 600
    lineHeight: 1.35
    letterSpacing: "normal"
  body:
    fontFamily: "'General Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif"
    fontSize: "0.875rem"
    fontWeight: 400
    lineHeight: 1.5
    letterSpacing: "normal"
  label:
    fontFamily: "'General Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif"
    fontSize: "0.75rem"
    fontWeight: 600
    lineHeight: 1.25
    letterSpacing: "normal"
rounded:
  sm: "4px"
  md: "6px"
  lg: "8px"
spacing:
  sm: "8px"
  md: "16px"
  lg: "24px"
  xl: "32px"
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "#FFFFFF"
    rounded: "{rounded.md}"
    padding: "10px 18px"
  button-primary-hover:
    backgroundColor: "{colors.primary-hover}"
---

# Design System: Telyuji

## Overview

**Creative North Star: "The Precision Testing Lab"**

Telyuji adalah sistem antarmuka berbasis web untuk manajemen pengujian laboratorium civitas akademika Telkom University. Filosofi desainnya mengedepankan presisi teknis, keterbacaan data instrumen, dan efisiensi operasional tanpa ornamen visual berlebihan (anti-slop).

Setiap elemen visual dirancang fungsional: status sampel terlihat seketika melalui warna semantik yang terkontrol, data disajikan pada permukaan datar (flat surfaces) yang bersih, dan navigasi memberikan kepastian langkah bagi pemohon maupun laboran.

**Key Characteristics:**
- Tipografi murni menggunakan font General Sans (Indian Type Foundry) pada seluruh hierarki antarmuka.
- Pemanfaatan palet warna khas Telyuji untuk identitas brand dan status data.
- Kepatuhan utilitas Tailwind CSS v3 tanpa tag `<style>` kustom.
- Permukaan datar dengan border tipis (1px) berlayer lembut, tanpa efek gradien atau bayangan dramatis.

## Colors

Palet warna Telyuji menggabungkan warna korporat Telkom University (merah & oranye) dengan warna semantik status laboratorium (mint, jasmine, cream).

### Primary
- **Racing Red** (`#ED1E28`): Identitas brand utama, sidebar operasional, tombol aksi penting (Simpan, Kirim, Pengajuan Baru), dan penanda status aktif.

### Secondary
- **Tiger Flame** (`#F26A2D`): Efek hover pada tombol aksi utama, penanda data metrik krusial, dan elemen visual pendukung sekunder.

### Tertiary
- **Jasmine** (`#F7D488`): Penanda status menunggu (pending/verifikasi), kartu peringatan administratif, dan badge antrean.
- **Frosted Mint** (`#E9F7CA`): Penanda status sukses, pengujian selesai, indikator hasil uji valid, dan konfirmasi positif.
- **Cream** (`#EAEFB1`): Latar belakang panel ringkasan data, aksen badge peran pengguna, dan selang-seling baris tabel agar mata tidak lelah.

### Neutral
- **Page Background** (`#F9FAFB` / `bg-gray-50`): Latar utama kanvas aplikasi.
- **Surface** (`#FFFFFF` / `bg-white`): Permukaan kartu, panel, dan tabel.
- **Border** (`#E5E7EB` / `border-gray-200`): Pemisah batas kontainer dan kolom data.
- **Text Main** (`#111827` / `text-gray-900`): Warna teks judul dan data penting.
- **Text Muted** (`#6B7280` / `text-gray-500`): Keterangan sekunder, timestamp, dan label pembantu.

### Named Rules
**The Rarity of Red Rule.** Racing Red adalah warna penentu aksi dan status. Jangan gunakan Racing Red untuk latar belakang konten luas atau teks paragraf biasa; kekuatannya terletak pada fokus aksi.

**The Functional Color Rule.** Warna tidak pernah menjadi ornamen semata. Setiap warna pada kartu atau badge menyatakan kondisi faktual sampel uji (Jasmine = Menunggu, Tiger Flame = Sedang Diuji, Mint = Selesai).

## Typography

**Display Font:** General Sans (dengan fallback system sans)
**Body Font:** General Sans (dengan fallback system sans)
**Label/Mono Font:** General Sans (angka tabular `tabular-nums` untuk nilai numerik data uji)

**Character:** Tipografi presisi, bersih, dan berbobot seimbang khas sistem instrumen modern.

### Hierarchy
- **Display** (700 / Bold, 2.25rem - 3rem, line-height 1.1): Judul besar landing page dan angka metrik utama.
- **Headline** (700 / Bold, 1.5rem, line-height 1.25): Judul halaman modul dan nama bagian penting.
- **Title** (600 / Semibold, 1.125rem, line-height 1.35): Judul kartu modul, nama tabel, dan modal header.
- **Body** (400 / Regular, 0.875rem, line-height 1.5, max measure 65–75ch): Teks isi formulir, deskripsi sampel, dan keterangan alur kerja.
- **Label** (600 / Semibold, 0.75rem, tracking normal): Label status, badge peran, dan header kolom tabel.

### Named Rules
**The Single Family Rule.** Seluruh antarmuka menggunakan font General Sans (Indian Type Foundry). Monospace hanya diperbolehkan untuk kode sampel/ID laboratorium unik jika diperlukan.

## Layout

Sistem tata letak menggunakan CSS Grid dan Flexbox bawaan Tailwind CSS dengan kontainer berpusat (`max-w-7xl mx-auto px-4 sm:px-6 lg:px-8`).
- Header lengket (sticky) di bagian atas dengan tinggi konsisten 64px (`h-16`).
- Jarak antar bagian utama menggunakan skala kelipatan 16px (`gap-4`, `gap-6`, `space-y-6`).
- Tata letak responsif penuh dari layar smartphone (390px), tablet (768px), hingga desktop (1440px).

## Elevation & Depth

Telyuji mengadopsi prinsip desain flat kontemporer. Kedalaman permukaan tidak diciptakan oleh bayangan gelap yang tebal, melainkan oleh garis batas 1px (`border border-gray-200`) dan kontras warna latar (`bg-gray-50` vs `bg-white`).

### Shadow Vocabulary
- **Surface Elevation** (`shadow-sm`): Hanya digunakan pada tombol utama dan kartu melayang ringan untuk respons visual.
- **Resting Surface** (`shadow-none`): Seluruh kartu, formulir, dan tabel dalam keadaan diam tidak menggunakan bayangan dekoratif.

### Named Rules
**The Flat Border Rule.** Kedalaman antarmuka diwujudkan melalui garis pemisah 1px yang tegas dan presisi, bukan bayangan kabur atau efek glow.

## Shapes

- **Sudut Elemen:** Tombol dan form input menggunakan sudut lembut 6px (`rounded-md`). Kontainer kartu dan panel menggunakan sudut 8px (`rounded-lg`).
- **Pills:** Bentuk pil melingkar (`rounded-full`) hanya diizinkan untuk badge status ringkas atau indikator kuantitas.

## Components

### Buttons
- **Shape:** Sedikit melengkung 6px (`rounded-md`).
- **Primary:** Latar Racing Red (`bg-racing-red`), teks putih, padding `py-2.5 px-4`, hover ke Tiger Flame (`hover:bg-tiger-flame`).
- **Secondary:** Latar putih, border abu-abu (`border-gray-300`), teks abu-abu gelap, hover ke `bg-gray-50`.
- **Focus Ring:** Ring 2px fokus warna Racing Red (`focus:ring-2 focus:ring-racing-red focus:outline-none`).

### Inputs / Form Fields
- **Style:** Border 1px abu-abu (`border-gray-300`), latar putih, teks 14px (`text-sm`), rounded-md.
- **Focus:** Border berubah menjadi Racing Red dengan ring 1px (`focus:border-racing-red focus:ring-1 focus:ring-racing-red`).
- **Error:** Border Racing Red (`border-racing-red`), pesan galat merah di bawah input.

### Status Badges
- **Menunggu Verifikasi:** Latar Jasmine (`bg-jasmine/30 text-amber-900 border border-amber-300`).
- **Sedang Diproses:** Latar Tiger Flame lembut (`bg-orange-50 text-tiger-flame border border-orange-200`).
- **Selesai / LHU Terbit:** Latar Frosted Mint (`bg-frosted-mint text-emerald-900 border border-emerald-300`).

## Do's and Don'ts

### Do:
- **Do** selalu gunakan utility class Tailwind CSS v3 murni.
- **Do** gunakan `@extends('layouts.app')` untuk setiap view Blade aplikasi.
- **Do** pasang label deskriptif eksplisit pada setiap elemen form pengujian sampel.
- **Do** gunakan token warna Telyuji (`racing-red`, `tiger-flame`, `jasmine`, `cream`, `frosted-mint`).

### Don't:
- **Don't** gunakan tag `<style>` kustom di dalam file Blade template.
- **Don't** gunakan efek gradien dekoratif (gradient background / gradient text).
- **Don't** gunakan bayangan gelap tebal atau efek glow halo pada elemen diam.
- **Don't** gunakan font lain selain General Sans di antarmuka sistem.
- **Don't** gunakan emoji sebagai ikon antarmuka; gunakan ikon SVG yang konsisten.
