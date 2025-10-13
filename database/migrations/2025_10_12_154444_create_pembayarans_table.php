<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_pembayarans_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('metode', 50)->nullable();
            $table->string('bukti')->nullable();
            $table->decimal('jumlah', 12, 2);
            $table->enum('status', ['pending','accepted','rejected'])->default('pending');
            $table->timestamp('tanggal_upload')->useCurrent();
            $table->timestamp('tanggal_proses')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayarans');
    }
};