<?php
// database/migrations/xxxx_xx_xx_xxxxxx_update_pembayaran_tahun_data.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Update data pembayaran SPP yang sudah ada
        // Isi field tahun dari tanggal_proses atau created_at
        DB::table('pembayarans')
            ->whereNull('tagihan_id') // Hanya pembayaran SPP
            ->where('status', 'accepted') // Hanya yang sudah diterima
            ->whereNull('tahun') // Hanya yang belum ada tahun
            ->update([
                'tahun' => DB::raw('COALESCE(YEAR(tanggal_proses), YEAR(created_at))'),
                'updated_at' => now()
            ]);

        // Untuk yang sudah ada tahun tapi tidak ada bulan, isi bulan_mulai dan bulan_akhir
        DB::table('pembayarans')
            ->whereNull('tagihan_id')
            ->where('status', 'accepted')
            ->whereNotNull('tahun')
            ->whereNull('bulan_mulai')
            ->whereNull('bulan_akhir')
            ->update([
                'bulan_mulai' => DB::raw('MONTH(COALESCE(tanggal_proses, created_at))'),
                'bulan_akhir' => DB::raw('MONTH(COALESCE(tanggal_proses, created_at))'),
                'updated_at' => now()
            ]);
    }

    public function down()
    {
        // Optional rollback
    }
};