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
            ['nama_aplikasi' => 'SiCUNDO SAKTI', 'deskripsi' => 'Aplikasi SiCUNDO SAKTI', 'username' => null, 'password' => null, 'is_active' => true],
            ['nama_aplikasi' => 'SAKTI Multiusaha', 'deskripsi' => 'Aplikasi SAKTI Multiusaha', 'username' => 'user1', 'password' => 'user1', 'is_active' => true],
            ['nama_aplikasi' => 'Sakti Online', 'deskripsi' => 'Aplikasi Sakti Online', 'username' => 'user1', 'password' => 'user1', 'is_active' => true],
            ['nama_aplikasi' => 'Dashboard Sakti Online', 'deskripsi' => 'Aplikasi Dashboard Sakti Online', 'username' => 'user1', 'password' => 'user1', 'is_active' => true],
            ['nama_aplikasi' => 'LACI', 'deskripsi' => 'Aplikasi LACI', 'username' => null, 'password' => null, 'is_active' => true],
            ['nama_aplikasi' => 'Transaksi SAKTI.Link', 'deskripsi' => 'Aplikasi Transaksi SAKTI.Link', 'username' => null, 'password' => null, 'is_active' => true],
            ['nama_aplikasi' => 'SAKTI.Link', 'deskripsi' => 'Aplikasi SAKTI.Link', 'username' => null, 'password' => null, 'is_active' => true],
            ['nama_aplikasi' => 'SAKTI Retail', 'deskripsi' => 'Aplikasi SAKTI Retail', 'username' => null, 'password' => null, 'is_active' => true],
            ['nama_aplikasi' => 'SiCUNDO KU', 'deskripsi' => 'Aplikasi SiCUNDO KU', 'username' => null, 'password' => null, 'is_active' => true],
            ['nama_aplikasi' => 'Sakti Mobile', 'deskripsi' => 'Aplikasi Sakti Mobile', 'username' => null, 'password' => null, 'is_active' => true],
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
            ['nama_kategori' => 'Transaksi SAKTI.Link'],
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
