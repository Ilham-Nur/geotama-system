<?php

namespace App\Http\Controllers;

use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        $employee->load('documents');

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
            'gender' => ['nullable', 'in:laki-laki,perempuan'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'full_address' => ['nullable', 'string'],
            'identity_number' => ['nullable', 'string', 'max:100'],
            'marital_status' => ['nullable', 'in:belum_kawin,kawin,cerai_hidup,cerai_mati'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'religion' => ['nullable', 'string', 'max:100'],
        ]);

        $employee->update($validated);

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
}
