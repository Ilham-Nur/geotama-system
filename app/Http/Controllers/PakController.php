<?php

namespace App\Http\Controllers;

use App\Models\Pak;
use App\Models\Category;
use App\Models\Client;
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
    private function parseCurrency($value): int
    {
        if ($value === null) {
            return 0;
        }

        return (int) preg_replace('/[^\d]/', '', (string) $value);
    }

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
        $clients = Client::orderBy('nama_perusahaan')->get();
        $newPakNo = Pak::generateNumber();

        return view('pak.create', compact('categories', 'newPakNo', 'layanans', 'clients'));
    }

    // =========================
    // 🔥 HELPER (CORE)
    // =========================
    private function buildPermohonanData(Request $request, $oldData = null)
    {
        $data = [];
        $client = $this->resolveClient($request);

        $data['client_id'] = $client->id;
        $data['client_mode'] = $request->input('client_mode', 'existing');
        $data['nama_perusahaan'] = $client->nama_perusahaan;
        $data['nama_pic'] = $client->nama_pic;
        $data['no_telp'] = $client->no_telp;
        $data['email'] = $client->email;
        $data['alamat'] = $client->alamat;

        $fields = [
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

    private function resolveClient(Request $request): Client
    {
        if ($request->input('client_mode') === 'existing') {
            if (!$request->filled('client_id')) {
                throw new \Exception('Silakan pilih client terlebih dahulu.');
            }

            return Client::findOrFail($request->client_id);
        }

        return Client::create([
            'nama_perusahaan' => $request->nama_perusahaan,
            'nama_pic' => $request->nama_pic,
            'no_telp' => $request->no_telp,
            'email' => $request->email,
            'alamat' => $request->alamat,
        ]);
    }

    // =========================
    // STORE
    // =========================
    public function store(Request $request)
    {
        // dd($request->all());


        DB::beginTransaction();


        try {

            $permohonanData = $this->buildPermohonanData($request);
            $projectValue = $this->parseCurrency($request->project_value);
            $tax = $request->filled('nilai_pajak')
                ? $this->parseCurrency($request->nilai_pajak)
                : (int) round($projectValue * 0.02);

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
            $profitAfterTax = $profit - $tax;

            $pak->update([
                'tax' => $tax,
                'total_cost' => $totalCost,
                'profit' => $profitAfterTax,
                'profit_percentage' => $projectValue > 0 ? ($profitAfterTax / $projectValue) * 100 : 0,
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
        $clients = Client::orderBy('nama_perusahaan')->get();

        return view('pak.edit', compact(
            'pak',
            'categories',
            'permohonan',
            'layanans',
            'clients'
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

            $projectValue = $this->parseCurrency($request->project_value);
            $tax = $request->filled('nilai_pajak')
                ? $this->parseCurrency($request->nilai_pajak)
                : (int) round($projectValue * 0.02);
            $totalCost = $this->parseCurrency($request->nilai_pak_raw);
            $margin = $this->parseCurrency($request->nilai_margin_raw);

            $pak->update([
                'permohonan_data' => $permohonanData,
                'project_value' => $projectValue,
                'tax' => $tax,
                'total_cost' => $totalCost,
                'profit' => $margin,
                'profit_percentage' => $projectValue > 0
                    ? round(($margin / $projectValue) * 100, 2)
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
                'client_id' => $data['client_id'] ?? null,
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
