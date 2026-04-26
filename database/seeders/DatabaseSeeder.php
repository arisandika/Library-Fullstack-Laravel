<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'ari@admin.com',
            'password' => Hash::make('password'),
        ]);

        Member::create([
            'member_number' => 'MBR-001',
            'name' => 'Budi Member',
            'email' => 'member@example.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'address' => 'Jl. Kebenaran No. 1, Jakarta',
            'status' => 'active',
        ]);
    }
}
