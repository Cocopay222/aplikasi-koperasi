<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('instansi_aplikasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instansi_id');
            $table->unsignedBigInteger('aplikasi_id');
            
            $table->foreign('instansi_id')->references('instansi_id')->on('instansis')->onDelete('cascade');
            $table->foreign('aplikasi_id')->references('aplikasi_id')->on('master_aplikasis')->onDelete('cascade');
            
            // Ensures an institution can't have duplicate apps in the pivot
            $table->unique(['instansi_id', 'aplikasi_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instansi_aplikasi');
    }
};
