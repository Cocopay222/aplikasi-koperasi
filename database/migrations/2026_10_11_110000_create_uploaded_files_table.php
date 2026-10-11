<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('uploaded_files')) {
            Schema::create('uploaded_files', function (Blueprint $table) {
                $table->id();
                $table->string('disk')->default('public');
                $table->string('path')->index();
                $table->string('original_name')->nullable();
                $table->string('mime_type')->nullable();
                $table->bigInteger('size')->default(0);
                $table->longText('content'); // base64 encoded binary
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('uploaded_files');
    }
};
