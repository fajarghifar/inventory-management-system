<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = collect([
            [
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('password')
            ],
            [
                'name' => 'quest',
                'email' => 'quest@quest.com',
                'password' => bcrypt('password')
            ],
            [
                'name' => 'user',
                'email' => 'user@user.com',
                'password' => bcrypt('password')
            ]
        ]);

        $users->each(function ($user){
            User::insert($user);
        });
    }
}
