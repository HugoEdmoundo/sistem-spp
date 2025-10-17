<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            // Ubah tagihan_id menjadi nullable
            $table->foreignId('tagihan_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->foreignId('tagihan_id')->nullable(false)->change();
        });
    }
};