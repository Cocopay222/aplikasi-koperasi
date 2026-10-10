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
        Schema::create('implementasi_koperasi', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_implementasi')->unique();
            $table->unsignedBigInteger('instansi_id'); // Koperasi
            $table->unsignedBigInteger('aplikasi_id')->nullable();
            $table->date('tanggal_pelatihan')->nullable();
            $table->string('metode_pelatihan')->nullable();
            $table->string('nama_trainer')->nullable();
            $table->unsignedBigInteger('pic_sakti_id')->nullable();
            $table->string('pic_koperasi')->nullable();
            $table->string('kontak_pic')->nullable();
            $table->text('catatan_pelatihan')->nullable();
            $table->date('target_go_live')->nullable();
            $table->date('tanggal_cut_off')->nullable();
            $table->string('status')->default('Belum Dimulai');
            $table->decimal('progres', 5, 2)->default(0);
            
            // Next Action columns
            $table->string('tindakan_berikutnya')->nullable();
            $table->string('pic_tindakan')->nullable();
            $table->date('target_tanggal_tindakan')->nullable();
            $table->string('status_tindakan')->nullable();
            
            $table->timestamps();

            // Foreign keys
            $table->foreign('instansi_id')->references('instansi_id')->on('instansis')->onDelete('cascade');
            $table->foreign('aplikasi_id')->references('aplikasi_id')->on('master_aplikasis')->onDelete('set null');
            $table->foreign('pic_sakti_id')->references('user_id')->on('users')->onDelete('set null');
        });

        Schema::create('implementasi_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('implementasi_id')->constrained('implementasi_koperasi')->onDelete('cascade');
            $table->string('nama_item');
            $table->string('kategori')->nullable();
            $table->string('status')->default('Belum Dikirim');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('implementasi_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('implementasi_id')->constrained('implementasi_koperasi')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('aktivitas');
            $table->json('data_sebelum')->nullable();
            $table->json('data_sesudah')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('implementasi_logs');
        Schema::dropIfExists('implementasi_checklists');
        Schema::dropIfExists('implementasi_koperasi');
    }
};
