<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($user = 1; $user <= 5; $user++) {
            User::firstOrCreate(
                ['email' => 'user_' . $user . '@whitehelemt.com'],
                ['name' => 'user ' . $user, 'password' => Hash::make('user_' . $user . '@whi$#mt')]
            );
        }
    }
}
