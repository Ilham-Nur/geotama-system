<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Models\ProyekTimesheet;
use App\Models\ProyekTimesheetUpload;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

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
        $validated = $request->validate([
            'inspection_date' => 'nullable|date',
            'remarks' => 'nullable|string|max:500',
        ], [
            'inspection_date.date' => 'Tanggal inspeksi tidak valid.',
        ]);

        ProyekTimesheet::create([
            'proyek_id' => $proyek->id,
            'form_no' => ProyekTimesheet::generateFormNo($proyek),
            'inspection_date' => $validated['inspection_date'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
            'generated_by' => auth()->id(),
            'status' => 'generated',
        ]);

        return redirect()
            ->route('proyek.show', $proyek->id)
            ->with('success', 'Form timesheet berhasil dibuat. Klik Export Template PDF untuk mencetak format baku.');
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

        return $pdf->stream($timesheet->form_no . '.pdf');
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

    public function uploadTimesheetHardcopy(Request $request, Proyek $proyek, ProyekTimesheet $timesheet)
    {
        if ((int) $timesheet->proyek_id !== (int) $proyek->id) {
            abort(404);
        }

        $validated = $request->validate([
            'hardcopy_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'notes' => 'nullable|string|max:255',
        ], [
            'hardcopy_file.required' => 'File hardcopy wajib diupload.',
            'hardcopy_file.mimes' => 'File harus PDF, JPG, JPEG, atau PNG.',
            'hardcopy_file.max' => 'Ukuran file maksimal 10 MB.',
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
