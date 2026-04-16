<?php

namespace App\Http\Controllers;

use App\Models\Pak;
use App\Models\Category;
use App\Models\Layanan;
use App\Models\Permohonan;
use App\Models\PermohonanItem;
use App\Models\PermohonanDokumen;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PakController extends Controller
{
    // =========================
    // INDEX
    // =========================
    public function index()
    {
        $paks = Pak::latest()->paginate(10);
        return view('pak.index', compact('paks'));
    }

    // =========================
    // CREATE
    // =========================
    public function create()
    {
        $layanans = Layanan::orderBy('nama')->get();
        $categories = Category::orderBy('order')->get();
        $newPakNo = Pak::generateNumber();

        return view('pak.create', compact('categories', 'newPakNo', 'layanans'));
    }

    // =========================
    // 🔥 HELPER (CORE)
    // =========================
    private function buildPermohonanData(Request $request, $oldData = null)
    {
        $data = [];

        $fields = [
            'nama_perusahaan',
            'nama_pic',
            'no_telp',
            'email',
            'alamat',
            'nama_proyek',
            'lokasi',
            'testuji',
            'testuji_external_keterangan',
            'permintaan_khusus'
        ];

        foreach ($fields as $field) {
            $data[$field] = $request->$field;
        }

        $data['dokumen_pendukung'] = $request->dokumen_pendukung ?? [];

        // =========================
        // DOKUMEN
        // =========================
        $dokumens = [];

        foreach ($data['dokumen_pendukung'] as $jenis) {

            $file = $request->file("dokumen_files.$jenis");

            if ($file) {

                $path = $file->store('pak/temp', 'public');

                $dokumens[] = [
                    'jenis' => $jenis,
                    'label' => $jenis,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path
                ];
            } else {

                if ($oldData) {
                    $existing = collect($oldData['dokumens'] ?? [])
                        ->firstWhere('jenis', $jenis);

                    if ($existing) {
                        $dokumens[] = $existing;
                    }
                }
            }
        }

        $data['dokumens'] = $dokumens;

        // =========================
        // ITEMS
        // =========================
        $items = [];

        foreach ($request->items ?? [] as $item) {

            if (empty($item['detail_pekerjaan'])) continue;

            $items[] = [
                'detail_pekerjaan' => $item['detail_pekerjaan'],
                'tanggal_permintaan' => $item['tanggal_permintaan'] ?? null,
                'tanggal_pelaksanaan' => null,
                'durasi' => null,
                'layanan_ids' => $item['layanan_ids'] ?? []
            ];
        }

        $data['items'] = $items;

        return $data;
    }

    // =========================
    // STORE
    // =========================
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $permohonanData = $this->buildPermohonanData($request);
            $projectValue = (int) str_replace('.', '', $request->project_value);

            $pak = Pak::create([
                'pak_number' => $request->project_number,
                'pak_name' => $permohonanData['nama_proyek'] ?? 'PAK',
                'permohonan_data' => $permohonanData,
                'project_value' => $projectValue,
            ]);

            // ITEMS
            $totalCost = 0;

            foreach ($request->items as $categoryId => $rows) {

                if (!is_numeric($categoryId)) continue;

                foreach ($rows as $row) {

                    if (empty($row['operational_needs'])) continue;

                    $qty = floatval($row['qty'] ?? 0);
                    $unit = floatval($row['unit_cost'] ?? 0);
                    $total = floatval($row['total_cost'] ?? ($qty * $unit));

                    $pak->items()->create([
                        'category_id' => $categoryId,
                        'name' => $row['operational_needs'],
                        'description' => $row['description'] ?? '-',
                        'qty' => $qty,
                        'unit_cost' => $unit,
                        'total_cost' => $total,
                    ]);

                    $totalCost += $total;
                }
            }

            $profit = $projectValue - $totalCost;
            $percent = $projectValue > 0 ? ($profit / $projectValue) * 100 : 0;

            $pak->update([
                'total_cost' => $totalCost,
                'profit' => $profit,
                'profit_percentage' => $percent,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'PAK berhasil disimpan'
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // =========================
    // SHOW
    // =========================
    public function show($id)
    {
        $pak = Pak::with('items.category')->findOrFail($id);

        $permohonan = (object) $pak->permohonan_data;

        // dokumen
        $permohonan->dokumens = collect($permohonan->dokumens ?? [])
            ->map(fn($d) => (object) $d);

        // items
        $permohonan->items = collect($permohonan->items ?? [])
            ->map(function ($item) {
                return (object) [
                    'detail_pekerjaan' => $item['detail_pekerjaan'] ?? '',
                    'tanggal_permintaan' => $item['tanggal_permintaan'] ?? '',
                    'layanans' => collect($item['layanan_ids'] ?? [])
                        ->map(fn($id) => (object) ['id' => $id])
                ];
            });

        $layanans = Layanan::pluck('nama', 'id');

        return view('pak.show', compact('pak', 'permohonan', 'layanans'));
    }

    // =========================
    // EDIT
    // =========================
    public function edit($id)
    {
        $pak = Pak::findOrFail($id);

        // 🔥 AMBIL DATA RAW
        $raw = $pak->permohonan_data;

        // 🔥 HANDLE DOUBLE JSON (INI KUNCI)
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }

        // kalau masih string lagi (double encode)
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }

        $permohonan = (object) $raw;

        // =========================
        // DOKUMEN
        // =========================
        $permohonan->dokumens = collect($permohonan->dokumens ?? [])
            ->map(fn($d) => (object) $d);

        // =========================
        // ITEMS
        // =========================
        $permohonan->items = collect($permohonan->items ?? [])
            ->map(function ($item) {

                // 🔥 handle array / object campur
                $item = (array) $item;

                return (object) [
                    'detail_pekerjaan' => $item['detail_pekerjaan'] ?? '',
                    'tanggal_permintaan' => $item['tanggal_permintaan'] ?? '',
                    'layanans' => collect($item['layanan_ids'] ?? [])
                        ->map(fn($id) => (object) ['id' => $id])
                ];
            });

        $categories = Category::all();
        $layanans = Layanan::all();

        return view('pak.edit', compact(
            'pak',
            'categories',
            'permohonan',
            'layanans'
        ));
    }
    // =========================
    // UPDATE
    // =========================
    public function update(Request $request, $id)
    {
        $pak = Pak::findOrFail($id);

        DB::beginTransaction();

        try {

            $oldData = is_array($pak->permohonan_data)
                ? $pak->permohonan_data
                : json_decode($pak->permohonan_data, true);

            $permohonanData = $this->buildPermohonanData($request, $oldData);

            $projectValue = (int) str_replace('.', '', $request->project_value);

            $pak->update([
                'permohonan_data' => $permohonanData,
                'project_value' => $projectValue,
                'total_cost' => $request->nilai_pak_raw,
                'profit' => $request->nilai_margin_raw,
                'profit_percentage' => $request->nilai_project_raw > 0
                    ? round(($request->nilai_margin_raw / $request->nilai_project_raw) * 100, 2)
                    : 0
            ]);

            $pak->items()->delete();

            foreach ($request->items as $categoryId => $rows) {

                foreach ($rows as $row) {

                    if (empty($row['operational_needs'])) continue;

                    $pak->items()->create([
                        'category_id' => $categoryId,
                        'name' => $row['operational_needs'],
                        'description' => $row['description'] ?? '-',
                        'qty' => $row['qty'] ?? 0,
                        'unit_cost' => $row['unit_cost'] ?? 0,
                        'total_cost' => $row['total_cost'] ?? 0,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'PAK berhasil diupdate'
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // =========================
    // CONVERT
    // =========================
    public function convert($id)
    {
        DB::beginTransaction();

        try {

            $pak = Pak::findOrFail($id);

            // =========================
            // 🔥 NORMALIZE DATA (WAJIB)
            // =========================
            $data = $pak->permohonan_data;

            // kalau string → decode
            if (is_string($data)) {
                $data = json_decode($data, true);
            }

            // kalau masih string (double encode)
            if (is_string($data)) {
                $data = json_decode($data, true);
            }

            if (!is_array($data)) {
                throw new \Exception('Format data tidak valid');
            }

            // =========================
            // 🔥 VALIDASI MINIMAL
            // =========================
            if (empty($data['items'])) {
                throw new \Exception('Tidak ada item pekerjaan');
            }

            // =========================
            // 🔥 CREATE PERMOHONAN
            // =========================
            $permohonan = Permohonan::create([
                'nomor' => Permohonan::generateNomor(),
                'nama_perusahaan' => $data['nama_perusahaan'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'nama_pic' => $data['nama_pic'] ?? null,
                'no_telp' => $data['no_telp'] ?? null,
                'email' => $data['email'] ?? null,
                'testuji' => $data['testuji'] ?? null,
                'testuji_external_keterangan' => $data['testuji_external_keterangan'] ?? null,
                'lokasi' => $data['lokasi'] ?? null,
                'nama_proyek' => $data['nama_proyek'] ?? null,
                'permintaan_khusus' => $data['permintaan_khusus'] ?? null,
            ]);

            // =========================
            // 🔥 ITEMS
            // =========================
            foreach ($data['items'] as $item) {

                // handle object / array campur
                $item = (array) $item;

                $permItem = PermohonanItem::create([
                    'permohonan_id' => $permohonan->id,
                    'detail_pekerjaan' => $item['detail_pekerjaan'] ?? null,
                    'tanggal_permintaan' => $item['tanggal_permintaan'] ?? null,
                    'tanggal_pelaksanaan' => null,
                    'durasi' => null,
                ]);

                $permItem->layanans()->sync($item['layanan_ids'] ?? []);
            }

            // =========================
            // 🔥 DOKUMEN
            // =========================
            foreach ($data['dokumens'] ?? [] as $doc) {

                $doc = (array) $doc;

                if (empty($doc['file_path'])) continue;

                if (!Storage::disk('public')->exists($doc['file_path'])) continue;

                $newPath = str_replace('pak/temp', 'permohonan', $doc['file_path']);

                Storage::disk('public')->copy($doc['file_path'], $newPath);

                PermohonanDokumen::create([
                    'permohonan_id' => $permohonan->id,
                    'jenis' => $doc['jenis'] ?? null,
                    'label' => $doc['label'] ?? null,
                    'file_path' => $newPath,
                    'file_name' => $doc['file_name'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permohonan berhasil dibuat',
                'redirect' => route('permohonan.index')
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal convert: ' . $e->getMessage()
            ]);
        }
    }

    public function exportPdf($id)
    {
        $pak = Pak::with(['items.category'])->findOrFail($id);

        $categories = Category::query()
            ->get()
            ->keyBy('id');

        $permohonan = (object) ($pak->permohonan_data ?? []);

        $pdf = Pdf::loadView('pak.pdf', compact('pak', 'categories', 'permohonan'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('pak-' . $pak->pak_number . '.pdf');
    }
}
