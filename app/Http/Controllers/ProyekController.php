<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Models\ProyekTimesheet;
use App\Models\ProyekTimesheetUpload;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProyekController extends Controller
{
    public function index()
    {
        $proyeks = Proyek::with([
            'permohonan.items.layanans', // untuk pekerjaan
            'users' // untuk PIC
        ])->latest()->get();

        return view('proyek.index', compact('proyeks'));
    }

    public function show($id)
    {
        $proyek = Proyek::with([
            'users',
            'permohonan.items.layanans',
            'permohonan.dokumens',
            'timesheets.generator',
            'timesheets.verifier',
            'timesheets.uploads.uploader',
        ])->findOrFail($id);

        return view('proyek.show', compact('proyek'));
    }

    public function showPekerjaan($proyekId, $itemId, $layananId)
    {
        $proyek = \App\Models\Proyek::with([
            'permohonan',
            'users',
            'permohonan.items.layanans'
        ])->findOrFail($proyekId);

        $item = $proyek->permohonan->items()
            ->with('layanans')
            ->findOrFail($itemId);

        $layanan = $item->layanans()
            ->where('layanans.id', $layananId)
            ->firstOrFail();

        return view('proyek.pekerjaan-show', compact('proyek', 'item', 'layanan'));
    }

    public function storeTimesheetForm(Request $request, Proyek $proyek)
    {
        $timesheet = ProyekTimesheet::create([
            'proyek_id' => $proyek->id,
            'form_no' => ProyekTimesheet::generateFormNo($proyek),
            'inspection_date' => null,
            'remarks' => null,
            'generated_by' => auth()->id(),
            'status' => 'generated',
        ]);

        return redirect()
            ->route('proyek.timesheet.export-pdf', [$proyek->id, $timesheet->id]);
    }

    public function exportTimesheetPdf(Proyek $proyek, ProyekTimesheet $timesheet)
    {
        if ((int) $timesheet->proyek_id !== (int) $proyek->id) {
            abort(404);
        }

        $proyek->loadMissing(['permohonan', 'users']);

        if ($timesheet->status === 'generated') {
            $timesheet->update([
                'status' => 'in_field',
            ]);
        }

        $pdf = Pdf::loadView('proyek.timesheet-pdf', [
            'proyek' => $proyek,
            'timesheet' => $timesheet,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('timesheet-template-' . ($proyek->no_proyek ?: $proyek->id) . '.pdf');
    }


    public function verifyTimesheet(Proyek $proyek, ProyekTimesheet $timesheet)
    {
        if ((int) $timesheet->proyek_id !== (int) $proyek->id) {
            abort(404);
        }

        if (! $timesheet->uploads()->exists()) {
            return redirect()
                ->route('proyek.show', $proyek->id)
                ->with('error', 'Timesheet belum bisa diverifikasi karena belum ada hardcopy yang diupload.');
        }

        $timesheet->update([
            'status' => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        return redirect()
            ->route('proyek.show', $proyek->id)
            ->with('success', 'Timesheet berhasil diverifikasi.');
    }


    public function updateTimesheetHardcopy(Request $request, Proyek $proyek, ProyekTimesheet $timesheet, ProyekTimesheetUpload $upload)
    {
        if ((int) $timesheet->proyek_id !== (int) $proyek->id || (int) $upload->proyek_timesheet_id !== (int) $timesheet->id) {
            abort(404);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:255',
            'work_date' => 'nullable|date',
            'duration_days' => 'nullable|integer|min:1|max:365',
            'hardcopy_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ], [
            'hardcopy_file.mimes' => 'File harus PDF, JPG, JPEG, atau PNG.',
            'hardcopy_file.max' => 'Ukuran file maksimal 10 MB.',
            'work_date.date' => 'Tanggal kerja tidak valid.',
            'duration_days.integer' => 'Durasi hari harus angka.',
            'duration_days.min' => 'Durasi hari minimal 1 hari.',
        ]);

        if (! $request->filled('notes') && ! $request->hasFile('hardcopy_file') && ! $request->filled('work_date') && ! $request->filled('duration_days')) {
            return redirect()
                ->route('proyek.show', $proyek->id)
                ->with('error', 'Isi catatan/tanggal/durasi atau pilih file baru untuk memperbarui hardcopy.');
        }

        $payload = [
            'notes' => $validated['notes'] ?? $upload->notes,
            'work_date' => $validated['work_date'] ?? $upload->work_date,
            'duration_days' => $validated['duration_days'] ?? $upload->duration_days,
        ];

        if ($request->hasFile('hardcopy_file')) {
            if ($upload->file_path && Storage::disk('public')->exists($upload->file_path)) {
                Storage::disk('public')->delete($upload->file_path);
            }

            $file = $request->file('hardcopy_file');
            $payload['file_path'] = $file->store('timesheets/hardcopy', 'public');
            $payload['file_name'] = $file->getClientOriginalName();
            $payload['mime_type'] = $file->getMimeType();
            $payload['file_size'] = $file->getSize();
            $payload['uploaded_by'] = auth()->id();
        }

        $upload->update($payload);

        if ($timesheet->status === 'verified') {
            $timesheet->update([
                'status' => 'completed',
                'verified_by' => null,
                'verified_at' => null,
            ]);
        }

        return redirect()
            ->route('proyek.show', $proyek->id)
            ->with('success', 'Hardcopy timesheet berhasil diperbarui.');
    }

    public function deleteTimesheetHardcopy(Proyek $proyek, ProyekTimesheet $timesheet, ProyekTimesheetUpload $upload)
    {
        if ((int) $timesheet->proyek_id !== (int) $proyek->id || (int) $upload->proyek_timesheet_id !== (int) $timesheet->id) {
            abort(404);
        }

        if ($upload->file_path && Storage::disk('public')->exists($upload->file_path)) {
            Storage::disk('public')->delete($upload->file_path);
        }

        $upload->delete();

        $remainingUploads = $timesheet->uploads()->count();

        if ($remainingUploads === 0) {
            $timesheet->update([
                'status' => 'in_field',
                'verified_by' => null,
                'verified_at' => null,
            ]);
        } elseif ($timesheet->status === 'verified') {
            $timesheet->update([
                'status' => 'completed',
                'verified_by' => null,
                'verified_at' => null,
            ]);
        }

        return redirect()
            ->route('proyek.show', $proyek->id)
            ->with('success', 'Hardcopy timesheet berhasil dihapus.');
    }

    public function uploadTimesheetHardcopy(Request $request, Proyek $proyek, ProyekTimesheet $timesheet)
    {
        if ((int) $timesheet->proyek_id !== (int) $proyek->id) {
            abort(404);
        }

        $validated = $request->validate([
            'hardcopy_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'work_date' => 'required|date',
            'duration_days' => 'required|integer|min:1|max:365',
            'notes' => 'nullable|string|max:255',
        ], [
            'hardcopy_file.required' => 'File hardcopy wajib diupload.',
            'hardcopy_file.mimes' => 'File harus PDF, JPG, JPEG, atau PNG.',
            'hardcopy_file.max' => 'Ukuran file maksimal 10 MB.',
            'work_date.required' => 'Tanggal timesheet wajib diisi.',
            'work_date.date' => 'Tanggal timesheet tidak valid.',
            'duration_days.required' => 'Durasi hari wajib diisi.',
            'duration_days.integer' => 'Durasi hari harus angka.',
            'duration_days.min' => 'Durasi minimal 1 hari.',
        ]);

        $nextVersion = ((int) $timesheet->uploads()->max('version_no')) + 1;

        $file = $validated['hardcopy_file'];
        $path = $file->store('timesheets/hardcopy', 'public');

        ProyekTimesheetUpload::create([
            'proyek_timesheet_id' => $timesheet->id,
            'proyek_id' => $proyek->id,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'version_no' => $nextVersion,
            'work_date' => $validated['work_date'],
            'duration_days' => $validated['duration_days'],
            'notes' => $validated['notes'] ?? null,
            'uploaded_by' => auth()->id(),
        ]);

        if ($timesheet->status !== 'verified') {
            $timesheet->update([
                'status' => 'completed',
            ]);
        }

        return redirect()
            ->route('proyek.show', $proyek->id)
            ->with('success', 'Hardcopy timesheet berhasil diupload (versi ' . $nextVersion . '). Status otomatis menjadi completed.');
    }
}
