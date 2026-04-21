<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeCertificate;
use App\Models\EmployeeContract;
use App\Models\EmployeeWorkExperience;
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
        $employee->load(['user.roles', 'documents', 'contracts', 'workExperiences', 'certificates']);

        return response()->json([
            'id' => $employee->id,
            'employee_code' => $employee->employee_code,
            'full_name' => $employee->full_name,
            'position' => $employee->position,
            'phone' => $employee->phone,
            'emergency_contact_name' => $employee->emergency_contact_name,
            'emergency_contact_phone' => $employee->emergency_contact_phone,
            'hire_date' => optional($employee->hire_date)->format('d M Y'),
            'employment_status' => $employee->employment_status,
            'gender' => $employee->gender,
            'birth_place' => $employee->birth_place,
            'birth_date' => optional($employee->birth_date)->format('d M Y'),
            'identity_number' => $employee->identity_number,
            'bpjs_ketenagakerjaan_number' => $employee->bpjs_ketenagakerjaan_number,
            'bpjs_kesehatan_number' => $employee->bpjs_kesehatan_number,
            'marital_status' => $employee->marital_status,
            'nationality' => $employee->nationality,
            'religion' => $employee->religion,
            'important_information' => $employee->important_information,
            'last_education' => $employee->last_education,
            'last_education_file_url' => $employee->last_education_file_path ? asset('storage/' . $employee->last_education_file_path) : null,
            'last_education_file_name' => $employee->last_education_file_name,
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
            'work_experiences' => $employee->workExperiences
                ->map(fn($experience) => [
                    'company_name' => $experience->company_name,
                    'position' => $experience->position,
                    'start_year' => $experience->start_year,
                    'end_year' => $experience->end_year,
                    'certificate_file_name' => $experience->certificate_file_name,
                    'certificate_file_url' => $experience->certificate_file_path ? asset('storage/' . $experience->certificate_file_path) : null,
                ])
                ->values()
                ->all(),
            'certificates' => $employee->certificates
                ->sortByDesc('created_at')
                ->values()
                ->map(function ($certificate) {
                    $isExternal = $certificate->certificate_type === 'external';
                    $daysUntilExpired = $certificate->expired_at
                        ? now()->startOfDay()->diffInDays($certificate->expired_at->startOfDay(), false)
                        : null;
                    $isExpiringSoon = $isExternal && $daysUntilExpired !== null && $daysUntilExpired >= 0 && $daysUntilExpired <= 90;
                    $isExpired = $isExternal && $daysUntilExpired !== null && $daysUntilExpired < 0;

                    return [
                        'certificate_type' => $certificate->certificate_type,
                        'certificate_name' => $certificate->certificate_name,
                        'issuer' => $certificate->issuer,
                        'issued_at' => optional($certificate->issued_at)->format('d M Y'),
                        'expired_at' => optional($certificate->expired_at)->format('d M Y'),
                        'file_name' => $certificate->file_name,
                        'file_url' => $certificate->file_path ? asset('storage/' . $certificate->file_path) : null,
                        'days_until_expired' => $daysUntilExpired,
                        'is_expiring_soon' => $isExpiringSoon,
                        'is_expired' => $isExpired,
                    ];
                })
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

        $employee = Employee::create([
            'user_id' => $user?->id,
            'employee_code' => $validated['employee_code'],
            'full_name' => $validated['full_name'],
            'position' => $validated['position'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
            'hire_date' => $validated['hire_date'] ?? null,
            'employment_status' => $validated['employment_status'],
            'gender' => $validated['gender'] ?? null,
            'birth_place' => $validated['birth_place'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'full_address' => $validated['full_address'] ?? null,
            'identity_number' => $validated['identity_number'] ?? null,
            'bpjs_ketenagakerjaan_number' => $validated['bpjs_ketenagakerjaan_number'] ?? null,
            'bpjs_kesehatan_number' => $validated['bpjs_kesehatan_number'] ?? null,
            'marital_status' => $validated['marital_status'] ?? null,
            'nationality' => $validated['nationality'] ?? null,
            'religion' => $validated['religion'] ?? null,
            'important_information' => $validated['important_information'] ?? null,
            'last_education' => $validated['last_education'] ?? null,
        ]);

        if ($request->hasFile('last_education_file')) {
            $file = $request->file('last_education_file');
            $employee->update([
                'last_education_file_path' => $file->store('employee-education', 'public'),
                'last_education_file_name' => $file->getClientOriginalName(),
            ]);
        }

        $this->syncWorkExperiences($request, $employee);
        $this->syncCertificates($request, $employee);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Data karyawan berhasil ditambahkan.');
    }

    public function edit(Employee $employee)
    {
        $employee->load(['user.roles', 'workExperiences', 'certificates']);
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
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
            'hire_date' => $validated['hire_date'] ?? null,
            'employment_status' => $validated['employment_status'],
            'gender' => $validated['gender'] ?? null,
            'birth_place' => $validated['birth_place'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'full_address' => $validated['full_address'] ?? null,
            'identity_number' => $validated['identity_number'] ?? null,
            'bpjs_ketenagakerjaan_number' => $validated['bpjs_ketenagakerjaan_number'] ?? null,
            'bpjs_kesehatan_number' => $validated['bpjs_kesehatan_number'] ?? null,
            'marital_status' => $validated['marital_status'] ?? null,
            'nationality' => $validated['nationality'] ?? null,
            'religion' => $validated['religion'] ?? null,
            'important_information' => $validated['important_information'] ?? null,
            'last_education' => $validated['last_education'] ?? null,
        ]);

        if ($request->hasFile('last_education_file')) {
            if ($employee->last_education_file_path && Storage::disk('public')->exists($employee->last_education_file_path)) {
                Storage::disk('public')->delete($employee->last_education_file_path);
            }

            $file = $request->file('last_education_file');
            $employee->update([
                'last_education_file_path' => $file->store('employee-education', 'public'),
                'last_education_file_name' => $file->getClientOriginalName(),
            ]);
        }

        $this->syncWorkExperiences($request, $employee);
        $this->syncCertificates($request, $employee);

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
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'hire_date' => ['nullable', 'date'],
            'employment_status' => ['required', Rule::in(['tetap', 'kontrak', 'magang'])],
            'gender' => ['nullable', 'in:laki-laki,perempuan'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'full_address' => ['nullable', 'string'],
            'identity_number' => ['nullable', 'string', 'max:100'],
            'bpjs_ketenagakerjaan_number' => ['nullable', 'string', 'max:100'],
            'bpjs_kesehatan_number' => ['nullable', 'string', 'max:100'],
            'marital_status' => ['nullable', 'in:belum_kawin,kawin,cerai_hidup,cerai_mati'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'religion' => ['nullable', 'string', 'max:100'],
            'important_information' => ['nullable', 'string'],
            'last_education' => ['nullable', 'string', 'max:255'],
            'last_education_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],

            'work_experiences' => ['nullable', 'array'],
            'work_experiences.*.id' => ['nullable', 'integer'],
            'work_experiences.*.company_name' => ['required_with:work_experiences.*.start_year,work_experiences.*.end_year,work_experiences.*.position', 'nullable', 'string', 'max:255'],
            'work_experiences.*.position' => ['nullable', 'string', 'max:255'],
            'work_experiences.*.start_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'work_experiences.*.end_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'work_experience_files.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],

            'certificates' => ['nullable', 'array'],
            'certificates.*.id' => ['nullable', 'integer'],
            'certificates.*.certificate_type' => ['required_with:certificates.*.certificate_name', Rule::in(['internal', 'external'])],
            'certificates.*.certificate_name' => ['required_with:certificates.*.certificate_type', 'nullable', 'string', 'max:255'],
            'certificates.*.issuer' => ['nullable', 'string', 'max:255'],
            'certificates.*.issued_at' => ['nullable', 'date'],
            'certificates.*.expired_at' => ['nullable', 'date'],
            'certificate_files.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],

            'create_system_account' => ['nullable', 'boolean'],
            'username' => [Rule::requiredIf($createSystemAccount), 'nullable', 'string', 'max:255'],
            'email' => [Rule::requiredIf($createSystemAccount), 'nullable', 'email', 'max:255'],
            'password' => [Rule::requiredIf($createSystemAccount && !$employeeId), 'nullable', 'string', 'min:8', 'confirmed'],
            'role' => [Rule::requiredIf($createSystemAccount), 'nullable', 'exists:roles,name'],
        ]);
    }

    private function syncWorkExperiences(Request $request, Employee $employee): void
    {
        if (!$request->has('work_experiences') && !$request->hasFile('work_experience_files')) {
            return;
        }

        $rows = $request->input('work_experiences', []);
        $files = $request->file('work_experience_files', []);
        $keptIds = [];

        foreach ($rows as $index => $row) {
            if (blank($row['company_name'] ?? null)) {
                continue;
            }

            $file = $files[$index] ?? null;
            $experience = !empty($row['id'])
                ? $employee->workExperiences()->whereKey($row['id'])->first()
                : null;

            if (!$experience) {
                $experience = $employee->workExperiences()->make();
            }

            if ($file && $experience->certificate_file_path && Storage::disk('public')->exists($experience->certificate_file_path)) {
                Storage::disk('public')->delete($experience->certificate_file_path);
            }

            $experience->fill([
                'company_name' => $row['company_name'],
                'position' => $row['position'] ?? null,
                'start_year' => $row['start_year'] ?? null,
                'end_year' => $row['end_year'] ?? null,
            ]);

            if ($file) {
                $experience->certificate_file_path = $file->store('employee-work-experiences', 'public');
                $experience->certificate_file_name = $file->getClientOriginalName();
            }

            $experience->employee()->associate($employee);
            $experience->save();
            $keptIds[] = $experience->id;
        }

        $toDelete = $employee->workExperiences()
            ->when(!empty($keptIds), fn($query) => $query->whereNotIn('id', $keptIds))
            ->when(empty($keptIds), fn($query) => $query)
            ->get();

        foreach ($toDelete as $item) {
            if ($item->certificate_file_path && Storage::disk('public')->exists($item->certificate_file_path)) {
                Storage::disk('public')->delete($item->certificate_file_path);
            }
            $item->delete();
        }
    }

    private function syncCertificates(Request $request, Employee $employee): void
    {
        if (!$request->has('certificates') && !$request->hasFile('certificate_files')) {
            return;
        }

        $rows = $request->input('certificates', []);
        $files = $request->file('certificate_files', []);
        $keptIds = [];

        foreach ($rows as $index => $row) {
            if (blank($row['certificate_name'] ?? null)) {
                continue;
            }

            $file = $files[$index] ?? null;
            $certificate = !empty($row['id'])
                ? $employee->certificates()->whereKey($row['id'])->first()
                : null;

            if (!$certificate) {
                $certificate = $employee->certificates()->make();
            }

            if ($file && $certificate->file_path && Storage::disk('public')->exists($certificate->file_path)) {
                Storage::disk('public')->delete($certificate->file_path);
            }

            $certificate->fill([
                'certificate_type' => $row['certificate_type'],
                'certificate_name' => $row['certificate_name'],
                'issuer' => $row['issuer'] ?? null,
                'issued_at' => $row['issued_at'] ?? null,
                'expired_at' => $row['expired_at'] ?? null,
            ]);

            if ($file) {
                $certificate->file_path = $file->store('employee-certificates', 'public');
                $certificate->file_name = $file->getClientOriginalName();
            }

            $certificate->employee()->associate($employee);
            $certificate->save();
            $keptIds[] = $certificate->id;
        }

        $toDelete = $employee->certificates()
            ->when(!empty($keptIds), fn($query) => $query->whereNotIn('id', $keptIds))
            ->when(empty($keptIds), fn($query) => $query)
            ->get();

        foreach ($toDelete as $item) {
            if ($item->file_path && Storage::disk('public')->exists($item->file_path)) {
                Storage::disk('public')->delete($item->file_path);
            }
            $item->delete();
        }
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
