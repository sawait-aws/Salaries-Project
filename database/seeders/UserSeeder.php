<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Seed a manager
        DB::table('users')->insert([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'user_id' => 69,
            'password' => Hash::make('password123'), // Securely hash the password
            'role' => 'manager',
            'position' => 'Team Lead',
            'joining_date' => now()->subYears(2)->toDateString(),
            'email_address' => 'manager@example.com',
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Seed employees
        $employees = [
            [
                'first_name' => 'Alice',
                'last_name' => 'Smith',
                'user_id' => 2,
                'password' => Hash::make('password123'),
                'role' => 'employee',
                'position' => 'Developer',
                'joining_date' => now()->subMonths(18)->toDateString(),
                'email_address' => 'alice@example.com',
            ],
            [
                'first_name' => 'Bob',
                'last_name' => 'Brown',
                'user_id' => 3,
                'password' => Hash::make('password123'),
                'role' => 'employee',
                'position' => 'Developer',
                'joining_date' => now()->subMonths(12)->toDateString(),
                'email_address' => 'bob@example.com',
            ],
            [
                'first_name' => 'Charlie',
                'last_name' => 'Davis',
                'user_id' => 4,
                'password' => Hash::make('password123'),
                'role' => 'employee',
                'position' => 'Designer',
                'joining_date' => now()->subMonths(6)->toDateString(),
                'email_address' => 'charlie@example.com',
            ],
            [
                'first_name' => 'Diana',
                'last_name' => 'Taylor',
                'user_id' => 5,
                'password' => Hash::make('password123'),
                'role' => 'employee',
                'position' => 'Tester',
                'joining_date' => now()->subMonths(4)->toDateString(),
                'email_address' => 'diana@example.com',
            ],
        ];

        foreach ($employees as $employee) {
            DB::table('users')->insert(array_merge($employee, [
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
