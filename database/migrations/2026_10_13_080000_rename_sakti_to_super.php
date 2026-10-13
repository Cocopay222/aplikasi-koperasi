<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Pasangan teks checklist lama => baru.
     */
    private array $checklistMap = [
        'Sakti sudah digunakan sebagai aplikasi operasional utama' => 'Super sudah digunakan sebagai aplikasi operasional utama',
        'Operator aktif menggunakan Sakti' => 'Operator aktif menggunakan Super',
        'Laporan Sakti menjadi acuan operasional' => 'Laporan Super menjadi acuan operasional',
        'Operator sudah memahami proses operasional Sakti' => 'Operator sudah memahami proses operasional Super',
    ];

    /**
     * Ganti teks "Sakti" menjadi "Super" pada data lama.
     */
    public function up(): void
    {
        foreach ($this->checklistMap as $old => $new) {
            DB::table('implementasi_checklists')->where('nama_item', $old)->update(['nama_item' => $new]);
        }

        DB::table('implementasi_koperasi')
            ->where('nomor_implementasi', 'like', 'IMP/SAKTI/%')
            ->update(['nomor_implementasi' => DB::raw("REPLACE(nomor_implementasi, 'IMP/SAKTI/', 'IMP/SUPER/')")]);
    }

    /**
     * Kembalikan teks ke "Sakti".
     */
    public function down(): void
    {
        foreach ($this->checklistMap as $old => $new) {
            DB::table('implementasi_checklists')->where('nama_item', $new)->update(['nama_item' => $old]);
        }

        DB::table('implementasi_koperasi')
            ->where('nomor_implementasi', 'like', 'IMP/SUPER/%')
            ->update(['nomor_implementasi' => DB::raw("REPLACE(nomor_implementasi, 'IMP/SUPER/', 'IMP/SAKTI/')")]);
    }
};
