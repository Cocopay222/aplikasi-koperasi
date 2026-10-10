<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('implementasi_koperasi', function (Blueprint $table) {
            $table->string('anggota_hadir')->nullable()->after('nama_trainer');
        });
    }

    public function down(): void
    {
        Schema::table('implementasi_koperasi', function (Blueprint $table) {
            $table->dropColumn('anggota_hadir');
        });
    }
};
