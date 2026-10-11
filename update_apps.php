<?php
use App\Models\MasterAplikasi;

// Disable all current apps
MasterAplikasi::where('is_active', true)->update(['is_active' => false]);

$newApps = [
    ['nama_aplikasi' => 'Super Multiusaha', 'deskripsi' => 'Aplikasi Super Multiusaha', 'is_active' => true],
    ['nama_aplikasi' => 'Super Online', 'deskripsi' => 'Aplikasi Super Online', 'is_active' => true],
    ['nama_aplikasi' => 'Dashboard Super Online', 'deskripsi' => 'Aplikasi Dashboard Super Online', 'is_active' => true],
    ['nama_aplikasi' => 'Super LACI', 'deskripsi' => 'Aplikasi Super LACI', 'is_active' => true],
    ['nama_aplikasi' => 'Transaksi Super.Link', 'deskripsi' => 'Aplikasi Transaksi Super.Link', 'is_active' => true],
    ['nama_aplikasi' => 'Super.Link', 'deskripsi' => 'Aplikasi Super.Link', 'is_active' => true],
    ['nama_aplikasi' => 'Super Retail', 'deskripsi' => 'Aplikasi Super Retail', 'is_active' => true],
    ['nama_aplikasi' => 'SICUNDO SUPER', 'deskripsi' => 'Aplikasi SICUNDO SUPER', 'is_active' => true],
    ['nama_aplikasi' => 'Super Mobile', 'deskripsi' => 'Aplikasi Super Mobile', 'is_active' => true],
];

foreach ($newApps as $app) {
    MasterAplikasi::updateOrCreate(
        ['nama_aplikasi' => $app['nama_aplikasi']],
        $app
    );
}

echo "Done\n";
