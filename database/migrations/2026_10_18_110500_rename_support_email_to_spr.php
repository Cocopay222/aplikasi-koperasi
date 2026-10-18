<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private string $oldEmail = 'support@skk.co.id';
    private string $newEmail = 'support@spr.co.id';

    /**
     * Ganti email support admin lama (skk.co.id) ke domain baru (spr.co.id) pada data pengguna yang sudah ada.
     */
    public function up(): void
    {
        $this->swap($this->oldEmail, $this->newEmail);
    }

    public function down(): void
    {
        $this->swap($this->newEmail, $this->oldEmail);
    }

    private function swap(string $from, string $to): void
    {
        // Lewati jika email tujuan sudah dipakai, agar tidak bentrok unique constraint
        if (DB::table('users')->where('email', $to)->exists()) {
            return;
        }

        DB::table('users')->where('email', $from)->update(['email' => $to]);
    }
};
