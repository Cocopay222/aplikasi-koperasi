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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik')->nullable()->after('avatar');
            $table->string('whatsapp')->nullable()->after('nik');
            $table->string('spesialisasi')->nullable()->after('whatsapp');
            $table->boolean('two_factor')->default(false)->after('spesialisasi');
            $table->string('otp_method')->default('WhatsApp')->after('two_factor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nik', 'whatsapp', 'spesialisasi', 'two_factor', 'otp_method']);
        });
    }
};
