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

        User::create([
            'nik' => 'ADMIN001',
            'name' => 'Administrator',
            'email' => 'admin',
            'password' => Hash::make('Smartpay1ct'),
            'role' => 'admin',
        ]);
    }
}
