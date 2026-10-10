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
        Schema::table('implementasi_koperasi', function (Blueprint $table) {
            $table->time('waktu_go_live')->nullable()->after('target_go_live');
            $table->string('tempat_go_live')->nullable()->after('waktu_go_live');
            $table->string('status_go_live')->default('Belum Done')->after('tempat_go_live');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('implementasi_koperasi', function (Blueprint $table) {
            $table->dropColumn(['waktu_go_live', 'tempat_go_live', 'status_go_live']);
        });
    }
};
