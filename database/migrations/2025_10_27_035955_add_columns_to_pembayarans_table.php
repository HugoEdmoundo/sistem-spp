<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            // Kolom yang mungkin belum ada
            if (!Schema::hasColumn('pembayarans', 'alasan_reject')) {
                $table->text('alasan_reject')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('pembayarans', 'tanggal_bayar')) {
                $table->dateTime('tanggal_bayar')->nullable()->after('tanggal_upload');
            }
            
            if (!Schema::hasColumn('pembayarans', 'catatan_admin')) {
                $table->text('catatan_admin')->nullable()->after('alasan_reject');
            }
            
            if (!Schema::hasColumn('pembayarans', 'admin_id')) {
                $table->foreignId('admin_id')->nullable()->constrained('users')->after('user_id');
            }
            
            if (!Schema::hasColumn('pembayarans', 'tanggal_proses')) {
                $table->dateTime('tanggal_proses')->nullable()->after('tanggal_bayar');
            }
        });
    }

    public function down()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            // Hapus kolom jika rollback
            $columns = ['alasan_reject', 'tanggal_bayar', 'catatan_admin', 'admin_id', 'tanggal_proses'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('pembayarans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};