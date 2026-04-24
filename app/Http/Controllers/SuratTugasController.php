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
        $suratTugas = SuratTugas::with(['proyek:id,no_proyek', 'biayaItems'])
            ->latest()
            ->paginate(10);

        $proyekList = Proyek::query()
            ->select(['id', 'no_proyek'])
            ->orderByDesc('id')
            ->get();

        return view('surat-tugas.index', compact('suratTugas', 'proyekList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proyek_id' => ['required', 'exists:proyek,id'],
            'tanggal_berangkat' => ['required', 'date'],
            'tanggal_kembali' => ['required', 'date', 'after_or_equal:tanggal_berangkat'],
            'transportasi' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.deskripsi' => ['required', 'string', 'max:255'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.total' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated) {
            $grandTotal = collect($validated['items'])->sum(fn($item) => (float) $item['total']);

            $suratTugas = SuratTugas::create([
                'proyek_id' => $validated['proyek_id'],
                'tanggal_berangkat' => $validated['tanggal_berangkat'],
                'tanggal_kembali' => $validated['tanggal_kembali'],
                'transportasi' => $validated['transportasi'],
                'keterangan' => $validated['keterangan'] ?? null,
                'grand_total' => $grandTotal,
            ]);

            $suratTugas->biayaItems()->createMany($validated['items']);
        });

        return redirect()->route('surat-tugas.index')->with('success', 'Surat tugas berhasil ditambahkan.');
    }

    public function update(Request $request, SuratTugas $suratTugas)
    {
        $validated = $request->validate([
            'proyek_id' => ['required', 'exists:proyek,id'],
            'tanggal_berangkat' => ['required', 'date'],
            'tanggal_kembali' => ['required', 'date', 'after_or_equal:tanggal_berangkat'],
            'transportasi' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.deskripsi' => ['required', 'string', 'max:255'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.total' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated, $suratTugas) {
            $grandTotal = collect($validated['items'])->sum(fn($item) => (float) $item['total']);

            $suratTugas->update([
                'proyek_id' => $validated['proyek_id'],
                'tanggal_berangkat' => $validated['tanggal_berangkat'],
                'tanggal_kembali' => $validated['tanggal_kembali'],
                'transportasi' => $validated['transportasi'],
                'keterangan' => $validated['keterangan'] ?? null,
                'grand_total' => $grandTotal,
            ]);

            $suratTugas->biayaItems()->delete();
            $suratTugas->biayaItems()->createMany($validated['items']);
        });

        return redirect()->route('surat-tugas.index')->with('success', 'Surat tugas berhasil diupdate.');
    }

    public function destroy(SuratTugas $suratTugas)
    {
        $suratTugas->delete();

        return redirect()->route('surat-tugas.index')->with('success', 'Surat tugas berhasil dihapus.');
    }
}
