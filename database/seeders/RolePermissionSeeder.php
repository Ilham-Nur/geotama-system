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
            // Dashboard
            'dashboard.view',

            // Employee management
            'employees.view',
            'employees.create',
            'employees.edit',
            'employees.delete',

            // User management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Role management
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'roles.permission.manage',

            // PAK
            'pak.view',
            'pak.create',
            'pak.edit',
            'pak.convert',

            // Permohonan
            'permohonan.view',
            'permohonan.create',
            'permohonan.edit',
            'permohonan.delete',
            'permohonan.preview',
            'permohonan.download',
            'permohonan.export_pdf',
            'permohonan.jadikan_project',

            // Proyek
            'proyek.view',
            'proyek.show',

            // Invoice
            'invoice.view',
            'invoice.create',
            'invoice.store',
            'invoice.export_pdf',
            'invoice.upload_signed',

            // Pembayaran
            'pembayaran.view',
            'pembayaran.create',
            'pembayaran.store',

            // Quotation
            'quotation.view',
            'quotation.create',
            'quotation.edit',

            // Assets
            'assets.view',
            'assets.create',
            'assets.edit',
            'assets.delete',
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

            'pak.view',
            'pak.create',
            'pak.edit',
            'pak.convert',

            'permohonan.view',
            'permohonan.create',
            'permohonan.edit',
            'permohonan.preview',
            'permohonan.download',
            'permohonan.export_pdf',
            'permohonan.jadikan_project',

            'proyek.view',
            'proyek.show',

            'invoice.view',
            'invoice.create',
            'invoice.store',
            'invoice.export_pdf',
            'invoice.upload_signed',

            'pembayaran.view',
            'pembayaran.create',
            'pembayaran.store',

            'quotation.view',
            'quotation.create',
            'quotation.edit',

            'assets.view',
            'assets.create',
            'assets.edit',
            'assets.delete',
        ]);

        $staff->syncPermissions([
            'dashboard.view',
            'employees.view',
            'pak.view',
            'permohonan.view',
            'proyek.view',
            'invoice.view',
            'pembayaran.view',
        ]);

        $magang->syncPermissions([
            'dashboard.view',
            'pak.view',
        ]);
    }
}
