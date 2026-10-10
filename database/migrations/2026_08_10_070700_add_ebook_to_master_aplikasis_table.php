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
        if (!Schema::hasColumn('master_aplikasis', 'ebook')) {
            Schema::table('master_aplikasis', function (Blueprint $table) {
                $table->string('ebook')->nullable()->after('deskripsi');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_aplikasis', function (Blueprint $table) {
            $table->dropColumn('ebook');
        });
    }
};
