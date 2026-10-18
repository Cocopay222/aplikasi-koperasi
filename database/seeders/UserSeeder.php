<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@superdesk.com'],
            [
                'nama' => 'Super Admin Radith',
                'password' => Hash::make('password123'),
                'role' => UserRole::SUPERADMIN->value,
                'instansi_id' => null,
                'is_verified' => true,
            ]
        );

        // Support User (Admin)
        User::updateOrCreate(
            ['email' => 'support@skk.co.id'],
            [
                'nama' => 'Admin Support',
                'password' => Hash::make('password123'),
                'role' => UserRole::SUPPORT->value,
                'instansi_id' => null,
                'is_verified' => true,
            ]
        );

        // Pelapor User (Mitra)
        User::updateOrCreate(
            ['email' => 'pic@koperasi.com'],
            [
                'nama' => 'PIC Koperasi Sejahtera',
                'password' => Hash::make('password123'),
                'role' => UserRole::PELAPOR->value,
                'instansi_id' => 1, // Asumsi ID 1 adalah Koperasi Kredit Sejahtera dari MasterDataSeeder
                'is_verified' => true,
            ]
        );
        // Admin Koperasi Baru
        User::updateOrCreate(
            ['email' => 'admin@koperasi.com'],
            [
                'nama' => 'Admin Koperasi',
                'password' => Hash::make('admin1234'),
                'role' => UserRole::SUPERADMIN->value,
                'instansi_id' => null,
                'is_verified' => true,
            ]
        );
    }
}
