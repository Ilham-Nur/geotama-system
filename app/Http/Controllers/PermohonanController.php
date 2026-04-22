<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Client;
use App\Models\Permohonan;
use App\Models\PermohonanDokumen;
use App\Models\Proyek;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;

class PermohonanController extends Controller
{
    public function index()
    {
        $permohonans = Permohonan::with('items.layanans')->latest()->get();

        return view('permohonan.index', compact('permohonans'));
    }

    public function create()
    {
        $layanans = Layanan::orderBy('nama')->get();
        $clients = Client::orderBy('nama_perusahaan')->get();

        return view('permohonan.create', compact('layanans', 'clients'));
    }

    public function store(Request $request)
    {
        $this->validateRequest($request);
        $this->validateDokumenWajib($request);

        DB::beginTransaction();

        try {
            $client = $this->resolveClient($request);

            $permohonan = Permohonan::create([
                'nomor' => Permohonan::generateNomor(),
                'client_id' => $client->id,
                'nama_perusahaan' => $client->nama_perusahaan,
                'alamat' => $client->alamat,
                'nama_pic' => $client->nama_pic,
                'no_telp' => $client->no_telp,
                'email' => $client->email,
                'testuji' => $request->testuji,
                'testuji_external_keterangan' => $request->testuji === 'quality_external'
                    ? $request->testuji_external_keterangan
                    : null,
                'lokasi' => $request->lokasi,
                'nama_proyek' => $request->nama_proyek,
                'permintaan_khusus' => $request->permintaan_khusus,
            ]);

            $this->saveItems($permohonan, $request);
            $this->saveDokumens($permohonan, $request);

            DB::commit();

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Permohonan berhasil disimpan.',
                    'redirect' => route('permohonan.index'),
                ]);
            }

            return redirect()
                ->route('permohonan.index')
                ->with('success', 'Permohonan berhasil disimpan.');
        } catch (\Throwable $th) {
            DB::rollBack();

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $th->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $th->getMessage());
        }
    }

    public function show($id)
    {
        $permohonan = Permohonan::with(['items.layanans', 'dokumens'])
            ->findOrFail($id);

        $pics = User::role('staff')
            // ->where('is_active', 1)
            ->get();

        $generatedProjectNo = Proyek::generateProjectNo();

        return view('permohonan.show', compact('permohonan', 'pics', 'generatedProjectNo'));
    }

    public function edit($id)
    {
        $permohonan = Permohonan::with(['items.layanans', 'dokumens'])->findOrFail($id);
        $layanans = Layanan::orderBy('nama')->get();
        $clients = Client::orderBy('nama_perusahaan')->get();

        return view('permohonan.edit', compact('permohonan', 'layanans', 'clients'));
    }

    public function update(Request $request, $id)
    {
        $permohonan = Permohonan::with(['items.layanans', 'dokumens'])->findOrFail($id);

        $this->validateRequest($request, $permohonan);
        $this->validateDokumenWajib($request, $permohonan);

        DB::beginTransaction();

        try {
            $client = $this->resolveClient($request);

            $permohonan->update([
                'client_id' => $client->id,
                'nama_perusahaan' => $client->nama_perusahaan,
                'alamat' => $client->alamat,
                'nama_pic' => $client->nama_pic,
                'no_telp' => $client->no_telp,
                'email' => $client->email,
                'testuji' => $request->testuji,
                'testuji_external_keterangan' => $request->testuji === 'quality_external'
                    ? $request->testuji_external_keterangan
                    : null,
                'lokasi' => $request->lokasi,
                'nama_proyek' => $request->nama_proyek,
                'permintaan_khusus' => $request->permintaan_khusus,
            ]);

            $permohonan->items()->delete();
            $this->saveItems($permohonan, $request);

            $this->syncDokumens($permohonan, $request);

            DB::commit();

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Permohonan berhasil diupdate.',
                    'redirect' => route('permohonan.index'),
                ]);
            }

            return redirect()
                ->route('permohonan.index')
                ->with('success', 'Permohonan berhasil diupdate.');
        } catch (\Throwable $th) {
            DB::rollBack();

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $th->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        $permohonan = Permohonan::with('dokumens')->findOrFail($id);

        foreach ($permohonan->dokumens as $dok) {
            if (Storage::disk('public')->exists($dok->file_path)) {
                Storage::disk('public')->delete($dok->file_path);
            }
        }

        $permohonan->delete();

        return redirect()
            ->route('permohonan.index')
            ->with('success', 'Permohonan berhasil dihapus.');
    }

    public function previewDokumen(PermohonanDokumen $dokumen)
    {
        $path = storage_path('app/public/' . $dokumen->file_path);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->file($path);
    }

    public function downloadDokumen(PermohonanDokumen $dokumen)
    {
        $path = storage_path('app/public/' . $dokumen->file_path);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download($path, $dokumen->file_name ?? basename($path));
    }

    protected function validateRequest(Request $request, ?Permohonan $permohonan = null)
    {
        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'client_mode' => 'nullable|in:new,existing',
            'nama_perusahaan' => 'required_without:client_id|nullable|string|max:255',
            'alamat' => 'required_without:client_id|nullable|string',
            'nama_pic' => 'required_without:client_id|nullable|string|max:255',
            'no_telp' => 'required_without:client_id|nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'testuji' => 'required|in:quality_internal,quality_external',
            'testuji_external_keterangan' => 'nullable|string|max:255',
            'lokasi' => 'required|string',
            'nama_proyek' => 'required|string|max:255',
            'permintaan_khusus' => 'nullable|string',

            'items' => 'required|array|min:1',
            'items.*.detail_pekerjaan' => 'required|string|max:255',
            'items.*.tanggal_permintaan' => 'required|date',
            'items.*.layanan_ids' => 'required|array|min:1',
            'items.*.layanan_ids.*' => 'required|exists:layanans,id',

            'dokumen_pendukung' => 'nullable|array',
            'dokumen_pendukung.*' => 'in:drawing,p_id_isometric,wps_pqr,standar,foto,schedule,lainnya',

            'dokumen_files.drawing' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:5120',
            'dokumen_files.p_id_isometric' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:5120',
            'dokumen_files.wps_pqr' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:5120',
            'dokumen_files.standar' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:5120',
            'dokumen_files.foto' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:5120',
            'dokumen_files.schedule' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:5120',
            'dokumen_files.lainnya' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:5120',

            'dokumen_lainnya_text' => 'nullable|string|max:255',
        ]);

        if ($request->testuji === 'quality_external' && !$request->filled('testuji_external_keterangan')) {
            throw ValidationException::withMessages([
                'testuji_external_keterangan' => 'Keterangan quality external wajib diisi.',
            ]);
        }

        if ($request->input('client_mode') === 'existing' && !$request->filled('client_id')) {
            throw ValidationException::withMessages([
                'client_id' => 'Silakan pilih client terlebih dahulu.',
            ]);
        }
    }

    protected function resolveClient(Request $request): Client
    {
        if ($request->filled('client_id')) {
            return Client::findOrFail($request->client_id);
        }

        return Client::create([
            'nama_perusahaan' => $request->nama_perusahaan,
            'alamat' => $request->alamat,
            'nama_pic' => $request->nama_pic,
            'no_telp' => $request->no_telp,
            'email' => $request->email,
        ]);
    }

    protected function validateDokumenWajib(Request $request, ?Permohonan $permohonan = null)
    {
        $selected = $request->dokumen_pendukung ?? [];
        $errors = [];

        foreach ($selected as $jenis) {
            $hasNewFile = $request->hasFile("dokumen_files.$jenis");

            $existingDoc = null;
            if ($permohonan) {
                $existingDoc = $permohonan->dokumens->firstWhere('jenis', $jenis);
            }

            if (!$hasNewFile && !$existingDoc) {
                $errors["dokumen_files.$jenis"] = 'File untuk dokumen ' . $this->labelDokumen($jenis) . ' wajib diupload.';
            }

            if ($jenis === 'lainnya' && !$request->filled('dokumen_lainnya_text')) {
                $errors['dokumen_lainnya_text'] = 'Keterangan dokumen lainnya wajib diisi.';
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    protected function generateNomor()
    {
        $year = date('Y');

        $last = Permohonan::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;

        if ($last) {
            $lastSequence = (int) substr($last->nomor, -4);
            $nextNumber = $lastSequence + 1;
        }

        return 'GGI-FP-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    protected function saveItems(Permohonan $permohonan, Request $request)
    {
        foreach ($request->items as $item) {
            $permohonanItem = $permohonan->items()->create([
                'detail_pekerjaan' => $item['detail_pekerjaan'],
                'tanggal_permintaan' => $item['tanggal_permintaan'],
                'tanggal_pelaksanaan' => null,
                'durasi' => null,
            ]);

            $permohonanItem->layanans()->sync($item['layanan_ids']);
        }
    }

    protected function saveDokumens(Permohonan $permohonan, Request $request)
    {
        $selected = $request->dokumen_pendukung ?? [];

        foreach ($selected as $jenis) {
            if ($request->hasFile("dokumen_files.$jenis")) {
                $file = $request->file("dokumen_files.$jenis");
                $path = $file->store('permohonan_dokumen', 'public');

                $label = $jenis === 'lainnya' ? $request->dokumen_lainnya_text : $this->labelDokumen($jenis);

                $permohonan->dokumens()->create([
                    'jenis' => $jenis,
                    'label' => $label,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }
        }
    }

    protected function syncDokumens(Permohonan $permohonan, Request $request)
    {
        $selected = $request->dokumen_pendukung ?? [];
        $currentDocs = $permohonan->dokumens->keyBy('jenis');

        foreach ($currentDocs as $jenis => $dok) {
            if (!in_array($jenis, $selected)) {
                if (Storage::disk('public')->exists($dok->file_path)) {
                    Storage::disk('public')->delete($dok->file_path);
                }
                $dok->delete();
            }
        }

        foreach ($selected as $jenis) {
            $existingDoc = $permohonan->dokumens()->where('jenis', $jenis)->first();
            $label = $jenis === 'lainnya' ? $request->dokumen_lainnya_text : $this->labelDokumen($jenis);

            if ($request->hasFile("dokumen_files.$jenis")) {
                if ($existingDoc && Storage::disk('public')->exists($existingDoc->file_path)) {
                    Storage::disk('public')->delete($existingDoc->file_path);
                }

                $file = $request->file("dokumen_files.$jenis");
                $path = $file->store('permohonan_dokumen', 'public');

                if ($existingDoc) {
                    $existingDoc->update([
                        'label' => $label,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                    ]);
                } else {
                    $permohonan->dokumens()->create([
                        'jenis' => $jenis,
                        'label' => $label,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                    ]);
                }
            } else {
                if ($existingDoc) {
                    $existingDoc->update([
                        'label' => $label,
                    ]);
                }
            }
        }
    }

    protected function labelDokumen($jenis)
    {
        return match ($jenis) {
            'drawing' => 'Drawing',
            'p_id_isometric' => 'P&ID / Isometric',
            'wps_pqr' => 'WPS / PQR',
            'standar' => 'Standar',
            'foto' => 'Foto',
            'schedule' => 'Schedule',
            'lainnya' => 'Lainnya',
            default => ucfirst($jenis),
        };
    }

    public function exportPdf($id)
    {
        $permohonan = Permohonan::with(['items.layanans', 'dokumens'])->findOrFail($id);

        $pdf = Pdf::loadView('permohonan.pdf', compact('permohonan'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('permohonan-' . $permohonan->nomor . '.pdf');
    }

    public function jadikanProject(Request $request, $id)
    {
        $request->validate([
            'project_no' => 'required|string|unique:proyek,no_proyek',
            'pic_ids' => 'required|array',
            'pic_ids.*' => 'exists:users,id',
            'description' => 'nullable|string',
        ]);

        $permohonan = Permohonan::findOrFail($id);

        if ($permohonan->proyek) {
            return response()->json([
                'message' => 'Permohonan sudah jadi proyek.'
            ], 422);
        }

        DB::beginTransaction();

        try {
            $proyek = Proyek::create([
                'no_proyek' => Proyek::generateProjectNo(),
                'permohonan_id' => $permohonan->id,
                'deskripsi' => $request->description,
                'status' => Proyek::STATUS_PROGRESS,
            ]);

            $proyek->users()->attach($request->pic_ids);

            DB::commit();

            return response()->json([
                'message' => 'Proyek berhasil dibuat.',
                'redirect' => route('proyek.show', $proyek->id),
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function blankPdf()
    {
        return Pdf::loadView('permohonan.blank_pdf')
            ->setPaper('A4', 'portrait')
            ->stream('form-permohonan-blank.pdf');
    }
}
