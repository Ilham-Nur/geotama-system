<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Models\ProyekTimesheet;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\LaporanNdtCertificate;
use App\Models\LaporanNdtInspectionItem;
use App\Models\LaporanNdtPhoto;
use App\Models\LaporanNdtReport;
use App\Models\LaporanPekerjaan;
use App\Models\LaporanFileReport;
use App\Models\LaporanFotoLampiran;
use App\Models\Layanan;
use App\Models\NdtAcceptanceCriterion;
use App\Models\NdtApprovalPerson;
use App\Models\NdtCertificate;
use App\Models\NdtInspectionDescription;
use App\Models\NdtProcedure;
use App\Models\NdtTestingStandard;
use App\Models\PermohonanItem;
use Carbon\Carbon;

class ProyekController extends Controller
{
    public function index()
    {
        $proyeks = Proyek::with([
            'permohonan.items.layanans', // untuk pekerjaan
            'users' // untuk PIC
        ])
            ->orderByRaw(
                "CASE status
                    WHEN 'progress' THEN 1
                    WHEN 'reporting' THEN 2
                    WHEN 'endorse' THEN 3
                    WHEN 'close' THEN 4
                    ELSE 5
                END"
            )
            ->latest()
            ->get();

        $statusOptions = [
            Proyek::STATUS_PROGRESS => 'Progress',
            Proyek::STATUS_REPORTING => 'Reporting',
            Proyek::STATUS_ENDORSE => 'Endorse',
            Proyek::STATUS_CLOSE => 'Selesai',
        ];

        return view('proyek.index', compact('proyeks', 'statusOptions'));
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
        $laporan = \App\Models\LaporanPekerjaan::with([
            'fileReport',
            'fotoLampiran',
            'ndtReport.inspectionItems.photos',
            'ndtReport.certificates',
        ])
            ->where('proyek_id', $proyekId)
            ->where('item_id', $itemId)
            ->where('layanan_id', $layananId)
            ->latest()
            ->first();

        $serviceCode = $this->detectNdtServiceCode($layanan->nama ?? '');
        $isNdtLayanan = in_array($serviceCode, ['MT', 'PT'], true);
        $serviceSequence = $isNdtLayanan ? $this->resolveServiceSequence($proyek, (int) $item->id, (int) $layanan->id) : null;
        $reportNoPreview = $isNdtLayanan
            ? (($laporan?->ndtReport?->report_no) ?? "{$proyek->no_proyek}/{$serviceSequence}/{$serviceCode}")
            : null;
        $selectedCertificateIds = $laporan?->ndtReport?->certificates
            ? $laporan->ndtReport->certificates->pluck('certificate_id')->filter()->all()
            : [];

        $materialOptions = match ($serviceCode) {
            'MT' => [
                'magnetic_testing' => 'Magnetic Testing',
                'magnetic_spray' => 'Magnetic Spray',
            ],
            'PT' => [
                'cleaner' => 'Cleaner',
                'penetrant' => 'Penetrant',
                'developer' => 'Developer',
            ],
            default => [],
        };

        $ndtMasterData = $isNdtLayanan ? [
            'procedures' => NdtProcedure::where('is_active', true)->orderBy('code')->get(),
            'criteria' => NdtAcceptanceCriterion::where('is_active', true)->orderBy('code')->get(),
            'standards' => NdtTestingStandard::where('is_active', true)->orderBy('code')->get(),
            'descriptions' => NdtInspectionDescription::where('is_active', true)->orderBy('name')->get(),
            'certificates' => NdtCertificate::query()
                ->where(function ($query) use ($selectedCertificateIds) {
                    $query->where('is_active', true);

                    if (!empty($selectedCertificateIds)) {
                        $query->orWhereIn('id', $selectedCertificateIds);
                    }
                })
                ->orderBy('title')
                ->get(),
            'approvals' => NdtApprovalPerson::where('is_active', true)->orderBy('role')->orderBy('name')->get()->groupBy('role'),
            'approval_roles' => NdtApprovalPerson::ROLES,
        ] : [];
        $ndtReferenceReports = $isNdtLayanan
            ? LaporanNdtReport::with([
                'laporanPekerjaan.proyek.permohonan',
                'laporanPekerjaan.item',
                'laporanPekerjaan.layanan',
            ])
                ->where('service_code', $serviceCode)
                ->where('status', LaporanNdtReport::STATUS_SUBMIT)
                ->when($laporan?->ndtReport?->id, function ($query, $currentReportId) {
                    $query->where('id', '<>', $currentReportId);
                })
                ->latest()
                ->limit(50)
                ->get()
            : collect();

        return view('proyek.pekerjaan-show', compact(
            'proyek',
            'item',
            'layanan',
            'laporan',
            'isNdtLayanan',
            'serviceCode',
            'serviceSequence',
            'reportNoPreview',
            'materialOptions',
            'ndtMasterData',
            'ndtReferenceReports'
        ));
    }

