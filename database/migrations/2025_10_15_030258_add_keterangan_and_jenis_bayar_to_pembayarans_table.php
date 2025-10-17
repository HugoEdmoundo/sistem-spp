<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->string('keterangan')->nullable()->after('status');
            $table->enum('jenis_bayar', ['lunas', 'cicilan'])->default('lunas')->after('keterangan');
        });
    }

    public function down()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn(['keterangan', 'jenis_bayar']);
        });
    }
};