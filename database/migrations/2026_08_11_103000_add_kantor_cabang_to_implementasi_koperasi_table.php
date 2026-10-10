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
        if (!Schema::hasColumn('implementasi_koperasi', 'kantor_cabang')) {
            Schema::table('implementasi_koperasi', function (Blueprint $table) {
                $table->string('kantor_cabang')->nullable()->after('instansi_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('implementasi_koperasi', function (Blueprint $table) {
            $table->dropColumn('kantor_cabang');
        });
    }
};
