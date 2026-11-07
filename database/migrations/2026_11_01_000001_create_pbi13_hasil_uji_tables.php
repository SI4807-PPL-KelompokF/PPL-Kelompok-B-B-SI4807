<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePbi13HasilUjiTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tiket_pengujian', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tiket')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_pemohon');
            $table->string('nim_nip');
            $table->string('fakultas');
            $table->string('nama_sampel');
            $table->string('instrumen');
            $table->string('laboratorium');
            $table->enum('status', ['Menunggu Verifikasi', 'Sedang Diuji', 'Selesai'])->default('Sedang Diuji');
            $table->date('tanggal_uji');
            $table->timestamps();
        });

        Schema::create('dokumen_hasil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_pengujian_id')->unique()->constrained('tiket_pengujian')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_berkas_asli');
            $table->string('path_berkas');
            $table->string('ekstensi', 16);
            $table->string('mime_type', 120);
            $table->unsignedInteger('ukuran_bytes');
            $table->unsignedInteger('versi')->default(1);
            $table->text('catatan_laboran')->nullable();
            $table->string('enkripsi_algoritma', 40)->default('AES-256-CBC');
            $table->string('sha256_checksum', 64)->nullable();
            $table->timestamps();
        });

        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_pengujian_id')->constrained('tiket_pengujian')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_aktor');
            $table->string('aksi', 50);
            $table->string('berkas_lama')->nullable();
            $table->string('berkas_baru');
            $table->unsignedInteger('ukuran_bytes');
            $table->unsignedInteger('versi');
            $table->text('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_trails');
        Schema::dropIfExists('dokumen_hasil');
        Schema::dropIfExists('tiket_pengujian');
    }
}
