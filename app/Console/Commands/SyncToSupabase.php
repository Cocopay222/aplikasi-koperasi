<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class SyncToSupabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:sync-to-supabase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate schema and copy all data from local MariaDB to Supabase Postgres';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Memulai Sinkronisasi Database dari Lokal (MariaDB) ke Supabase (PostgreSQL)');

        // Setup koneksi source (Lokal MariaDB) secara dinamis
        Config::set('database.connections.source_db', [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'koperasi_db',
            'username' => 'root',
            'password' => 'MariaDB', // Sesuai password .env lokal Anda sebelumnya
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
        ]);

        $sourceConnection = 'source_db';
        $targetConnection = 'pgsql'; // Supabase (karena DB_CONNECTION di .env = pgsql)

        // Test koneksi
        try {
            DB::connection($sourceConnection)->getPdo();
            $this->info('✅ Berhasil terhubung ke database Lokal MariaDB.');
        } catch (\Exception $e) {
            $this->error('❌ Gagal terhubung ke Lokal MariaDB. Pesan: ' . $e->getMessage());
            return;
        }

        try {
            DB::connection($targetConnection)->getPdo();
            $this->info('✅ Berhasil terhubung ke Supabase PostgreSQL.');
        } catch (\Exception $e) {
            $this->error('❌ Gagal terhubung ke Supabase. Pesan: ' . $e->getMessage());
            $this->error('Pastikan Anda sudah mengisi password Supabase di file .env!');
            return;
        }

        // Jalankan migrasi segar di Supabase
        $this->warn('⚙️ Membuat ulang struktur tabel di Supabase...');
        Artisan::call('migrate:fresh', [
            '--database' => $targetConnection,
            '--force' => true
        ]);
        $this->info('✅ Struktur tabel berhasil dibuat!');

        // Dapatkan semua tabel dari source
        $tables = DB::connection($sourceConnection)->select('SHOW TABLES');
        $databaseName = 'koperasi_db';
        $tableKey = 'Tables_in_' . $databaseName;

        // Matikan Foreign Key Constraint di Postgres sementara
        DB::connection($targetConnection)->statement('SET session_replication_role = replica;');

        foreach ($tables as $tableObj) {
            $tableName = (array) $tableObj;
            $tableName = array_values($tableName)[0];

            if ($tableName === 'migrations') {
                continue; // Jangan copy tabel migrasi
            }

            $this->info("🔄 Menyalin data tabel: {$tableName}...");

            $rows = DB::connection($sourceConnection)->table($tableName)->get();
            $count = $rows->count();

            if ($count === 0) {
                $this->line("   Tabel kosong, dilewati.");
                continue;
            }

            // Convert baris ke bentuk array
            $insertData = [];
            foreach ($rows as $row) {
                $insertData[] = (array) $row;
            }

            // Insert secara massal (chunk untuk menghindari limit memory)
            $chunks = array_chunk($insertData, 500);
            foreach ($chunks as $chunk) {
                DB::connection($targetConnection)->table($tableName)->insert($chunk);
            }

            $this->info("   ✅ {$count} baris berhasil disalin.");

            // Reset Sequence ID di PostgreSQL agar auto-increment sinkron
            try {
                $hasId = DB::connection($targetConnection)->getSchemaBuilder()->hasColumn($tableName, 'id');
                if ($hasId) {
                    $seqQuery = "SELECT setval(pg_get_serial_sequence('{$tableName}', 'id'), coalesce(max(id), 1), max(id) IS NOT null) FROM {$tableName};";
                    DB::connection($targetConnection)->statement($seqQuery);
                }
            } catch (\Exception $e) {
                // Abaikan jika tidak ada sequence
            }
        }

        // Hidupkan kembali Foreign Key Constraint
        DB::connection($targetConnection)->statement('SET session_replication_role = DEFAULT;');

        $this->info('🎉 Selesai! Seluruh data dan struktur tabel berhasil bermigrasi ke Supabase!');
    }
}
