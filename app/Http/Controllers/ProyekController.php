<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Models\ProyekTimesheet;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\LaporanPekerjaan;
use App\Models\LaporanFileReport;
use App\Models\LaporanFotoLampiran;
use App\Models\PermohonanItem;
use Carbon\Carbon;

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

        // Cek apakah sudah ada laporan untuk kombinasi ini
        $laporan = \App\Models\LaporanPekerjaan::with(['fileReport', 'fotoLampiran'])
            ->where('proyek_id', $proyekId)
            ->where('item_id', $itemId)
            ->where('layanan_id', $layananId)
            ->latest()
            ->first();

        return view('proyek.pekerjaan-show', compact('proyek', 'item', 'layanan', 'laporan'));
    }

    public function tambahReportPekerjaan(Request $request, Proyek $proyek, $itemId, $layananId)
    {
        // ------------------------------------------
        //  1. Validasi
        // ------------------------------------------
        $request->validate([
            'tanggal_pelaksanaan'   => ['required', 'date'],
            'action'                => ['required', 'in:draft,submit'],

            // file_report: boleh foto atau PDF, masing-masing max 10MB
            'file_report'           => ['nullable', 'array'],
            'file_report.*'         => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],

            // foto_lampiran: hanya foto, masing-masing max 5MB
            'foto_lampiran'         => ['nullable', 'array'],
            'foto_lampiran.*'       => ['file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        // ------------------------------------------
        //  2. Simpan dalam DB transaction
        //     agar jika ada error, semua rollback
        // ------------------------------------------
        DB::beginTransaction();

        try {
            // Buat record laporan utama
            $laporan = LaporanPekerjaan::create([
                'proyek_id'           => $proyek->id,
                'item_id'             => $itemId,
                'layanan_id'          => $layananId,
                'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
                'action'              => $request->action,
                'created_by'          => auth()->id(),
            ]);

            $tanggalPermintaan = PermohonanItem::where('id', $itemId)->value('tanggal_permintaan');

            $durasi = Carbon::parse($tanggalPermintaan)
                ->diffInDays(Carbon::parse($request->tanggal_pelaksanaan));

            PermohonanItem::where('id', $itemId)
                ->update([
                    'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
                    'durasi'              => $durasi,
                ]);

            // ------------------------------------------
            //  3. Simpan File Report (foto / PDF)
            // ------------------------------------------
            if ($request->hasFile('file_report')) {
                foreach ($request->file('file_report') as $file) {
                    // Buat nama file unik agar tidak tabrakan
                    $namaUnik = Str::uuid() . '.' . $file->getClientOriginalExtension();

                    // Simpan ke storage/app/public/laporan/report/{proyek_id}/{laporan_id}/
                    $path = $file->storeAs(
                        "laporan/report/{$proyek->id}/{$laporan->id}",
                        $namaUnik,
                        'public'
                    );

                    LaporanFileReport::create([
                        'laporan_pekerjaan_id' => $laporan->id,
                        'nama_file'            => $file->getClientOriginalName(),
                        'path'                 => $path,
                        'mime_type'            => $file->getMimeType(),
                        'size'                 => $file->getSize(),
                    ]);
                }
            }

            // ------------------------------------------
            //  4. Simpan Foto Lampiran (foto saja)
            // ------------------------------------------
            if ($request->hasFile('foto_lampiran')) {
                foreach ($request->file('foto_lampiran') as $foto) {
                    $namaUnik = Str::uuid() . '.' . $foto->getClientOriginalExtension();

                    $path = $foto->storeAs(
                        "laporan/lampiran/{$proyek->id}/{$laporan->id}",
                        $namaUnik,
                        'public'
                    );

                    LaporanFotoLampiran::create([
                        'laporan_pekerjaan_id' => $laporan->id,
                        'nama_file'            => $foto->getClientOriginalName(),
                        'path'                 => $path,
                        'mime_type'            => $foto->getMimeType(),
                        'size'                 => $foto->getSize(),
                    ]);
                }
            }

            DB::commit();

            $proyek->load('permohonan.items.layanans');
            $proyek->updateStatusDariLaporan();

            // ------------------------------------------
            //  5. Response JSON untuk AJAX
            // ------------------------------------------
            $message = $request->action === 'draft'
                ? 'Laporan berhasil disimpan sebagai draft.'
                : 'Laporan berhasil disimpan dan dikirimkan.';

            return response()->json([
                'message'  => $message,
                'laporan'  => $laporan->load(['fileReport', 'fotoLampiran']),
                'redirect' => route('proyek.show', $proyek->id),
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            $laporanId = $laporan->id ?? 0;

            Storage::disk('public')->deleteDirectory("laporan/report/{$proyek->id}/{$laporanId}");
            Storage::disk('public')->deleteDirectory("laporan/lampiran/{$proyek->id}/{$laporanId}");

            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan laporan. Silakan coba lagi.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function updateReportPekerjaan(Request $request, Proyek $proyek, $itemId, $layananId)
    {
        $request->validate([
            'tanggal_pelaksanaan' => ['required', 'date'],
            'action'              => ['required', 'in:draft,submit'],
            'file_report'         => ['nullable', 'array'],
            'file_report.*'       => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
            'foto_lampiran'       => ['nullable', 'array'],
            'foto_lampiran.*'     => ['file', 'mimes:jpg,jpeg,png', 'max:5120'],
            // ID file yang ingin dihapus (dikirim dari frontend)
            'hapus_file_report'   => ['nullable', 'array'],
            'hapus_file_report.*' => ['integer'],
            'hapus_foto_lampiran'   => ['nullable', 'array'],
            'hapus_foto_lampiran.*' => ['integer'],
        ]);

        $laporan = LaporanPekerjaan::with(['fileReport', 'fotoLampiran'])
            ->where('proyek_id', $proyek->id)
            ->where('item_id', $itemId)
            ->where('layanan_id', $layananId)
            ->latest()
            ->firstOrFail();

        DB::beginTransaction();

        try {
            // Update data utama
            $laporan->update([
                'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
                'action'              => $request->action,
            ]);

            $tanggalPermintaan = PermohonanItem::where('id', $itemId)->value('tanggal_permintaan');

            $durasi = Carbon::parse($tanggalPermintaan)
                ->diffInDays(Carbon::parse($request->tanggal_pelaksanaan));

            PermohonanItem::where('id', $itemId)
                ->update([
                    'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
                    'durasi'              => $durasi,
                ]);


            // ------------------------------------------
            //  Hapus file report yang dipilih user
            // ------------------------------------------
            if ($request->filled('hapus_file_report')) {
                $fileHapus = LaporanFileReport::whereIn('id', $request->hapus_file_report)
                    ->where('laporan_pekerjaan_id', $laporan->id)
                    ->get();

                foreach ($fileHapus as $f) {
                    Storage::disk('public')->delete($f->path);
                    $f->delete();
                }
            }

            // ------------------------------------------
            //  Hapus foto lampiran yang dipilih user
            // ------------------------------------------
            if ($request->filled('hapus_foto_lampiran')) {
                $fotoHapus = LaporanFotoLampiran::whereIn('id', $request->hapus_foto_lampiran)
                    ->where('laporan_pekerjaan_id', $laporan->id)
                    ->get();

                foreach ($fotoHapus as $f) {
                    Storage::disk('public')->delete($f->path);
                    $f->delete();
                }
            }

            // ------------------------------------------
            //  Tambah file report baru (jika ada)
            // ------------------------------------------
            if ($request->hasFile('file_report')) {
                foreach ($request->file('file_report') as $file) {
                    $namaUnik = \Illuminate\Support\Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs(
                        "laporan/report/{$proyek->id}/{$laporan->id}",
                        $namaUnik,
                        'public'
                    );

                    LaporanFileReport::create([
                        'laporan_pekerjaan_id' => $laporan->id,
                        'nama_file'            => $file->getClientOriginalName(),
                        'path'                 => $path,
                        'mime_type'            => $file->getMimeType(),
                        'size'                 => $file->getSize(),
                    ]);
                }
            }

            // ------------------------------------------
            //  Tambah foto lampiran baru (jika ada)
            // ------------------------------------------
            if ($request->hasFile('foto_lampiran')) {
                foreach ($request->file('foto_lampiran') as $foto) {
                    $namaUnik = \Illuminate\Support\Str::uuid() . '.' . $foto->getClientOriginalExtension();
                    $path = $foto->storeAs(
                        "laporan/lampiran/{$proyek->id}/{$laporan->id}",
                        $namaUnik,
                        'public'
                    );

                    LaporanFotoLampiran::create([
                        'laporan_pekerjaan_id' => $laporan->id,
                        'nama_file'            => $foto->getClientOriginalName(),
                        'path'                 => $foto->getMimeType(),
                        'mime_type'            => $foto->getMimeType(),
                        'size'                 => $foto->getSize(),
                    ]);
                }
            }

            DB::commit();

            $proyek->load('permohonan.items.layanans');
            $proyek->updateStatusDariLaporan();

            $message = $request->action === 'draft'
                ? 'Laporan berhasil diperbarui sebagai draft.'
                : 'Laporan berhasil diperbarui dan dikirimkan.';

            return response()->json([
                'message'  => $message,
                'redirect' => route('proyek.show', $proyek->id),
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui laporan.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
