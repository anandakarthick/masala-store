<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'Full system access',
            ],
            [
                'name' => 'Staff',
                'slug' => 'staff',
                'description' => 'Order processing and inventory management',
            ],
            [
                'name' => 'Customer',
                'slug' => 'customer',
                'description' => 'Regular customer account',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
