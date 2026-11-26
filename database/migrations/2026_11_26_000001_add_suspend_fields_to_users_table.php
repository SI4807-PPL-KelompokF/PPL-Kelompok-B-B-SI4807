<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSuspendFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     * PBI-02 / SPK-2: Manajemen Pelanggaran & Penangguhan/Suspend Sementara Akun Pemohon oleh Laboran.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_suspended')) {
                $table->boolean('is_suspended')->default(false)->after('status_validasi_ktm');
            }
            if (!Schema::hasColumn('users', 'alasan_suspend')) {
                $table->text('alasan_suspend')->nullable()->after('is_suspended');
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
            if (Schema::hasColumn('users', 'alasan_suspend')) {
                $cols[] = 'alasan_suspend';
            }
            if (Schema::hasColumn('users', 'is_suspended')) {
                $cols[] = 'is_suspended';
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
}
