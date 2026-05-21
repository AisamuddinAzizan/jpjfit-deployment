<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'view dashboard',
            'manage users',
            'manage participants',
            'manage sessions',
            'manage health-records',
            'manage fitness-results',
            'manage certificates',
            'view reports',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $adminRole = Role::findOrCreate('admin', 'web');
        $jpjOfficerRole = Role::findOrCreate('jpj_officer', 'web');
        $healthOfficerRole = Role::findOrCreate('health_officer', 'web');

        $adminRole->syncPermissions($permissions);
        $jpjOfficerRole->syncPermissions([
            'view dashboard',
            'manage participants',
            'manage sessions',
            'manage fitness-results',
            'manage certificates',
            'view reports',
        ]);
        $healthOfficerRole->syncPermissions([
            'view dashboard',
            'manage health-records',
            'manage certificates',
            'view reports',
        ]);

        $admin = User::updateOrCreate(
            ['email' => 'admin@jpjfit.gov.my'],
            [
                'name' => 'System Admin',
                'phone' => '0120000001',
                'department' => 'JPJ HQ',
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('Password@123'),
            ],
        );
        $admin->syncRoles([$adminRole]);

        $jpjOfficer = User::updateOrCreate(
            ['email' => 'officer@jpjfit.gov.my'],
            [
                'name' => 'JPJ Officer',
                'phone' => '0120000002',
                'department' => 'JPJ Operations',
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('Password@123'),
            ],
        );
        $jpjOfficer->syncRoles([$jpjOfficerRole]);

        $healthOfficer = User::updateOrCreate(
            ['email' => 'health@jpjfit.gov.my'],
            [
                'name' => 'Health Officer KKM',
                'phone' => '0120000003',
                'department' => 'KKM',
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('Password@123'),
            ],
        );
        $healthOfficer->syncRoles([$healthOfficerRole]);
    }
}
