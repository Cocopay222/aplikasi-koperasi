<?php

namespace Database\Seeders;

use App\Models\Instansi;
use App\Models\MasterAplikasi;
use App\Models\MasterKategori;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $instansi = Instansi::create([
            'nama_instansi' => 'Koperasi Kredit Sejahtera',
            'alamat' => 'Jl. Merdeka No. 1, Jakarta',
            'no_telp' => '021-12345678',
        ]);

        $aplikasis = [
            ['nama_aplikasi' => 'Super Multiusaha', 'deskripsi' => 'Aplikasi Super Multiusaha', 'username' => 'user1', 'password' => 'user1', 'is_active' => true],
            ['nama_aplikasi' => 'Super Online', 'deskripsi' => 'Aplikasi Super Online', 'username' => 'user1', 'password' => 'user1', 'is_active' => true],
            ['nama_aplikasi' => 'Dashboard Super Online', 'deskripsi' => 'Aplikasi Dashboard Super Online', 'username' => 'user1', 'password' => 'user1', 'is_active' => true],
            ['nama_aplikasi' => 'Super LACI', 'deskripsi' => 'Aplikasi Super LACI', 'username' => null, 'password' => null, 'is_active' => true],
            ['nama_aplikasi' => 'Transaksi Super.Link', 'deskripsi' => 'Aplikasi Transaksi Super.Link', 'username' => null, 'password' => null, 'is_active' => true],
            ['nama_aplikasi' => 'Super.Link', 'deskripsi' => 'Aplikasi Super.Link', 'username' => null, 'password' => null, 'is_active' => true],
            ['nama_aplikasi' => 'Super Retail', 'deskripsi' => 'Aplikasi Super Retail', 'username' => null, 'password' => null, 'is_active' => true],
            ['nama_aplikasi' => 'SICUNDO SUPER', 'deskripsi' => 'Aplikasi SICUNDO SUPER', 'username' => null, 'password' => null, 'is_active' => true],
            ['nama_aplikasi' => 'Super Mobile', 'deskripsi' => 'Aplikasi Super Mobile', 'username' => null, 'password' => null, 'is_active' => true],
        ];

        foreach ($aplikasis as $app) {
            MasterAplikasi::updateOrCreate(
                ['nama_aplikasi' => $app['nama_aplikasi']],
                $app
            );
        }

        $kategoris = [
            ['nama_kategori' => 'Migrasi Data'],
            ['nama_kategori' => 'Support SOP'],
            ['nama_kategori' => 'Support Data'],
            ['nama_kategori' => 'Support Teknis Bug/Optimise'],
            ['nama_kategori' => 'Transaksi Super Link'],
            ['nama_kategori' => 'Support Pra'],
            ['nama_kategori' => 'Setup Datalama'],
            ['nama_kategori' => 'Fraud'],
        ];

        foreach ($kategoris as $kat) {
            MasterKategori::updateOrCreate(
                ['nama_kategori' => $kat['nama_kategori']],
                $kat
            );
        }
    }
}
