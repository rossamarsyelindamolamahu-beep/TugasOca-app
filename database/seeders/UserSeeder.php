<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the default admin and pimpinan accounts.
     *
     * Menggunakan updateOrCreate agar idempotent: aman dijalankan berulang
     * dan selalu memastikan password tersimpan sebagai hash Laravel.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'Admin',
            ]
        );

        User::updateOrCreate(
            ['username' => 'pimpinan'],
            [
                'name' => 'Kepala Puskesmas',
                'password' => Hash::make('pimpinan123'),
                'role' => 'Pimpinan',
            ]
        );
    }
}
