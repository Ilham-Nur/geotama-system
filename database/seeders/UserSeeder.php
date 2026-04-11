<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // pastikan role sudah ada
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // user super admin
        $superAdmin = User::firstOrCreate([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@gmail.com',
            'password' => 'password123', // auto hash (karena cast)
        ]);

        $superAdmin->syncRoles($superAdminRole);

        // user admin
        $admin = User::firstOrCreate([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => 'password123',
        ]);

        $admin->syncRoles($adminRole);
    }
}