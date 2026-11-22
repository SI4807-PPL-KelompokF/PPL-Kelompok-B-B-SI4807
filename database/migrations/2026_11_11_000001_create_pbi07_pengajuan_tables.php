<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePbi07PengajuanTables extends Migration
{
    /**
     * PBI-07 (UC-07): Tabel mesin_lab untuk katalog mesin dan tabel pengajuan uji lab.
     * PBI-08 (UC-08): Kolom barcode_path pada tiket_pengujian untuk menyimpan path resi PDF.
     * PBI-09 (UC-09): Kolom timeline status pada tiket_pengujian.
     */
    public function up()
    {
        // Tabel mesin lab (katalog instrumen)
        if (!Schema::hasTable('mesin_lab')) {
            Schema::create('mesin_lab', function (Blueprint $table) {
                $table->id();
                $table->string('nama_mesin');
                $table->string('kategori_layanan')->nullable();
                $table->string('laboratorium');
                $table->enum('status_mesin', ['Aktif', 'Maintenance'])->default('Aktif');
                $table->unsignedInteger('kuota_harian')->default(5);
                $table->unsignedInteger('estimasi_sla_hari')->default(3);
                $table->text('sop_penyiapan')->nullable();
                $table->timestamps();
            });
        }

        // Tambah kolom pada tiket_pengujian jika belum ada
        if (Schema::hasTable('tiket_pengujian')) {
            Schema::table('tiket_pengujian', function (Blueprint $table) {
                if (!Schema::hasColumn('tiket_pengujian', 'mesin_lab_id')) {
                    $table->unsignedBigInteger('mesin_lab_id')->nullable()->after('fakultas');
                }
                if (!Schema::hasColumn('tiket_pengujian', 'deskripsi_sampel')) {
                    $table->text('deskripsi_sampel')->nullable()->after('nama_sampel');
                }
                if (!Schema::hasColumn('tiket_pengujian', 'tujuan_pengujian')) {
                    $table->text('tujuan_pengujian')->nullable()->after('deskripsi_sampel');
                }
                if (!Schema::hasColumn('tiket_pengujian', 'jenis_sampel')) {
                    $table->enum('jenis_sampel', ['fisik', 'digital'])->default('fisik')->after('tujuan_pengujian');
                }
                if (!Schema::hasColumn('tiket_pengujian', 'tautan_file_cloud')) {
                    $table->string('tautan_file_cloud')->nullable()->after('jenis_sampel');
                }
                if (!Schema::hasColumn('tiket_pengujian', 'barcode_token')) {
                    $table->string('barcode_token', 64)->nullable()->unique()->after('tautan_file_cloud');
                }
                if (!Schema::hasColumn('tiket_pengujian', 'barcode_path')) {
                    $table->string('barcode_path')->nullable()->after('barcode_token');
                }
                if (!Schema::hasColumn('tiket_pengujian', 'prodi')) {
                    $table->string('prodi')->nullable()->after('fakultas');
                }
                if (!Schema::hasColumn('tiket_pengujian', 'submitted_at')) {
                    $table->timestamp('submitted_at')->nullable()->after('tanggal_uji');
                }
                if (!Schema::hasColumn('tiket_pengujian', 'received_at')) {
                    $table->timestamp('received_at')->nullable()->after('submitted_at');
                }
                if (!Schema::hasColumn('tiket_pengujian', 'testing_at')) {
                    $table->timestamp('testing_at')->nullable()->after('received_at');
                }
                if (!Schema::hasColumn('tiket_pengujian', 'completed_at')) {
                    $table->timestamp('completed_at')->nullable()->after('testing_at');
                }
                if (!Schema::hasColumn('tiket_pengujian', 'rejected_at')) {
                    $table->timestamp('rejected_at')->nullable()->after('completed_at');
                }
                if (!Schema::hasColumn('tiket_pengujian', 'alasan_penolakan')) {
                    $table->text('alasan_penolakan')->nullable()->after('rejected_at');
                }
            });
        }

        // Modifikasi enum status agar mendukung alur lengkap
        // (MySQL enum modification via raw statement)
        if (Schema::hasTable('tiket_pengujian')) {
            try {
                \DB::statement("ALTER TABLE tiket_pengujian MODIFY COLUMN status ENUM('Menunggu Verifikasi','Dalam Antrean','Sedang Diuji','Selesai','Ditolak') DEFAULT 'Menunggu Verifikasi'");
            } catch (\Throwable $e) {
                // SQLite atau database lain tidak mendukung ALTER COLUMN enum
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('mesin_lab');

        if (Schema::hasTable('tiket_pengujian')) {
            Schema::table('tiket_pengujian', function (Blueprint $table) {
                $cols = [
                    'mesin_lab_id', 'deskripsi_sampel', 'tujuan_pengujian',
                    'jenis_sampel', 'tautan_file_cloud', 'barcode_token',
                    'barcode_path', 'prodi', 'submitted_at', 'received_at',
                    'testing_at', 'completed_at', 'rejected_at', 'alasan_penolakan',
                ];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('tiket_pengujian', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
}

