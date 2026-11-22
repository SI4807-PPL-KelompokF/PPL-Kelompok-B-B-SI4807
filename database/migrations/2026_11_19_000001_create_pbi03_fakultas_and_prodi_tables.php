<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePbi03FakultasAndProdiTables extends Migration
{
    /**
     * Run the migrations.
     * PBI-03 (UC-04): Master Data Fakultas dan Program Studi untuk role Admin.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('fakultas')) {
            Schema::create('fakultas', function (Blueprint $table) {
                $table->id();
                $table->string('kode_fakultas', 30)->unique();
                $table->string('nama_fakultas', 150)->unique();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('prodi')) {
            Schema::create('prodi', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fakultas_id')->constrained('fakultas')->cascadeOnDelete();
                $table->string('kode_prodi', 30)->nullable();
                $table->string('nama_prodi', 150);
                $table->timestamps();

                $table->index('fakultas_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prodi');
        Schema::dropIfExists('fakultas');
    }
}
