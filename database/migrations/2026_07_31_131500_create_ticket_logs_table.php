<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_id');
            $table->foreignId('user_id')->nullable();
            $table->string('aktivitas');
            $table->json('data_sebelum')->nullable();
            $table->json('data_sesudah')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('ticket_id')->references('ticket_id')->on('tickets')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_logs');
    }
};
