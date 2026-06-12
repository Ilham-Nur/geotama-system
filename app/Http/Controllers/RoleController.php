<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissionGroups = $this->permissionGroups();

        return view('roles.create', compact('permissionGroups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil dibuat.');
    }

    public function edit(Role $role)
    {
        $permissionGroups = $this->permissionGroups();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact('role', 'permissionGroups', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role->update([
            'name' => $validated['name'],
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil diupdate.');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, ['super-admin', 'superadmin'], true)) {
            return redirect()
                ->route('roles.index')
                ->with('error', 'Role super-admin tidak boleh dihapus.');
        }

        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil dihapus.');
    }

    private function permissionGroups(): Collection
    {
        $moduleLabels = [
            'dashboard' => 'Dashboard',
            'employees' => 'Karyawan',
            'users' => 'Pengguna',
            'roles' => 'Role Management',
            'pak' => 'PAK',
            'permohonan' => 'Permohonan',
            'proyek' => 'Proyek',
            'invoice' => 'Invoice',
            'pembayaran' => 'Pembayaran',
            'quotation' => 'Quotation',
            'surat_tugas' => 'Surat Tugas',
            'assets' => 'Aset',
        ];

        return Permission::orderBy('name')
            ->get()
            ->groupBy(fn (Permission $permission) => Str::before($permission->name, '.'))
            ->map(fn (Collection $permissions, string $module) => [
                'label' => $moduleLabels[$module] ?? Str::headline($module),
                'permissions' => $permissions->map(fn (Permission $permission) => [
                    'name' => $permission->name,
                    'label' => $this->permissionActionLabel(Str::after($permission->name, '.')),
                ]),
            ])
            ->sortBy(fn (array $group, string $module) => $module === 'dashboard' ? '0' : '1'.$group['label']);
    }

    private function permissionActionLabel(string $action): string
    {
        return [
            'view' => 'Lihat',
            'show' => 'Lihat Detail',
            'create' => 'Tambah',
            'store' => 'Simpan',
            'edit' => 'Edit',
            'delete' => 'Hapus',
            'download' => 'Download',
            'preview' => 'Preview',
            'export_pdf' => 'Export PDF',
            'upload_signed' => 'Upload Dokumen Ditandatangani',
            'jadikan_project' => 'Jadikan Proyek',
            'convert' => 'Konversi',
            'permission.manage' => 'Kelola Permission',
            'cv.generate' => 'Generate CV',
        ][$action] ?? Str::headline(str_replace('.', ' ', $action));
    }
}
