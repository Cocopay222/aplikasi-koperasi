<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ImplementasiKoperasi;

class GenerateRunningChecklists extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recap:generate-running';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Men-generate checklist Running Monitoring untuk data implementasi lama yang belum memilikinya';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pengecekan dan generate Running Monitoring checklist...');

        $implementasis = ImplementasiKoperasi::all();
        $runningItems = [
            ['kategori' => 'Running - Aplikasi', 'nama_item' => 'Aplikasi dapat diakses/login dengan normal'],
            ['kategori' => 'Running - Aplikasi', 'nama_item' => 'Aplikasi dapat digunakan tanpa error'],
            ['kategori' => 'Running - Aplikasi', 'nama_item' => 'Perpindahan/menu aplikasi berjalan normal'],
            ['kategori' => 'Running - Aplikasi', 'nama_item' => 'Proses input data berjalan normal'],
            ['kategori' => 'Running - Aplikasi', 'nama_item' => 'Proses penyimpanan transaksi berjalan normal'],
            ['kategori' => 'Running - Aplikasi', 'nama_item' => 'Transaksi berhasil diproses dan tercatat'],
            ['kategori' => 'Running - Aplikasi', 'nama_item' => 'Data/transaksi yang sudah disimpan dapat ditampilkan kembali'],
            ['kategori' => 'Running - Aplikasi', 'nama_item' => 'Laporan dapat ditampilkan dengan normal'],
            ['kategori' => 'Running - Aplikasi', 'nama_item' => 'Cetak/export laporan berjalan normal'],
            ['kategori' => 'Running - Aplikasi', 'nama_item' => 'Tidak terdapat kendala yang menghambat operasional koperasi'],
            ['kategori' => 'Running - Penggunaan Aplikasi', 'nama_item' => 'Sakti sudah digunakan sebagai aplikasi operasional utama'],
            ['kategori' => 'Running - Penggunaan Aplikasi', 'nama_item' => 'Operator aktif menggunakan Sakti'],
            ['kategori' => 'Running - Penggunaan Aplikasi', 'nama_item' => 'Proses manual/Excel untuk transaksi utama sudah ditinggalkan'],
            ['kategori' => 'Running - Transaksi', 'nama_item' => 'Transaksi anggota sudah berjalan'],
            ['kategori' => 'Running - Transaksi', 'nama_item' => 'Transaksi simpanan sudah berjalan'],
            ['kategori' => 'Running - Transaksi', 'nama_item' => 'Transaksi pinjaman sudah berjalan'],
            ['kategori' => 'Running - Transaksi', 'nama_item' => 'Transaksi angsuran sudah berjalan'],
            ['kategori' => 'Running - Transaksi', 'nama_item' => 'Transaksi kas sudah berjalan'],
            ['kategori' => 'Running - Laporan', 'nama_item' => 'Laporan sudah digunakan oleh koperasi'],
            ['kategori' => 'Running - Laporan', 'nama_item' => 'Laporan Sakti menjadi acuan operasional'],
            ['kategori' => 'Running - Kemandirian', 'nama_item' => 'Operator dapat melakukan transaksi tanpa bantuan Support'],
            ['kategori' => 'Running - Kemandirian', 'nama_item' => 'Operator dapat melakukan pengecekan/koreksi sederhana sendiri'],
            ['kategori' => 'Running - Kemandirian', 'nama_item' => 'Operator sudah memahami proses operasional Sakti']
        ];
        
        // Auto rename SAS to Sakti
        ImplementasiChecklist::where('nama_item', 'like', '%SAS%')->get()->each(function($chk) {
            $chk->update([
                'nama_item' => str_replace('SAS', 'Sakti', $chk->nama_item)
            ]);
        });

        $totalAdded = 0;
        foreach($implementasis as $impl) {
            foreach($runningItems as $item) {
                $exists = $impl->checklists()
                    ->where('kategori', $item['kategori'])
                    ->where('nama_item', $item['nama_item'])
                    ->exists();

                if (!$exists) {
                    $impl->checklists()->create([
                        'kategori' => $item['kategori'],
                        'nama_item' => $item['nama_item'],
                        'status' => 'Belum Dikirim',
                    ]);
                    $totalAdded++;
                }
            }
        }

        $this->info("Selesai! Berhasil menambahkan {$totalAdded} item checklist Running Monitoring.");
    }
}
