<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@undanganmu.com'],
            [
                'name' => 'Admin Undangan',
                'password' => Hash::make('password'),
                'phone' => '081234567890',
                'whatsapp' => '6281234567890',
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ]
        );
    }
}
