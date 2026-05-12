<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'テスト太郎',
            'email' => 'user@example.com',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        User::factory(4)->create();
    }
}