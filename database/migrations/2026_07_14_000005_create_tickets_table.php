<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TicketStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->string('ticket_id')->primary(); // TKT-YYMMDDXXXX
            $table->unsignedBigInteger('pelapor_id');
            $table->unsignedBigInteger('aplikasi_id');
            $table->unsignedBigInteger('kategori_id')->nullable();
            $table->unsignedBigInteger('pic_support_id')->nullable();
            
            $table->text('permasalahan');
            $table->string('lampiran')->nullable();
            $table->text('penyelesaian')->nullable();
            $table->text('pencegahan')->nullable();
            $table->string('status')->default(TicketStatus::OPEN->value);
            $table->string('link_ticket')->nullable();
            $table->boolean('is_faq')->default(false);
            
            $table->timestamp('tanggal_input')->useCurrent();
            $table->timestamp('tanggal_penyelesaian')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('pelapor_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('aplikasi_id')->references('aplikasi_id')->on('master_aplikasis')->onDelete('restrict');
            $table->foreign('kategori_id')->references('kategori_id')->on('master_kategoris')->onDelete('set null');
            $table->foreign('pic_support_id')->references('user_id')->on('users')->onDelete('set null');

            // Indexing for optimization
            $table->index('status');
            $table->index('tanggal_input');
            $table->index('tanggal_penyelesaian');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
