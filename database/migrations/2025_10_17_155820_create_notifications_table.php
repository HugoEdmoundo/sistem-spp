<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_notifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // jenis notifikasi: pembayaran_baru, status_pembayaran, dll
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // data tambahan dalam format JSON
            $table->timestamp('read_at')->nullable(); // kapan dibaca
            $table->string('related_type')->nullable(); // model terkait: App\Models\Pembayaran, dll
            $table->unsignedBigInteger('related_id')->nullable(); // ID model terkait
            $table->timestamps();

            // Indexes untuk performa
            $table->index(['user_id', 'read_at']);
            $table->index(['related_type', 'related_id']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};