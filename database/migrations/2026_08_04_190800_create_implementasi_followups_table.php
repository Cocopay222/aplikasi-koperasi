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
        if (!Schema::hasTable('implementasi_followups')) {
            Schema::create('implementasi_followups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('implementasi_id');
            $table->date('tanggal_followup')->nullable();
            $table->date('tanggal_followup_berikutnya')->nullable();
            $table->date('target_tanggal_tindakan')->nullable();
            $table->string('jenis_tindakan')->nullable();
            $table->string('pic_tindakan')->nullable();
            $table->string('status_tindakan')->nullable();
            $table->text('hasil_komunikasi')->nullable();
            $table->text('kendala_koperasi')->nullable();
            $table->text('komitmen_koperasi')->nullable();
            $table->text('tindakan_berikutnya')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('implementasi_id')->references('id')->on('implementasi_koperasi')->onDelete('cascade');
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('set null');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('implementasi_followups');
    }
};
