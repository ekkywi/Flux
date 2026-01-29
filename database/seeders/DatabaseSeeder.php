<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::create([
            'id' => (string) Str::uuid(),
            'first_name' => 'System',
            'last_name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@flux.com',
            'password' => Hash::make('password'),
            'department' => 'IT Infrastructure',
            'role' => 'System Administrator',
            'is_active' => true,
        ]);
    }
}
