<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();

        User::firstOrCreate(
            ['email' => 'anandakarthickdon@gmail.com'],
            [
                'name' => 'Admin User',
                'email' => 'anandakarthickdon@gmail.com',
                'password' => Hash::make('password'),
                'phone' => '9003096885',
                'role_id' => $adminRole?->id,
                'is_active' => true,
            ]
        );
    }
}
