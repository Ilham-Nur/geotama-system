<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Models\SuratTugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuratTugasController extends Controller
{
    public function index()
    {
        $suratTugas = SuratTugas::with('proyek')
            ->latest()
            ->get();

        return view('surat-tugas.index', compact('suratTugas'));
    }

    public function create()
    {
        $proyeks = Proyek::orderBy('no_proyek')->get();

        return view('surat-tugas.create', compact('proyeks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_surat' => ['required', 'string', 'max:255', 'unique:surat_tugas,no_surat'],
            'proyek_id' => ['required', 'exists:proyek,id'],
            'tanggal_berangkat' => ['required', 'date'],
            'tanggal_kembali' => ['required', 'date', 'after_or_equal:tanggal_berangkat'],
            'transportasi' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.deskripsi' => ['required', 'string', 'max:255'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01'],
            'items.*.total' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated) {
            $grandTotal = collect($validated['items'])->sum(fn($item) => (float) $item['total']);

            $suratTugas = SuratTugas::create([
                'no_surat' => $validated['no_surat'],
                'proyek_id' => $validated['proyek_id'],
                'tanggal_berangkat' => $validated['tanggal_berangkat'],
                'tanggal_kembali' => $validated['tanggal_kembali'],
                'transportasi' => $validated['transportasi'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
                'grand_total' => $grandTotal,
            ]);

            foreach ($validated['items'] as $item) {
                $suratTugas->biayaItems()->create([
                    'deskripsi' => $item['deskripsi'],
                    'qty' => $item['qty'],
                    'total' => $item['total'],
                ]);
            }
        });

        return redirect()->route('surat-tugas.index')->with('success', 'Surat tugas berhasil dibuat.');
    }

    public function edit(SuratTugas $suratTugas)
    {
        $suratTugas->load('biayaItems');
        $proyeks = Proyek::orderBy('no_proyek')->get();

        return view('surat-tugas.edit', compact('suratTugas', 'proyeks'));
    }

    public function update(Request $request, SuratTugas $suratTugas)
    {
        $validated = $request->validate([
            'no_surat' => ['required', 'string', 'max:255', 'unique:surat_tugas,no_surat,' . $suratTugas->id],
            'proyek_id' => ['required', 'exists:proyek,id'],
            'tanggal_berangkat' => ['required', 'date'],
            'tanggal_kembali' => ['required', 'date', 'after_or_equal:tanggal_berangkat'],
            'transportasi' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.deskripsi' => ['required', 'string', 'max:255'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01'],
            'items.*.total' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated, $suratTugas) {
            $grandTotal = collect($validated['items'])->sum(fn($item) => (float) $item['total']);

            $suratTugas->update([
                'no_surat' => $validated['no_surat'],
                'proyek_id' => $validated['proyek_id'],
                'tanggal_berangkat' => $validated['tanggal_berangkat'],
                'tanggal_kembali' => $validated['tanggal_kembali'],
                'transportasi' => $validated['transportasi'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
                'grand_total' => $grandTotal,
            ]);

            $suratTugas->biayaItems()->delete();

            foreach ($validated['items'] as $item) {
                $suratTugas->biayaItems()->create([
                    'deskripsi' => $item['deskripsi'],
                    'qty' => $item['qty'],
                    'total' => $item['total'],
                ]);
            }
        });

        return redirect()->route('surat-tugas.index')->with('success', 'Surat tugas berhasil diperbarui.');
    }

    public function destroy(SuratTugas $suratTugas)
    {
        $suratTugas->delete();

        return redirect()->route('surat-tugas.index')->with('success', 'Surat tugas berhasil dihapus.');
    }
}
