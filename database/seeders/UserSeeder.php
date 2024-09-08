<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // Password should be hashed
            'role' => 'Admin',
        ]);

        // Create Librarian user
        User::create([
            'name' => 'Librarian User',
            'email' => 'librarian@example.com',
            'password' => Hash::make('password'),
            'role' => 'Librarian',
        ]);

        // Create Member user
        User::create([
            'name' => 'Member User',
            'email' => 'member@example.com',
            'password' => Hash::make('password'),
            'role' => 'Member',
        ]);
    }
}
