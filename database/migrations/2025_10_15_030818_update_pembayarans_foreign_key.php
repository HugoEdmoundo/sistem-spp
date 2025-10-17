<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            // Hapus foreign key constraint lama jika ada
            $table->dropForeign(['tagihan_id']);
            
            // Tambah foreign key constraint baru yang mengizinkan null
            $table->foreign('tagihan_id')
                  ->references('id')
                  ->on('tagihans')
                  ->onDelete('cascade')
                  ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropForeign(['tagihan_id']);
            
            // Kembalikan ke constraint lama
            $table->foreign('tagihan_id')
                  ->references('id')
                  ->on('tagihans')
                  ->onDelete('cascade');
        });
    }
};