    public function tambahReportPekerjaan(Request $request, Proyek $proyek, $itemId, $layananId)
    {
        $layanan = Layanan::findOrFail($layananId);
        if ($this->detectNdtServiceCode($layanan->nama ?? '')) {
            return $this->saveNdtReport($request, $proyek, (int) $itemId, (int) $layananId);
        }

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
        $layanan = Layanan::findOrFail($layananId);
        if ($this->detectNdtServiceCode($layanan->nama ?? '')) {
            return $this->saveNdtReport($request, $proyek, (int) $itemId, (int) $layananId);
        }

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
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui laporan.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function ndtReportReference(Proyek $proyek, $itemId, $layananId, LaporanNdtReport $reference)
    {
        $layanan = Layanan::findOrFail($layananId);
        $serviceCode = $this->detectNdtServiceCode($layanan->nama ?? '');

        abort_unless(in_array($serviceCode, ['MT', 'PT'], true), 404);
        abort_unless($reference->service_code === $serviceCode, 404);
        abort_unless($reference->status === LaporanNdtReport::STATUS_SUBMIT, 404);

        $reference->load([
            'laporanPekerjaan.proyek.permohonan',
            'laporanPekerjaan.item',
            'laporanPekerjaan.layanan',
            'inspectionItems',
            'certificates',
        ]);

        $approvalPeople = NdtApprovalPerson::where('is_active', true)
            ->get()
            ->groupBy('role');
        $approvals = collect(NdtApprovalPerson::ROLES)
            ->mapWithKeys(function ($label, $role) use ($reference, $approvalPeople) {
                $name = $reference->{$role . '_name'};
                $position = $reference->{$role . '_position'};
                $person = ($approvalPeople->get($role) ?? collect())
                    ->first(function ($candidate) use ($name, $position) {
                        if ($candidate->name !== $name) {
                            return false;
                        }

                        return !$position || $candidate->position === $position;
                    });

                return [
                    $role => [
                        'id' => $person?->id,
                        'name' => $name,
                        'position' => $position,
                    ],
                ];
            });

        return response()->json([
            'id' => $reference->id,
            'report_no' => $reference->report_no,
            'service_code' => $reference->service_code,
            'project' => [
                'no_proyek' => $reference->laporanPekerjaan?->proyek?->no_proyek,
                'client' => $reference->laporanPekerjaan?->proyek?->permohonan?->nama_perusahaan,
                'project_name' => $reference->laporanPekerjaan?->proyek?->permohonan?->nama_proyek,
                'date' => optional($reference->laporanPekerjaan?->tanggal_pelaksanaan)->format('Y-m-d'),
            ],
            'certificate_ids' => $reference->certificates
                ->pluck('certificate_id')
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->values(),
            'fields' => [
                'part_no' => $reference->part_no,
                'description' => $reference->description,
                'material' => $reference->material,
                'temperature' => $reference->temperature,
                'procedure_id' => $reference->procedure_id,
                'criteria_id' => $reference->criteria_id,
                'testing_standard_id' => $reference->testing_standard_id,
                'surface_conditions' => $reference->surface_conditions ?? [],
                'preparation_conditions' => $reference->preparation_conditions ?? [],
                'stage_ofs' => $reference->stage_ofs ?? [],
                'examinations' => $reference->examinations ?? [],
                'techniques' => $reference->techniques ?? [],
                'test_types' => $reference->test_types ?? [],
                'penetrant_applications' => $reference->penetrant_applications ?? [],
                'material_rows' => $reference->material_rows ?? [],
                'dwell_times' => $reference->dwell_times ?? [],
                'blacklight_intensity' => $reference->blacklight_intensity,
                'approvals' => $approvals,
            ],
            'inspection_items' => $reference->inspectionItems
                ->values()
                ->map(function ($inspection) {
                    return [
                        'description_master_id' => $inspection->description_master_id,
                        'code' => $inspection->code,
                        'id_no' => $inspection->id_no,
                        'diameter_mm' => $inspection->diameter_mm,
                        'length_mm' => $inspection->length_mm,
                        'thickness_mm' => $inspection->thickness_mm,
                        'result' => $inspection->result,
                        'remark' => $inspection->remark,
                        'sketch_annotations' => $inspection->sketch_annotations ?? [],
                    ];
                }),
        ]);
    }

    public function exportNdtReportPdf(Proyek $proyek, $itemId, $layananId)
    {
        $laporan = LaporanPekerjaan::with([
            'item',
            'layanan',
            'ndtReport.inspectionItems.photos',
            'ndtReport.photos',
            'ndtReport.certificates.certificate',
        ])
            ->where('proyek_id', $proyek->id)
            ->where('item_id', $itemId)
            ->where('layanan_id', $layananId)
            ->latest()
            ->firstOrFail();

        abort_unless($laporan->ndtReport, 404);

        $proyek->loadMissing('permohonan');
        $ndtReport = $laporan->ndtReport;
        $sketches = NdtInspectionDescription::whereIn(
            'id',
            $ndtReport->inspectionItems->pluck('description_master_id')->filter()->unique()
        )->get()->keyBy('id');
        $masterData = [
            'procedure' => $ndtReport->procedure_id ? NdtProcedure::find($ndtReport->procedure_id) : null,
            'criteria' => $ndtReport->criteria_id ? NdtAcceptanceCriterion::find($ndtReport->criteria_id) : null,
            'standard' => $ndtReport->testing_standard_id ? NdtTestingStandard::find($ndtReport->testing_standard_id) : null,
        ];

        $filename = 'ndt-report-' . preg_replace('/[^A-Za-z0-9\-]+/', '-', $ndtReport->report_no) . '.pdf';

        $pdf = Pdf::loadView('proyek.ndt-report-pdf', compact('proyek', 'laporan', 'ndtReport', 'masterData', 'sketches'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream($filename);
    }

    private function saveNdtReport(Request $request, Proyek $proyek, int $itemId, int $layananId)
    {
        $proyek->loadMissing('permohonan.items.layanans');

        $item = $proyek->permohonan->items()->findOrFail($itemId);
        $layanan = $item->layanans()
            ->where('layanans.id', $layananId)
            ->firstOrFail();

        $serviceCode = $this->detectNdtServiceCode($layanan->nama ?? '');
        abort_unless(in_array($serviceCode, ['MT', 'PT'], true), 404);

        $laporan = LaporanPekerjaan::with('ndtReport')
            ->where('proyek_id', $proyek->id)
            ->where('item_id', $itemId)
            ->where('layanan_id', $layananId)
            ->latest()
            ->first();

        $existingReportId = $laporan?->ndtReport?->id;

        $validated = $request->validate([
            'tanggal_pelaksanaan' => ['required', 'date'],
            'action' => ['required', 'in:draft,submit'],
            'report_no' => [
                'required',
                'string',
                'max:255',
                Rule::unique('laporan_ndt_reports', 'report_no')->ignore($existingReportId),
            ],
            'service_code' => ['required', Rule::in(['MT', 'PT'])],
            'service_sequence' => ['required', 'integer', 'min:1'],
            'part_no' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'material' => ['nullable', 'string', 'max:255'],
            'temperature' => ['nullable', 'string', 'max:255'],
            'procedure_id' => ['nullable', 'integer', 'exists:ndt_procedures,id'],
            'criteria_id' => ['nullable', 'integer', 'exists:ndt_acceptance_criteria,id'],
            'testing_standard_id' => ['nullable', 'integer', 'exists:ndt_testing_standards,id'],
            'surface_conditions' => ['nullable', 'array'],
            'surface_conditions.*' => ['string', 'max:255'],
            'preparation_conditions' => ['nullable', 'array'],
            'preparation_conditions.*' => ['string', 'max:255'],
            'stage_ofs' => ['nullable', 'array'],
            'stage_ofs.*' => ['string', 'max:255'],
            'examinations' => ['nullable', 'array'],
            'examinations.*' => ['string', 'max:255'],
            'techniques' => ['nullable', 'array'],
            'techniques.*' => ['string', 'max:255'],
            'test_types' => ['nullable', 'array'],
            'test_types.*' => ['string', 'max:255'],
            'penetrant_applications' => ['nullable', 'array'],
            'penetrant_applications.*' => ['string', 'max:255'],
            'material_rows' => ['nullable', 'array'],
            'material_rows.*.trade_name' => ['nullable', 'string', 'max:255'],
            'material_rows.*.batch_number' => ['nullable', 'string', 'max:255'],
            'dwell_times' => ['nullable', 'array'],
            'dwell_times.penetrant_minutes' => ['nullable', 'numeric', 'min:0'],
            'dwell_times.developer_minutes' => ['nullable', 'numeric', 'min:0'],
            'blacklight_intensity' => ['nullable', 'string', 'max:255'],
            'inspection_items' => ['required', 'array', 'min:1'],
            'inspection_items.*.id' => ['nullable', 'integer', 'exists:laporan_ndt_inspection_items,id'],
            'inspection_items.*.description_master_id' => ['nullable', 'integer', 'exists:ndt_inspection_descriptions,id'],
            'inspection_items.*.code' => ['nullable', 'string', 'max:255'],
            'inspection_items.*.id_no' => ['nullable', 'string', 'max:255'],
            'inspection_items.*.diameter_mm' => ['nullable', 'numeric', 'min:0'],
            'inspection_items.*.length_mm' => ['nullable', 'numeric', 'min:0'],
            'inspection_items.*.thickness_mm' => ['nullable', 'numeric', 'min:0'],
            'inspection_items.*.result' => ['nullable', Rule::in(['acc', 'reject', 'repair'])],
            'inspection_items.*.remark' => ['nullable', 'string', 'max:1000'],
            'inspection_items.*.sketch_annotations' => ['nullable', 'string'],
            'certificates' => ['nullable', 'array'],
            'certificates.*' => ['integer', 'exists:ndt_certificates,id'],
            'approvals' => ['nullable', 'array'],
            'approvals.examiner' => ['nullable', 'integer', 'exists:ndt_approval_people,id'],
            'approvals.qc_inspector' => ['nullable', 'integer', 'exists:ndt_approval_people,id'],
            'approvals.owner_representative' => ['nullable', 'integer', 'exists:ndt_approval_people,id'],
            'approvals.surveyor' => ['nullable', 'integer', 'exists:ndt_approval_people,id'],
            'photos' => ['nullable', 'array'],
            'photos.*.before' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'photos.*.during' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'photos.*.after' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($validated['service_code'] !== $serviceCode) {
            return response()->json([
                'message' => 'Kode layanan report tidak sesuai dengan layanan proyek.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            if (!$laporan) {
                $laporan = LaporanPekerjaan::create([
                    'proyek_id' => $proyek->id,
                    'item_id' => $itemId,
                    'layanan_id' => $layananId,
                    'tanggal_pelaksanaan' => $validated['tanggal_pelaksanaan'],
                    'action' => $validated['action'],
                    'created_by' => auth()->id(),
                ]);
            } else {
                $laporan->update([
                    'tanggal_pelaksanaan' => $validated['tanggal_pelaksanaan'],
                    'action' => $validated['action'],
                ]);
            }

            $this->syncItemExecutionDate($itemId, $validated['tanggal_pelaksanaan']);

            $approvalData = $this->resolveApprovalSnapshot($request->input('approvals', []));

            $ndtReport = LaporanNdtReport::updateOrCreate(
                ['laporan_pekerjaan_id' => $laporan->id],
                array_merge([
                    'report_no' => $validated['report_no'],
                    'service_code' => $serviceCode,
                    'service_sequence' => $validated['service_sequence'],
                    'part_no' => $this->blankToNull($request->input('part_no')),
                    'description' => $this->blankToNull($request->input('description')),
                    'material' => $this->blankToNull($request->input('material')),
                    'temperature' => $this->blankToNull($request->input('temperature')),
                    'procedure_id' => $this->blankToNull($request->input('procedure_id')),
                    'criteria_id' => $this->blankToNull($request->input('criteria_id')),
                    'testing_standard_id' => $this->blankToNull($request->input('testing_standard_id')),
                    'surface_conditions' => $request->input('surface_conditions', []),
                    'preparation_conditions' => $request->input('preparation_conditions', []),
                    'stage_ofs' => $request->input('stage_ofs', []),
                    'examinations' => $request->input('examinations', []),
                    'techniques' => $request->input('techniques', []),
                    'test_types' => $request->input('test_types', []),
                    'penetrant_applications' => $request->input('penetrant_applications', []),
                    'material_rows' => $this->cleanNestedArray($request->input('material_rows', [])),
                    'dwell_times' => $serviceCode === 'MT'
                        ? []
                        : $this->cleanNestedArray($request->input('dwell_times', [])),
                    'blacklight_intensity' => $this->blankToNull($request->input('blacklight_intensity')),
                    'status' => $validated['action'],
                    'created_by' => $laporan->ndtReport?->created_by ?? auth()->id(),
                    'updated_by' => auth()->id(),
                ], $approvalData)
            );

            $this->syncNdtInspectionItems($request, $ndtReport, $proyek->id);
            $this->syncNdtCertificates($request, $ndtReport);

            DB::commit();

            $proyek->load('permohonan.items.layanans');
            $proyek->updateStatusDariLaporan();

            $message = $validated['action'] === 'draft'
                ? 'Draft report NDT berhasil disimpan.'
                : 'Report NDT berhasil disubmit.';

            return response()->json([
                'message' => $message,
                'redirect' => route('proyek.pekerjaan.show', [$proyek->id, $itemId, $layananId]),
                'pdf_url' => route('proyek.pekerjaan.ndt.pdf', [$proyek->id, $itemId, $layananId]),
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan report NDT.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    private function syncItemExecutionDate(int $itemId, string $tanggalPelaksanaan): void
    {
        $tanggalPermintaan = PermohonanItem::where('id', $itemId)->value('tanggal_permintaan');
        $durasi = $tanggalPermintaan
            ? Carbon::parse($tanggalPermintaan)->diffInDays(Carbon::parse($tanggalPelaksanaan))
            : 0;

        PermohonanItem::where('id', $itemId)->update([
            'tanggal_pelaksanaan' => $tanggalPelaksanaan,
            'durasi' => $durasi,
        ]);
    }

    private function resolveApprovalSnapshot(array $approvalInput): array
    {
        $approvalData = [];

        foreach (NdtApprovalPerson::ROLES as $role => $label) {
            $personId = $approvalInput[$role] ?? null;
            $person = $personId ? NdtApprovalPerson::find($personId) : null;

            $approvalData["{$role}_name"] = $person?->name;
            $approvalData["{$role}_position"] = $person?->position;
        }

        return $approvalData;
    }

    private function syncNdtInspectionItems(Request $request, LaporanNdtReport $ndtReport, int $proyekId): void
    {
        $submittedIds = [];
        $existingItems = $ndtReport->inspectionItems()->with('photos')->get()->keyBy('id');
        $descriptionIds = collect($request->input('inspection_items', []))
            ->pluck('description_master_id')
            ->filter()
            ->unique()
            ->values();
        $descriptions = NdtInspectionDescription::whereIn('id', $descriptionIds)->get()->keyBy('id');

        foreach ($request->input('inspection_items', []) as $index => $row) {
            $rowId = $this->blankToNull($row['id'] ?? null);

            if ($rowId && !$existingItems->has((int) $rowId)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "inspection_items.{$index}.id" => 'Item inspeksi tidak sesuai dengan report ini.',
                ]);
            }

            $descriptionMasterId = $this->blankToNull($row['description_master_id'] ?? null);
            $description = $descriptionMasterId ? $descriptions->get((int) $descriptionMasterId)?->name : null;
            $result = $this->blankToNull($row['result'] ?? null);
            $remark = $result === LaporanNdtInspectionItem::RESULT_ACC
                ? 'No relevant indication was detected during time of tested'
                : $this->blankToNull($row['remark'] ?? null);

            $inspectionItem = $rowId ? $existingItems->get((int) $rowId) : new LaporanNdtInspectionItem();
            $inspectionItem->fill([
                'laporan_ndt_report_id' => $ndtReport->id,
                'sort_order' => (int) $index,
                'description_master_id' => $descriptionMasterId,
                'description' => $description,
                'code' => $this->blankToNull($row['code'] ?? null),
                'id_no' => $this->blankToNull($row['id_no'] ?? null),
                'diameter_mm' => $this->blankToNull($row['diameter_mm'] ?? null),
                'length_mm' => $this->blankToNull($row['length_mm'] ?? null),
                'thickness_mm' => $this->blankToNull($row['thickness_mm'] ?? null),
                'result' => $result,
                'remark' => $remark,
                'sketch_annotations' => $this->decodeSketchAnnotations($row['sketch_annotations'] ?? null),
            ]);
            $inspectionItem->save();
            $submittedIds[] = $inspectionItem->id;

            $this->syncNdtItemPhotos($request, $ndtReport, $inspectionItem, (int) $index, $proyekId);
        }

        $itemsToDelete = $existingItems->reject(fn ($item) => in_array($item->id, $submittedIds, true));
        foreach ($itemsToDelete as $itemToDelete) {
            foreach ($itemToDelete->photos as $photo) {
                Storage::disk('public')->delete($photo->file_path);
                $photo->delete();
            }
            $itemToDelete->delete();
        }
    }

    private function syncNdtCertificates(Request $request, LaporanNdtReport $ndtReport): void
    {
        $certificateIds = collect($request->input('certificates', []))
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $certificates = NdtCertificate::whereIn('id', $certificateIds)->get()->keyBy('id');

        $ndtReport->certificates()->delete();

        foreach ($certificateIds as $index => $certificateId) {
            $certificate = $certificates->get($certificateId);

            if (!$certificate) {
                continue;
            }

            LaporanNdtCertificate::create([
                'laporan_ndt_report_id' => $ndtReport->id,
                'certificate_id' => $certificate->id,
                'certificate_title' => $certificate->title,
                'certificate_no' => $certificate->certificate_no,
                'file_path' => $certificate->file_path,
                'preview_path' => $certificate->preview_path,
                'sort_order' => $index + 1,
            ]);
        }
    }

    private function syncNdtItemPhotos(Request $request, LaporanNdtReport $ndtReport, LaporanNdtInspectionItem $inspectionItem, int $index, int $proyekId): void
    {
        $types = [
            LaporanNdtPhoto::TYPE_BEFORE,
            LaporanNdtPhoto::TYPE_DURING,
            LaporanNdtPhoto::TYPE_AFTER,
        ];

        foreach ($types as $sortOrder => $type) {
            if (!$request->hasFile("photos.{$index}.{$type}")) {
                continue;
            }

            $file = $request->file("photos.{$index}.{$type}");
            $oldPhotos = LaporanNdtPhoto::where('inspection_item_id', $inspectionItem->id)
                ->where('type', $type)
                ->get();

            foreach ($oldPhotos as $oldPhoto) {
                Storage::disk('public')->delete($oldPhoto->file_path);
                $oldPhoto->delete();
            }

            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs(
                "laporan/ndt/photos/{$proyekId}/{$ndtReport->id}",
                $filename,
                'public'
            );

            LaporanNdtPhoto::create([
                'laporan_ndt_report_id' => $ndtReport->id,
                'inspection_item_id' => $inspectionItem->id,
                'type' => $type,
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'sort_order' => $sortOrder,
            ]);
        }
    }

    private function blankToNull($value)
    {
        return $value === '' ? null : $value;
    }

    private function cleanNestedArray(array $rows): array
    {
        return collect($rows)
            ->map(function ($value) {
                if (is_array($value)) {
                    return $this->cleanNestedArray($value);
                }

                return $this->blankToNull($value);
            })
            ->all();
    }

    private function decodeSketchAnnotations($value): array
    {
        if (is_array($value)) {
            $decoded = $value;
        } elseif (is_string($value) && trim($value) !== '') {
            $decoded = json_decode($value, true);
        } else {
            $decoded = [];
        }

        if (!is_array($decoded)) {
            return [];
        }

        return collect($decoded)
            ->map(function ($annotation) {
                if (!is_array($annotation)) {
                    return null;
                }

                $x = isset($annotation['x']) ? (float) $annotation['x'] : null;
                $y = isset($annotation['y']) ? (float) $annotation['y'] : null;
                $text = trim((string) ($annotation['text'] ?? ''));

                if ($x === null || $y === null || $text === '') {
                    return null;
                }

                return [
                    'x' => max(0, min(100, $x)),
                    'y' => max(0, min(100, $y)),
                    'text' => Str::limit($text, 80, ''),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function detectNdtServiceCode(string $serviceName): ?string
    {
        $normalized = Str::upper($serviceName);

        if (Str::contains($normalized, ['MT', 'MAGNETIC'])) {
            return 'MT';
        }

        if (Str::contains($normalized, ['PT', 'PENETRANT'])) {
            return 'PT';
        }

        return null;
    }

    private function resolveServiceSequence(Proyek $proyek, int $currentItemId, int $currentLayananId): int
    {
        $sequence = 1;

        foreach ($proyek->permohonan->items as $projectItem) {
            foreach ($projectItem->layanans as $projectLayanan) {
                if ((int) $projectItem->id === $currentItemId && (int) $projectLayanan->id === $currentLayananId) {
                    return $sequence;
                }

                $sequence++;
            }
        }

        return $sequence;
    }
}
