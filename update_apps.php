<?php
use App\Models\MasterAplikasi;

// Disable all current apps
MasterAplikasi::where('is_active', true)->update(['is_active' => false]);

$newApps = [
    ['nama_aplikasi' => 'SiCUNDO SAKTI', 'deskripsi' => 'Aplikasi SiCUNDO SAKTI', 'is_active' => true],
    ['nama_aplikasi' => 'SAKTI Multiusaha', 'deskripsi' => 'Aplikasi SAKTI Multiusaha', 'is_active' => true],
    ['nama_aplikasi' => 'LACI', 'deskripsi' => 'Aplikasi LACI', 'is_active' => true],
    ['nama_aplikasi' => 'Transaksi SAKTI.Link', 'deskripsi' => 'Aplikasi Transaksi SAKTI.Link', 'is_active' => true],
    ['nama_aplikasi' => 'SAKTI.Link', 'deskripsi' => 'Aplikasi SAKTI.Link', 'is_active' => true],
    ['nama_aplikasi' => 'SAKTI Retail', 'deskripsi' => 'Aplikasi SAKTI Retail', 'is_active' => true],
    ['nama_aplikasi' => 'SiCUNDO KU', 'deskripsi' => 'Aplikasi SiCUNDO KU', 'is_active' => true],
    ['nama_aplikasi' => 'Sakti Mobile', 'deskripsi' => 'Aplikasi Sakti Mobile', 'is_active' => true],
];

foreach ($newApps as $app) {
    MasterAplikasi::updateOrCreate(
        ['nama_aplikasi' => $app['nama_aplikasi']],
        $app
    );
}

echo "Done\n";
