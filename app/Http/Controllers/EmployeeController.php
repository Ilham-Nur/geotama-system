<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();

        $employees = Employee::query()
            ->with('user.roles')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('full_name', 'like', "%{$search}%")
                        ->orWhere('employee_code', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('employees.index', compact('employees', 'search'));
    }

    public function show(Employee $employee)
    {
        $employee->load(['user.roles', 'documents']);

        return response()->json([
            'id' => $employee->id,
            'employee_code' => $employee->employee_code,
            'full_name' => $employee->full_name,
            'position' => $employee->position,
            'phone' => $employee->phone,
            'hire_date' => optional($employee->hire_date)->format('d M Y'),
            'employment_status' => $employee->employment_status,
            'gender' => $employee->gender,
            'birth_place' => $employee->birth_place,
            'birth_date' => optional($employee->birth_date)->format('d M Y'),
            'identity_number' => $employee->identity_number,
            'marital_status' => $employee->marital_status,
            'nationality' => $employee->nationality,
            'religion' => $employee->religion,
            'full_address' => $employee->full_address,
            'photo_url' => $employee->photo_path ? asset('storage/' . $employee->photo_path) : null,
            'documents' => $employee->documents
                ->map(fn($document) => [
                    'document_label' => $document->document_label,
                    'file_name' => $document->file_name,
                    'file_url' => asset('storage/' . $document->file_path),
                ])
                ->values()
                ->all(),
        ]);
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();

        return view('employees.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateEmployee($request);

        $user = $this->createOrUpdateUserForEmployee($request);

        Employee::create([
            'user_id' => $user?->id,
            'employee_code' => $validated['employee_code'],
            'full_name' => $validated['full_name'],
            'position' => $validated['position'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'hire_date' => $validated['hire_date'] ?? null,
            'employment_status' => $validated['employment_status'],
            'gender' => $validated['gender'] ?? null,
            'birth_place' => $validated['birth_place'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'full_address' => $validated['full_address'] ?? null,
            'identity_number' => $validated['identity_number'] ?? null,
            'marital_status' => $validated['marital_status'] ?? null,
            'nationality' => $validated['nationality'] ?? null,
            'religion' => $validated['religion'] ?? null,
        ]);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Data karyawan berhasil ditambahkan.');
    }

    public function edit(Employee $employee)
    {
        $employee->load('user.roles');
        $roles = Role::orderBy('name')->get();

        return view('employees.edit', compact('employee', 'roles'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $this->validateEmployee($request, $employee->id);

        $user = $this->createOrUpdateUserForEmployee($request, $employee);

        $employee->update([
            'user_id' => $user?->id ?? $employee->user_id,
            'employee_code' => $validated['employee_code'],
            'full_name' => $validated['full_name'],
            'position' => $validated['position'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'hire_date' => $validated['hire_date'] ?? null,
            'employment_status' => $validated['employment_status'],
            'gender' => $validated['gender'] ?? null,
            'birth_place' => $validated['birth_place'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'full_address' => $validated['full_address'] ?? null,
            'identity_number' => $validated['identity_number'] ?? null,
            'marital_status' => $validated['marital_status'] ?? null,
            'nationality' => $validated['nationality'] ?? null,
            'religion' => $validated['religion'] ?? null,
        ]);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Data karyawan berhasil diupdate.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('success', 'Data karyawan berhasil dihapus.');
    }

    private function validateEmployee(Request $request, ?int $employeeId = null): array
    {
        $createSystemAccount = $request->boolean('create_system_account');

        return $request->validate([
            'employee_code' => ['required', 'string', 'max:100', Rule::unique('employees', 'employee_code')->ignore($employeeId)],
            'full_name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'hire_date' => ['nullable', 'date'],
            'employment_status' => ['required', Rule::in(['tetap', 'kontrak', 'magang'])],
            'gender' => ['nullable', 'in:laki-laki,perempuan'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'full_address' => ['nullable', 'string'],
            'identity_number' => ['nullable', 'string', 'max:100'],
            'marital_status' => ['nullable', 'in:belum_kawin,kawin,cerai_hidup,cerai_mati'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'religion' => ['nullable', 'string', 'max:100'],

            'create_system_account' => ['nullable', 'boolean'],
            'username' => [Rule::requiredIf($createSystemAccount), 'nullable', 'string', 'max:255'],
            'email' => [Rule::requiredIf($createSystemAccount), 'nullable', 'email', 'max:255'],
            'password' => [Rule::requiredIf($createSystemAccount && !$employeeId), 'nullable', 'string', 'min:8', 'confirmed'],
            'role' => [Rule::requiredIf($createSystemAccount), 'nullable', 'exists:roles,name'],
        ]);
    }

    private function createOrUpdateUserForEmployee(Request $request, ?Employee $employee = null): ?User
    {
        if (!$request->boolean('create_system_account')) {
            return null;
        }

        $user = $employee?->user;

        $userData = [
            'name' => $request->input('full_name'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
        ];

        if ($user) {
            $request->validate([
                'username' => [Rule::unique('users', 'username')->ignore($user->id)],
                'email' => [Rule::unique('users', 'email')->ignore($user->id)],
            ]);

            if ($request->filled('password')) {
                $userData['password'] = $request->input('password');
            }

            $user->update($userData);
        } else {
            $request->validate([
                'username' => ['unique:users,username'],
                'email' => ['unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            $userData['password'] = $request->input('password');
            $user = User::create($userData);
        }

        $user->syncRoles([$request->input('role')]);

        return $user;
    }
}
