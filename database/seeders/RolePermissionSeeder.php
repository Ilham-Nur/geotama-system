<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'dashboard.view',

            'employees.view',
            'employees.create',
            'employees.edit',
            'employees.delete',

            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'roles.permission.manage',

            'permohonan.view',
            'permohonan.create',
            'permohonan.edit',
            'permohonan.delete',
            'permohonan.preview',
            'permohonan.download',
            'permohonan.export_pdf',

            'proyek.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $staff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $magang = Role::firstOrCreate(['name' => 'magang', 'guard_name' => 'web']);

        $superAdmin->syncPermissions(Permission::all());

        $admin->syncPermissions([
            'dashboard.view',
            'employees.view',
            'employees.create',
            'employees.edit',
            'users.view',
            'users.create',
            'users.edit',
            'roles.view',
            'permohonan.view',
        ]);

        $staff->syncPermissions([
            'dashboard.view',
            'employees.view',
        ]);

        $magang->syncPermissions([
            'dashboard.view',
        ]);
    }
}
