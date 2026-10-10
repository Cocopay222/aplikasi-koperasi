<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_kategoris', function (Blueprint $table) {
            $table->id('kategori_id');
            $table->string('nama_kategori');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_kategoris');
    }
};
