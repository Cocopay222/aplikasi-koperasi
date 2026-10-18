<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ganti nama kolom pic_sakti_id menjadi pic_super_id (beserta nama foreign key-nya).
     */
    public function up(): void
    {
        $this->renameColumn('pic_sakti_id', 'pic_super_id');
    }

    /**
     * Kembalikan nama kolom ke pic_sakti_id.
     */
    public function down(): void
    {
        $this->renameColumn('pic_super_id', 'pic_sakti_id');
    }

    private function renameColumn(string $from, string $to): void
    {
        $table = 'implementasi_koperasi';

        if (!Schema::hasColumn($table, $from) || Schema::hasColumn($table, $to)) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE {$table} RENAME COLUMN {$from} TO {$to}");

            $oldFk = "{$table}_{$from}_foreign";
            $newFk = "{$table}_{$to}_foreign";
            $exists = DB::selectOne(
                'SELECT 1 FROM pg_constraint WHERE conname = ?',
                [$oldFk]
            );
            if ($exists) {
                DB::statement("ALTER TABLE {$table} RENAME CONSTRAINT {$oldFk} TO {$newFk}");
            }
            return;
        }

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE `{$table}` RENAME COLUMN `{$from}` TO `{$to}`");
            return;
        }

        // sqlite & lainnya
        DB::statement("ALTER TABLE {$table} RENAME COLUMN {$from} TO {$to}");
    }
};
