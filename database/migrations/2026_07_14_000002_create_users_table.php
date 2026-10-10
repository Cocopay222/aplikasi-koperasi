<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\UserRole;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('password'); // password_hash is represented by 'password' in laravel normally
            $table->string('role')->default(UserRole::PELAPOR->value); // Pelapor, Support
            $table->unsignedBigInteger('instansi_id')->nullable();
            $table->timestamps();

            $table->foreign('instansi_id')->references('instansi_id')->on('instansis')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
