<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('implementasi_koperasi', function (Blueprint $table) {
            if (!Schema::hasColumn('implementasi_koperasi', 'tanggal_mulai_support')) {
                $table->date('tanggal_mulai_support')->nullable()->after('tanggal_selesai');
            }
            if (!Schema::hasColumn('implementasi_koperasi', 'tanggal_selesai_support')) {
                $table->date('tanggal_selesai_support')->nullable()->after('tanggal_mulai_support');
            }
            if (!Schema::hasColumn('implementasi_koperasi', 'durasi_support')) {
                $table->string('durasi_support')->nullable()->after('tanggal_selesai_support');
            }
        });
    }

    public function down(): void
    {
        Schema::table('implementasi_koperasi', function (Blueprint $table) {
            $table->dropColumn(['tanggal_mulai_support', 'tanggal_selesai_support', 'durasi_support']);
        });
    }
};
