<?php
// database/migrations/xxxx_xx_xx_xxxxxx_update_dates_to_timestamps_in_pembayarans_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            // Ubah tanggal_upload dari string/date menjadi timestamp
            $table->timestamp('tanggal_upload')->useCurrent()->change();
            
            // Ubah tanggal_proses dari string/date menjadi timestamp nullable
            $table->timestamp('tanggal_proses')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            // Kembalikan ke string jika rollback
            $table->string('tanggal_upload')->change();
            $table->string('tanggal_proses')->nullable()->change();
        });
    }
};