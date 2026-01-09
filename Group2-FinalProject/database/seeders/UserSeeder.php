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
        // 1. ADMIN ACCOUNT (Role: admin) - Full permissions
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'IT Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // FACULTY ACCOUNT (Role: faculty) - Mid-level resolver
        User::firstOrCreate(
            ['email' => 'faculty@gmail.com'],
            [
                'name' => 'Faculty Account',
                'password' => Hash::make('password'),
                'role' => 'faculty',
            ]
        );

        // 3. STUDENT ACCOUNT (Role: student) - Creator only
        User::firstOrCreate(
            ['email' => 'student@gmail.com'],
            [
                'name' => 'Student2 Account',
                'password' => Hash::make('password'),
                'role' => 'student',
            ]
        );

        // 4. Second Student for testing unauthorized access
        User::firstOrCreate(
            ['email' => 'student@gmail.com'],
            [
                'name' => 'Student Account',
                'password' => Hash::make('password'),
                'role' => 'student',
            ]
        );
    }
}
