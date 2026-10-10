<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id('faq_id');
            $table->unsignedBigInteger('kategori_id');
            $table->string('pertanyaan');
            $table->text('jawaban');
            $table->enum('visibility', ['public', 'internal'])->default('public');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('kategori_id')
                  ->references('kategori_id')
                  ->on('master_kategoris')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
