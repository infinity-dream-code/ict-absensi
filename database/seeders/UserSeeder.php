<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // Create admin user
        User::create([
            'nik' => 'ADMIN001',
            'name' => 'Administrator',
            'email' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Create test users
        $users = [
            [
                'nik' => 'EMP001',
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ],
            [
                'nik' => 'EMP002',
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ],
            [
                'nik' => 'EMP003',
                'name' => 'Bob Johnson',
                'email' => 'bob.johnson@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ],
            [
                'nik' => 'EMP004',
                'name' => 'Alice Williams',
                'email' => 'alice.williams@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ],
            [
                'nik' => 'EMP005',
                'name' => 'Charlie Brown',
                'email' => 'charlie.brown@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        $this->command->info('Admin and test users created successfully!');
        $this->command->info('Admin Login:');
        $this->command->info('Username: admin, Password: admin123');
        $this->command->info('Employee Login:');
        $this->command->info('NIK: EMP001, Password: password123');
        $this->command->info('NIK: EMP002, Password: password123');
        $this->command->info('NIK: EMP003, Password: password123');
        $this->command->info('NIK: EMP004, Password: password123');
        $this->command->info('NIK: EMP005, Password: password123');
    }
}
