<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_spp_settings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('spp_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('nominal', 12, 2);
            $table->date('berlaku_mulai');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('spp_settings');
    }
};