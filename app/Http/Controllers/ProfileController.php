<?php

namespace App\Http\Controllers;

use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            return redirect()
                ->route('blank')
                ->with('error', 'Data karyawan belum terhubung ke akun Anda. Hubungi admin.');
        }

        $employee->load(['documents', 'workExperiences', 'certificates']);

        return view('profile.show', compact('employee'));
    }

    public function update(Request $request)
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Data karyawan belum terhubung ke akun Anda. Hubungi admin.');
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
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
        ]);

        $employee->update([
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'] ?? null,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
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
            ->route('profile.show')
            ->with('success', 'Profil berhasil diperbarui.');
    }


    public function updatePhoto(Request $request)
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan belum terhubung ke akun Anda. Hubungi admin.',
            ], 422);
        }

        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        if ($employee->photo_path && Storage::disk('public')->exists($employee->photo_path)) {
            Storage::disk('public')->delete($employee->photo_path);
        }

        $path = $request->file('photo')->store('employee-photos', 'public');
        $employee->update(['photo_path' => $path]);

        return response()->json([
            'success' => true,
            'photo_url' => asset('storage/' . $path),
        ]);
    }

    public function storeDocument(Request $request)
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Data karyawan belum terhubung ke akun Anda. Hubungi admin.');
        }

        $validated = $request->validate([
            'document_label' => ['required', 'string', 'max:255'],
            'document_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx', 'max:5120'],
        ]);

        $file = $request->file('document_file');
        $path = $file->store('employee-documents', 'public');

        $employee->documents()->create([
            'document_label' => $validated['document_label'],
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return redirect()
            ->route('profile.show')
            ->with('success', 'Dokumen berhasil ditambahkan.');
    }

    public function destroyDocument(EmployeeDocument $document)
    {
        $employee = auth()->user()->employee;

        if (!$employee || $document->employee_id !== $employee->id) {
            abort(403);
        }

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()
            ->route('profile.show')
            ->with('success', 'Dokumen berhasil dihapus.');
    }

    private function syncWorkExperiences(Request $request, $employee): void
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

    private function syncCertificates(Request $request, $employee): void
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
}
