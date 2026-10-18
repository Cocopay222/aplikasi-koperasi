<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private string $oldEmail = 'superadmin@saktidesk.com';
    private string $newEmail = 'superadmin@superdesk.com';

    /**
     * Ganti email superadmin lama (saktidesk) ke domain baru (superdesk) pada data yang sudah ada.
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
