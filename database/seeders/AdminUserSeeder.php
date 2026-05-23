<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate([
            'name' => 'admin'
        ]);

        $admin = User::updateOrCreate(
            [
                'email' => 'admin@jpjfit.gov.my'
            ],
            [
                'name' => 'System Admin',
                'phone' => '0120000001',
                'department' => 'JPJ HQ',
                'is_active' => true,
                'password' => bcrypt('password'),
            ]
        );

        $admin->assignRole('admin');
    }
}