<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPbi01FieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     * PBI-01: Profil Akademik & Validasi Identitas KTM/ID untuk role Pemohon.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nama_lengkap')) {
                $table->string('nama_lengkap', 150)->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'nomor_induk')) {
                $table->string('nomor_induk', 50)->nullable()->unique()->after('nama_lengkap');
            }
            if (!Schema::hasColumn('users', 'fakultas_id')) {
                $table->string('fakultas_id', 150)->nullable()->after('nomor_induk');
            }
            if (!Schema::hasColumn('users', 'prodi_id')) {
                $table->string('prodi_id', 150)->nullable()->after('fakultas_id');
            }
            if (!Schema::hasColumn('users', 'foto_identitas')) {
                $table->string('foto_identitas')->nullable()->after('prodi_id');
            }
            if (!Schema::hasColumn('users', 'status_validasi_ktm')) {
                $table->string('status_validasi_ktm', 50)->default('Belum Tervalidasi')->after('foto_identitas');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = [];
            foreach (['status_validasi_ktm', 'foto_identitas', 'prodi_id', 'fakultas_id', 'nomor_induk', 'nama_lengkap'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $cols[] = $col;
                }
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
}
