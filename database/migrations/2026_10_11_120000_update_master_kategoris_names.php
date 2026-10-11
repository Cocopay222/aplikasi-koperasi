<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('master_kategoris')) {
            return;
        }

        DB::table('master_kategoris')
            ->whereIn('nama_kategori', ['Transaksi SAKTI.Link', 'Transaksi Sakti.Link', 'Transaksi Sakti Link', 'Transaksi Super.Link'])
            ->update([
                'nama_kategori' => 'Transaksi Super Link',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('master_kategoris')) {
            return;
        }

        DB::table('master_kategoris')
            ->where('nama_kategori', 'Transaksi Super Link')
            ->update([
                'nama_kategori' => 'Transaksi SAKTI.Link',
                'updated_at' => now(),
            ]);
    }
};
