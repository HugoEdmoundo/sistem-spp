<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            // Tambah kolom alasan_reject setelah status
            if (!Schema::hasColumn('pembayarans', 'alasan_reject')) {
                $table->text('alasan_reject')->nullable()->after('status');
            }
            
            // Tambah kolom catatan_admin setelah alasan_reject
            if (!Schema::hasColumn('pembayarans', 'catatan_admin')) {
                $table->text('catatan_admin')->nullable()->after('alasan_reject');
            }
        });
    }

    public function down()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            if (Schema::hasColumn('pembayarans', 'alasan_reject')) {
                $table->dropColumn('alasan_reject');
            }
            
            if (Schema::hasColumn('pembayarans', 'catatan_admin')) {
                $table->dropColumn('catatan_admin');
            }
        });
    }
};