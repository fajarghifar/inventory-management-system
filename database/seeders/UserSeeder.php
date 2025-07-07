<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'mdrakibulhaider.int@gmail.com'],
            [
                'name' => 'Admin',
                'username' => 'admin', // ✅ Added this line
                'password' => Hash::make('123456'),
            ]
        );
    }
}
