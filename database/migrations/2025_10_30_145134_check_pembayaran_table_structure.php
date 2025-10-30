<?php
// database/migrations/xxxx_xx_xx_xxxxxx_check_pembayaran_table_structure.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Cek apakah kolom yang diperlukan sudah ada
        if (!Schema::hasColumn('pembayarans', 'tahun')) {
            Schema::table('pembayarans', function (Blueprint $table) {
                $table->integer('tahun')->nullable()->after('keterangan');
            });
        }
        
        if (!Schema::hasColumn('pembayarans', 'bulan_mulai')) {
            Schema::table('pembayarans', function (Blueprint $table) {
                $table->integer('bulan_mulai')->nullable()->after('tahun');
            });
        }
        
        if (!Schema::hasColumn('pembayarans', 'bulan_akhir')) {
            Schema::table('pembayarans', function (Blueprint $table) {
                $table->integer('bulan_akhir')->nullable()->after('bulan_mulai');
            });
        }
    }

    public function down()
    {
        // Optional: rollback
    }
};