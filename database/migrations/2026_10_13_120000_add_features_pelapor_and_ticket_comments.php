<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom urgensi, rating, rating_ulasan pada tabel tickets
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'urgensi')) {
                $table->string('urgensi', 50)->default('Sedang')->after('status');
            }
            if (!Schema::hasColumn('tickets', 'rating')) {
                $table->unsignedTinyInteger('rating')->nullable()->after('urgensi');
            }
            if (!Schema::hasColumn('tickets', 'rating_ulasan')) {
                $table->text('rating_ulasan')->nullable()->after('rating');
            }
        });

        // 2. Buat tabel ticket_comments untuk diskusi/komentar antar pelapor dan support
        if (!Schema::hasTable('ticket_comments')) {
            Schema::create('ticket_comments', function (Blueprint $table) {
                $table->id();
                $table->string('ticket_id');
                $table->unsignedBigInteger('user_id');
                $table->text('pesan');
                $table->timestamps();

                $table->foreign('ticket_id')->references('ticket_id')->on('tickets')->onDelete('cascade');
                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->index('ticket_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_comments');
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'rating_ulasan')) {
                $table->dropColumn('rating_ulasan');
            }
            if (Schema::hasColumn('tickets', 'rating')) {
                $table->dropColumn('rating');
            }
            if (Schema::hasColumn('tickets', 'urgensi')) {
                $table->dropColumn('urgensi');
            }
        });
    }
};
