<?php
// database/migrations/xxxx_xx_xx_xxxxxx_update_tanggal_upload_to_timestamp_in_pembayarans_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            // Ubah tanggal_upload dari string menjadi timestamp
            $table->timestamp('tanggal_upload')->useCurrent()->change();
        });
    }

    public function down()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            // Kembalikan ke string jika rollback
            $table->string('tanggal_upload')->change();
        });
    }
};