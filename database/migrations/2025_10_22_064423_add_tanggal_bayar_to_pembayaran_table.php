<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dateTime('tanggal_bayar')->nullable()->after('tanggal_upload');
            $table->text('catatan_admin')->nullable()->after('admin_id');
        });
    }

    public function down()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn(['tanggal_bayar', 'catatan_admin']);
        });
    }
};