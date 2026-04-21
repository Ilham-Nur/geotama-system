<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Models\ProyekTimesheet;
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
            'timesheets',
        ])->findOrFail($id);

        return view('proyek.show', compact('proyek'));
    }

    public function exportTimesheetTemplate(Proyek $proyek)
    {
        $proyek->load('permohonan');

        $pdf = Pdf::loadView('proyek.timesheet-template-pdf', compact('proyek'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('timesheet-template-' . $proyek->no_proyek . '.pdf');
    }

    public function storeTimesheet(Request $request, Proyek $proyek)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'durasi_hari' => 'required|integer|min:1|max:365',
            'file_timesheet' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ], [
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.',
            'file_timesheet.mimes' => 'File harus berupa PDF, JPG, JPEG, atau PNG.',
            'file_timesheet.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        $file = $request->file('file_timesheet');
        $path = $file->store('proyek/timesheets', 'public');

        $proyek->timesheets()->create([
            'tanggal' => $validated['tanggal'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
            'durasi_hari' => $validated['durasi_hari'],
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
        ]);

        return redirect()
            ->route('proyek.show', $proyek->id)
            ->with('success', 'Timesheet berhasil diupload.');
    }

    public function destroyTimesheet(Proyek $proyek, ProyekTimesheet $timesheet)
    {
        if ((int) $timesheet->proyek_id !== (int) $proyek->id) {
            abort(404);
        }

        if ($timesheet->file_path && Storage::disk('public')->exists($timesheet->file_path)) {
            Storage::disk('public')->delete($timesheet->file_path);
        }

        $timesheet->delete();

        return redirect()
            ->route('proyek.show', $proyek->id)
            ->with('success', 'File timesheet berhasil dihapus.');
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
}
