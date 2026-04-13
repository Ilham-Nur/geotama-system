<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeContract;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        $employee->load(['user.roles', 'documents', 'contracts']);

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
            'contracts' => $employee->contracts
                ->sortByDesc('created_at')
                ->values()
                ->map(fn($contract) => [
                    'id' => $contract->id,
                    'contract_type' => $contract->contract_type,
                    'contract_number' => $contract->contract_number,
                    'signing_date' => optional($contract->signing_date)->format('d M Y'),
                    'generated_file_url' => asset('storage/' . $contract->generated_file_path),
                    'generated_file_name' => $contract->generated_file_name,
                    'hardcopy_file_url' => $contract->hardcopy_file_path ? asset('storage/' . $contract->hardcopy_file_path) : null,
                    'hardcopy_file_name' => $contract->hardcopy_file_name,
                ])
                ->all(),
        ]);
    }

    public function generateContract(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'contract_number' => ['nullable', 'string', 'max:100'],
            'signing_date' => ['required', 'date'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'contract_start_date' => [Rule::requiredIf(in_array($employee->employment_status, ['magang', 'kontrak'], true)), 'nullable', 'date'],
            'contract_end_date' => [Rule::requiredIf(in_array($employee->employment_status, ['magang', 'kontrak'], true)), 'nullable', 'date', 'after_or_equal:contract_start_date'],
            'effective_date' => [Rule::requiredIf($employee->employment_status === 'tetap'), 'nullable', 'date'],
        ]);

        $statusLabel = [
            'magang' => 'Perjanjian Magang',
            'kontrak' => 'Perjanjian Kerja Waktu Tertentu (PKWT)',
            'tetap' => 'Surat Pengangkatan Karyawan Tetap',
        ][$employee->employment_status] ?? 'Kontrak Kerja';

        $contractNumber = $validated['contract_number']
            ?? 'CTR/' . $employee->employee_code . '/' . now()->format('Ym') . '/' . str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT);

        $contractData = [
            'employee' => $employee,
            'status_label' => $statusLabel,
            'contract_number' => $contractNumber,
            'signing_date' => $validated['signing_date'],
            'contract_start_date' => $validated['contract_start_date'] ?? null,
            'contract_end_date' => $validated['contract_end_date'] ?? null,
            'effective_date' => $validated['effective_date'] ?? null,
            'salary' => $validated['salary'] ?? null,
        ];

        $pdf = Pdf::loadView('contracts.employee', $contractData)->setPaper('a4');
        $pdfBinary = $pdf->output();
        $fileName = Str::slug('kontrak-' . $employee->full_name . '-' . now()->format('YmdHis')) . '.pdf';
        $filePath = 'employee-contracts/generated/' . $fileName;

        Storage::disk('public')->put($filePath, $pdfBinary);

        $employee->contracts()->create([
            'contract_type' => $employee->employment_status,
            'contract_number' => $contractNumber,
            'signing_date' => $validated['signing_date'],
            'contract_start_date' => $validated['contract_start_date'] ?? null,
            'contract_end_date' => $validated['contract_end_date'] ?? null,
            'effective_date' => $validated['effective_date'] ?? null,
            'salary' => $validated['salary'] ?? null,
            'generated_file_path' => $filePath,
            'generated_file_name' => $fileName,
            'generated_file_size' => strlen($pdfBinary),
        ]);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Kontrak kerja berhasil digenerate dan tersimpan di arsip kontrak karyawan.');
    }

    public function uploadContractHardcopy(Request $request, Employee $employee, EmployeeContract $contract)
    {
        if ($contract->employee_id !== $employee->id) {
            abort(404);
        }

        $validated = $request->validate([
            'hardcopy_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        if ($contract->hardcopy_file_path && Storage::disk('public')->exists($contract->hardcopy_file_path)) {
            Storage::disk('public')->delete($contract->hardcopy_file_path);
        }

        $hardcopy = $validated['hardcopy_file'];
        $hardcopyPath = $hardcopy->store('employee-contracts/hardcopy', 'public');

        $contract->update([
            'hardcopy_file_path' => $hardcopyPath,
            'hardcopy_file_name' => $hardcopy->getClientOriginalName(),
            'hardcopy_file_size' => $hardcopy->getSize(),
        ]);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Hardcopy kontrak berhasil diupload.');
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
