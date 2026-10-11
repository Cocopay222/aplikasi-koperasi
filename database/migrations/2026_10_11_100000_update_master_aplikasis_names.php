<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('master_aplikasis')) {
            return;
        }

        // Rename existing applications
        $renameMap = [
            'SAKTI Multiusaha' => 'Super Multiusaha',
            'Sakti Online' => 'Super Online',
            'Dashboard Sakti Online' => 'Dashboard Super Online',
            'LACI' => 'Super LACI',
            'Transaksi SAKTI.Link' => 'Transaksi Super.Link',
            'SAKTI.Link' => 'Super.Link',
            'SAKTI Retail' => 'Super Retail',
            'SiCUNDO SAKTI' => 'SICUNDO SUPER',
            'Sakti Mobile' => 'Super Mobile',
        ];

        foreach ($renameMap as $old => $new) {
            DB::table('master_aplikasis')
                ->where('nama_aplikasi', $old)
                ->update([
                    'nama_aplikasi' => $new,
                    'is_active' => true,
                    'updated_at' => now(),
                ]);
        }

        // Deactivate applications not in the list (e.g. SiCUNDO KU)
        DB::table('master_aplikasis')
            ->where('nama_aplikasi', 'SiCUNDO KU')
            ->update([
                'is_active' => false,
                'updated_at' => now(),
            ]);

        // Ensure all 9 requested applications exist and are active
        $apps = [
            'Super Multiusaha',
            'Super Online',
            'Dashboard Super Online',
            'Super LACI',
            'Transaksi Super.Link',
            'Super.Link',
            'Super Retail',
            'SICUNDO SUPER',
            'Super Mobile',
        ];

        foreach ($apps as $app) {
            $record = DB::table('master_aplikasis')->where('nama_aplikasi', $app)->first();
            if ($record) {
                DB::table('master_aplikasis')->where('aplikasi_id', $record->aplikasi_id)->update([
                    'is_active' => true,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('master_aplikasis')->insert([
                    'nama_aplikasi' => $app,
                    'deskripsi' => 'Aplikasi ' . $app,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        $revertMap = [
            'Super Multiusaha' => 'SAKTI Multiusaha',
            'Super Online' => 'Sakti Online',
            'Dashboard Super Online' => 'Dashboard Sakti Online',
            'Super LACI' => 'LACI',
            'Transaksi Super.Link' => 'Transaksi SAKTI.Link',
            'Super.Link' => 'SAKTI.Link',
            'Super Retail' => 'SAKTI Retail',
            'SICUNDO SUPER' => 'SiCUNDO SAKTI',
            'Super Mobile' => 'Sakti Mobile',
        ];

        foreach ($revertMap as $new => $old) {
            DB::table('master_aplikasis')
                ->where('nama_aplikasi', $new)
                ->update([
                    'nama_aplikasi' => $old,
                    'updated_at' => now(),
                ]);
        }
    }
};
