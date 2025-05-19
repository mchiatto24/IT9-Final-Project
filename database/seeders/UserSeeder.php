<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Huey Hibaler',
            'email' => 'hibaler@admin.com',
            'password' => Hash::make('Admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Evander Amorcillo',
            'email' => 'amorcillo@staff.com',
            'password' => Hash::make('Staff123'),
            'role' => 'staff',
        ]);
    }
}