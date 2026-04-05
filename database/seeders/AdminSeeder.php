<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@rsud-baubau.go.id'],
            [
                'name' => 'Administrator RSUD',
                'nip' => '000000000001',
                'jabatan' => 'Administrator',
                'unit' => 'IT',
                'role' => 'admin',
                'password' => Hash::make('admin123'),
            ]
        );
    }
}
