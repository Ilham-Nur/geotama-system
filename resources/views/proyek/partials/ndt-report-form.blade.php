@php
    $ndtReport = $laporan?->ndtReport;
    $surfaceConditions = $ndtReport?->surface_conditions ?? [];
    $preparationConditions = $ndtReport?->preparation_conditions ?? [];
    $stageOfs = $ndtReport?->stage_ofs ?? [];
    $examinations = $ndtReport?->examinations ?? [];
    $techniques = $ndtReport?->techniques ?? [];
    $testTypes = $ndtReport?->test_types ?? [];
    $penetrantApplications = $ndtReport?->penetrant_applications ?? [];
    $materialRows = $ndtReport?->material_rows ?? [];
    $dwellTimes = $ndtReport?->dwell_times ?? [];
    $selectedCertificateIds = collect($ndtReport?->certificates)->pluck('certificate_id')->filter()->map(fn ($id) => (int) $id)->all();
    $surfaceOptions = $serviceCode === 'MT'
        ? ['as_welded' => 'AS - WELDED', 'as_rolled' => 'AS ROLLED', 'as_forged' => 'AS - FORGED', 'as_cast' => 'AS - CAST']
        : ['as_welded' => 'As Welded', 'machined' => 'Machined', 'ground' => 'Ground', 'painted' => 'Painted'];
    $preparationOptions = $serviceCode === 'MT'
        ? ['grinding' => 'GRINDING', 'machining' => 'MACHINING', 'recondition' => 'RECONDITION', 'others' => 'OTHERS']
        : ['wire_brush' => 'Wire Brush', 'grinding' => 'Grinding', 'solvent_cleaning' => 'Solvent Cleaning', 'dry' => 'Dry'];
    $stageOfOptions = ['before_pwht' => 'Before PWHT', 'after_pwht' => 'After PWHT', 'before_hydrotest' => 'Before Hydrotest', 'after_hydrotest' => 'After Hydrotest'];
    $examinationOptions = ['after_repair' => 'After Repair', 'after_machining' => 'After Machining', 'other' => 'Other'];
    $techniqueOptions = ['color_contrast' => 'Color Contrast', 'fluorescent' => 'Fluorescent'];
    $testTypeOptions = ['solvent_removable' => 'Solvent Removable', 'post' => 'Post'];
    $penetrantApplicationOptions = ['spraying' => 'Spraying', 'brushing' => 'Brushing'];
    $inspectionRows = $ndtReport?->inspectionItems?->count() ? $ndtReport->inspectionItems : collect([null]);
    $ndtSaveUrl = route('proyek.pekerjaan.tambah-report', [$proyek->id, $item->id, $layanan->id]);
    $ndtUpdateUrl = route('proyek.pekerjaan.update-report', [$proyek->id, $item->id, $layanan->id]);
    $ndtShowUrl = route('proyek.pekerjaan.show', [$proyek->id, $item->id, $layanan->id]);
    $ndtReferenceUrlTemplate = route('proyek.pekerjaan.ndt.reference', [$proyek->id, $item->id, $layanan->id, '__REFERENCE__'], false);
    $existingPhotos = [];
    foreach ($inspectionRows as $inspectionRow) {
        if (!$inspectionRow) {
            continue;
        }

        $existingPhotos[$inspectionRow->id] = $inspectionRow->photos
            ->mapWithKeys(fn ($photo) => [$photo->type => $photo->url])
            ->all();
    }
@endphp

@push('styles')
    <style>
        .ndt-condition-line {
            display: grid;
            grid-template-columns: 130px 16px 1fr;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .ndt-condition-label,
        .ndt-condition-separator {
            color: #4b5563;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
        }

        .ndt-condition-options {
            display: grid;
            grid-template-columns: repeat(4, minmax(130px, 1fr));
            gap: 8px 18px;
        }

        .ndt-condition-options .form-check {
            margin: 0;
            min-height: auto;
        }

        @media (max-width: 767.98px) {
            .ndt-condition-line {
                grid-template-columns: 1fr;
                gap: 4px;
            }

            .ndt-condition-separator {
                display: none;
            }

            .ndt-condition-options {
                grid-template-columns: repeat(2, minmax(120px, 1fr));
            }
        }

        .ndt-table-scroll {
            overflow-x: auto;
        }

        .ndt-inspection-table {
            min-width: 1120px;
            table-layout: fixed;
        }

        .ndt-inspection-table th {
            font-size: 13px;
            white-space: nowrap;
            vertical-align: middle;
        }

        .ndt-inspection-table td {
            vertical-align: top;
            padding: 8px;
        }

        .ndt-inspection-table .form-control,
        .ndt-inspection-table .form-select {
            min-height: 38px;
            border-radius: 6px;
        }

        .ndt-col-description {
            width: 260px;
        }

        .ndt-col-code {
            width: 110px;
        }

        .ndt-col-id {
            width: 120px;
        }

        .ndt-col-size {
            width: 105px;
        }

        .ndt-col-result {
            width: 115px;
        }

        .ndt-col-remark {
            width: 190px;
        }

        .ndt-col-action {
            width: 86px;
        }

        .ndt-photo-table {
            min-width: 980px;
            table-layout: fixed;
        }

        .ndt-photo-upload {
            display: block;
            cursor: pointer;
        }

        .ndt-photo-preview {
            width: 100%;
            aspect-ratio: 4 / 3;
            min-height: 120px;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            background: #f8fafc;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            text-align: center;
            padding: 8px;
        }

        .ndt-photo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .ndt-photo-input {
            margin-top: 8px;
        }

        .ndt-sketch-board {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #f8fafc;
            padding: 10px;
            height: 100%;
        }

        .ndt-sketch-canvas {
            position: relative;
            min-height: 220px;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            background: #fff;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ndt-sketch-image-wrap {
            position: relative;
            display: inline-block;
            max-width: 100%;
        }

        .ndt-sketch-image-wrap img {
            max-width: 100%;
            max-height: 260px;
            object-fit: contain;
            display: block;
        }

        .ndt-sketch-marker {
            position: absolute;
            transform: translate(-50%, -50%);
            display: flex;
            align-items: center;
            gap: 4px;
            max-width: 180px;
            z-index: 2;
        }

        .ndt-sketch-marker-dot {
            width: 12px;
            height: 12px;
            border-radius: 999px;
            background: #dc2626;
            border: 2px solid #fff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .35);
            flex: 0 0 auto;
        }

        .ndt-sketch-marker-label {
            border: 1px solid #dc2626;
            border-radius: 6px;
            background: #fff;
            color: #991b1b;
            font-size: 12px;
            line-height: 1.2;
            padding: 3px 6px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .15);
            word-break: break-word;
        }

        .ndt-sketch-canvas.is-marking {
            cursor: crosshair;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
        }

        .ndt-certificate-option {
            height: 100%;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 12px;
            background: #fff;
        }

        .ndt-certificate-option .form-check-input {
            margin-top: 3px;
        }

        @media (max-width: 767.98px) {
            #formNdtReport {
                padding-bottom: 92px;
            }

            .ndt-section {
                border-radius: 8px;
            }

            .ndt-section .card-header {
                align-items: flex-start !important;
                gap: 10px;
                flex-wrap: wrap;
            }

            .ndt-section .card-header strong {
                line-height: 1.35;
            }

            .ndt-section .card-header .btn {
                width: 100%;
            }

            .ndt-table-scroll {
                overflow-x: visible;
            }

            .ndt-inspection-table,
            .ndt-photo-table,
            .ndt-material-table {
                min-width: 0;
                border: 0;
                table-layout: auto;
            }

            .ndt-inspection-table thead,
            .ndt-photo-table thead,
            .ndt-material-table thead {
                display: none;
            }

            .ndt-inspection-table tbody,
            .ndt-inspection-table tr,
            .ndt-inspection-table td,
            .ndt-photo-table tbody,
            .ndt-photo-table tr,
            .ndt-photo-table td,
            .ndt-material-table tbody,
            .ndt-material-table tr,
            .ndt-material-table td {
                display: block;
                width: 100%;
            }

            .ndt-inspection-table tbody,
            .ndt-photo-table tbody,
            .ndt-material-table tbody {
                padding: 12px;
            }

            .ndt-inspection-table tr,
            .ndt-photo-table tr,
            .ndt-material-table tr {
                margin: 0 12px 14px;
                padding: 12px;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                background: #fff;
            }

            .ndt-inspection-table td,
            .ndt-photo-table td,
            .ndt-material-table td {
                border: 0 !important;
                padding: 6px 0 !important;
            }

            .ndt-inspection-table td::before,
            .ndt-photo-table td::before,
            .ndt-material-table td::before {
                content: attr(data-label);
                display: block;
                margin-bottom: 5px;
                color: #4b5563;
                font-size: 12px;
                font-weight: 700;
            }

            .ndt-inspection-table td[data-label="Aksi"]::before,
            .ndt-photo-table td:first-child::before {
                display: none;
            }

            .ndt-inspection-table .form-control,
            .ndt-inspection-table .form-select {
                min-height: 44px;
            }

            .ndt-inspection-table .btn-remove-inspection-row {
                width: 100%;
                min-height: 42px;
            }

            .ndt-photo-table td:first-child {
                margin-bottom: 8px;
                padding-bottom: 10px !important;
                border-bottom: 1px solid #e5e7eb !important;
                font-size: 15px;
            }

            .ndt-photo-preview {
                min-height: 180px;
            }

            .ndt-photo-input {
                min-height: 44px;
            }

            .ndt-sketch-board > .d-flex {
                align-items: stretch !important;
                flex-direction: column;
            }

            .ndt-sketch-board > .d-flex > .d-flex {
                width: 100%;
            }

            .ndt-sketch-board .btn {
                flex: 1 1 0;
                min-height: 42px;
            }

            .ndt-sketch-canvas {
                min-height: 280px;
            }

            .ndt-sketch-image-wrap img {
                max-height: 320px;
            }

            .ndt-certificate-option {
                min-height: 112px;
            }

            .ndt-action-card {
                position: fixed;
                right: 0;
                bottom: 0;
                left: 0;
                z-index: 1030;
                margin-bottom: 0 !important;
                border-right: 0;
                border-bottom: 0;
                border-left: 0;
                border-radius: 0;
                box-shadow: 0 -8px 24px rgba(15, 23, 42, .12);
            }

            .ndt-action-card .card-body {
                display: grid !important;
                grid-template-columns: 1fr 1fr;
                gap: 8px !important;
                padding: 10px 12px;
            }

            .ndt-action-card .btn {
                min-height: 44px;
                width: 100%;
            }

            .ndt-action-card a.btn {
                grid-column: 1 / -1;
            }
        }
    </style>
@endpush

<form id="formNdtReport" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="service_code" value="{{ $serviceCode }}">
    <input type="hidden" name="service_sequence" value="{{ $serviceSequence }}">

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $serviceCode }} Report</h5>
            <span class="badge bg-primary">{{ $reportNoPreview }}</span>
        </div>
    </div>

    <div class="card mb-3 ndt-section">
        <div class="card-header">
            <strong>Ambil Referensi Report Submitted</strong>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">Report Referensi</label>
                    <select id="ndtReferenceSelect" class="form-select" @disabled(($ndtReferenceReports ?? collect())->isEmpty())>
                        <option value="">- Pilih report submitted -</option>
                        @foreach (($ndtReferenceReports ?? collect()) as $referenceReport)
                            @php
                                $referenceProject = $referenceReport->laporanPekerjaan?->proyek;
                                $referenceClient = $referenceProject?->permohonan?->nama_perusahaan;
                                $referenceDate = optional($referenceReport->laporanPekerjaan?->tanggal_pelaksanaan)->format('d/m/Y');
                            @endphp
                            <option value="{{ $referenceReport->id }}">
                                {{ $referenceReport->report_no }}
                                {{ $referenceProject?->no_proyek ? '- ' . $referenceProject->no_proyek : '' }}
                                {{ $referenceClient ? '- ' . $referenceClient : '' }}
                                {{ $referenceDate ? '- ' . $referenceDate : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="button" id="btnApplyNdtReference" class="btn btn-outline-primary w-100"
                        @disabled(($ndtReferenceReports ?? collect())->isEmpty())>
                        Terapkan Referensi
                    </button>
                </div>
                <div class="col-12">
                    <small class="text-muted">
                        Referensi akan mengisi certificate, material, condition, technique, inspection result, sketch marker, dan approval.
                        Report No, data project, tanggal inspection, dan foto dokumentasi tidak ikut disalin.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3 ndt-section">
        <div class="card-header">
            <strong>Certificate Selection</strong>
        </div>
        <div class="card-body">
            @if ($ndtMasterData['certificates']->isEmpty())
                <div class="text-muted">Belum ada master sertifikat aktif.</div>
            @else
                <div class="row g-3">
                    @foreach ($ndtMasterData['certificates'] as $certificate)
                        <div class="col-md-6 col-xl-4">
                            <label class="ndt-certificate-option">
                                <div class="form-check">
                                    <input type="checkbox" name="certificates[]" value="{{ $certificate->id }}"
                                        class="form-check-input" @checked(in_array((int) $certificate->id, $selectedCertificateIds, true))>
                                    <span class="form-check-label">
                                        <span class="fw-semibold d-block">{{ $certificate->title }}</span>
                                        <span class="text-muted small d-block">No: {{ $certificate->certificate_no ?? '-' }}</span>
                                        <span class="text-muted small d-block">Type: {{ $certificate->type ?? '-' }}</span>
                                        <span class="text-muted small d-block">
                                            Valid:
                                            {{ optional($certificate->issued_at)->format('d/m/Y') ?? '-' }}
                                            -
                                            {{ optional($certificate->expired_at)->format('d/m/Y') ?? '-' }}
                                        </span>
                                        @if ($certificate->url)
                                            <a href="{{ $certificate->url }}" target="_blank" class="small">Lihat file</a>
                                        @endif
                                    </span>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="card mb-3 ndt-section">
        <div class="card-header">
            <strong>Section 1 - Project Data</strong>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Report No</label>
                    <input type="text" class="form-control" name="report_no" value="{{ $reportNoPreview }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Client</label>
                    <input type="text" class="form-control" value="{{ $proyek->permohonan->nama_perusahaan ?? '-' }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Location</label>
                    <input type="text" class="form-control" value="{{ $proyek->permohonan->lokasi ?? '-' }}" readonly>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Project Name</label>
                    <input type="text" class="form-control" value="{{ $proyek->permohonan->nama_proyek ?? '-' }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-control" name="tanggal_pelaksanaan"
                        value="{{ optional($laporan?->tanggal_pelaksanaan)->format('Y-m-d') }}">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3 ndt-section">
        <div class="card-header">
            <strong>Section 2 - Material Identification</strong>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Part No</label>
                    <input type="text" name="part_no" class="form-control" value="{{ $ndtReport->part_no ?? '' }}">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" value="{{ $ndtReport->description ?? $item->detail_pekerjaan }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Material</label>
                    <input type="text" name="material" class="form-control" value="{{ $ndtReport->material ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Temp</label>
                    <input type="text" name="temperature" class="form-control" value="{{ $ndtReport->temperature ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Procedure No</label>
                    <select name="procedure_id" class="form-select">
                        <option value="">- Pilih -</option>
                        @foreach ($ndtMasterData['procedures'] as $procedure)
                            <option value="{{ $procedure->id }}" @selected(($ndtReport->procedure_id ?? null) === $procedure->id)>
                                {{ $procedure->code }} {{ $procedure->name ? '- ' . $procedure->name : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Criteria</label>
                    <select name="criteria_id" class="form-select">
                        <option value="">- Pilih -</option>
                        @foreach ($ndtMasterData['criteria'] as $criteria)
                            <option value="{{ $criteria->id }}" @selected(($ndtReport->criteria_id ?? null) === $criteria->id)>
                                {{ $criteria->code }} {{ $criteria->name ? '- ' . $criteria->name : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Testing Standard</label>
                    <select name="testing_standard_id" class="form-select">
                        <option value="">- Pilih -</option>
                        @foreach ($ndtMasterData['standards'] as $standard)
                            <option value="{{ $standard->id }}" @selected(($ndtReport->testing_standard_id ?? null) === $standard->id)>
                                {{ $standard->code }} {{ $standard->name ? '- ' . $standard->name : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3 ndt-section">
        <div class="card-header">
            <strong>Section 3 - Condition</strong>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12">
                    <div class="ndt-condition-line">
                        <div class="ndt-condition-label">SURFACE</div>
                        <div class="ndt-condition-separator">:</div>
                        <div class="ndt-condition-options">
                            @foreach ($surfaceOptions as $value => $label)
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="surface_conditions[]" value="{{ $value }}"
                                        @checked(in_array($value, $surfaceConditions, true))>
                                    <span class="form-check-label">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="ndt-condition-line">
                        <div class="ndt-condition-label">PREPARATION</div>
                        <div class="ndt-condition-separator">:</div>
                        <div class="ndt-condition-options">
                            @foreach ($preparationOptions as $value => $label)
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="preparation_conditions[]" value="{{ $value }}"
                                        @checked(in_array($value, $preparationConditions, true))>
                                    <span class="form-check-label">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label d-block">Stage Of</label>
                    @foreach ($stageOfOptions as $value => $label)
                        <label class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="stage_ofs[]" value="{{ $value }}"
                                @checked(in_array($value, $stageOfs, true))>
                            <span class="form-check-label">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="col-md-6">
                    <label class="form-label d-block">Examination</label>
                    @foreach ($examinationOptions as $value => $label)
                        <label class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="examinations[]" value="{{ $value }}"
                                @checked(in_array($value, $examinations, true))>
                            <span class="form-check-label">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3 ndt-section">
        <div class="card-header">
            <strong>Section 4 - Technique / Type / Materials</strong>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label d-block">Technique</label>
                    @foreach ($techniqueOptions as $value => $label)
                        <label class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="techniques[]" value="{{ $value }}"
                                @checked(in_array($value, $techniques, true))>
                            <span class="form-check-label">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="col-md-6">
                    <label class="form-label d-block">Type</label>
                    @foreach ($testTypeOptions as $value => $label)
                        <label class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="test_types[]" value="{{ $value }}"
                                @checked(in_array($value, $testTypes, true))>
                            <span class="form-check-label">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="col-md-6">
                    <label class="form-label d-block">Penetrant Application</label>
                    @foreach ($penetrantApplicationOptions as $value => $label)
                        <label class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="penetrant_applications[]" value="{{ $value }}"
                                @checked(in_array($value, $penetrantApplications, true))>
                            <span class="form-check-label">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="col-12">
                    <label class="form-label">Materials</label>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0 ndt-material-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 30%;">Material</th>
                                    <th>Trade Name</th>
                                    <th>Batch Number</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($materialOptions as $value => $label)
                                    <tr>
                                        <td class="fw-semibold" data-label="Material">{{ $label }}</td>
                                        <td data-label="Trade Name">
                                            <input type="text" name="material_rows[{{ $value }}][trade_name]"
                                                class="form-control"
                                                value="{{ $materialRows[$value]['trade_name'] ?? '' }}">
                                        </td>
                                        <td data-label="Batch Number">
                                            <input type="text" name="material_rows[{{ $value }}][batch_number]"
                                                class="form-control"
                                                value="{{ $materialRows[$value]['batch_number'] ?? '' }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($serviceCode !== 'MT')
                    <div class="col-md-4">
                        <label class="form-label">Dwell Time - Penetrant</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" name="dwell_times[penetrant_minutes]" class="form-control"
                                value="{{ $dwellTimes['penetrant_minutes'] ?? '' }}">
                            <span class="input-group-text">minutes</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Dwell Time - Developer</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" name="dwell_times[developer_minutes]" class="form-control"
                                value="{{ $dwellTimes['developer_minutes'] ?? '' }}">
                            <span class="input-group-text">minutes</span>
                        </div>
                    </div>
                @endif
                <div class="col-md-4">
                    <label class="form-label">Blacklight Intensity</label>
                    <div class="input-group">
                        <input type="text" name="blacklight_intensity" class="form-control" value="{{ $ndtReport->blacklight_intensity ?? '' }}">
                        <span class="input-group-text">mW/cm&sup2;</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3 ndt-section">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Section 5 - Inspection Result</strong>
            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddInspectionRow">Tambah Item</button>
        </div>
        <div class="card-body p-0">
            <div class="ndt-table-scroll">
                <table class="table table-bordered align-middle mb-0 ndt-inspection-table" id="inspectionResultTable">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2" class="ndt-col-description">Description</th>
                            <th rowspan="2" class="ndt-col-code">Code</th>
                            <th rowspan="2" class="ndt-col-id">ID No</th>
                            <th colspan="3" class="text-center">Size (mm)</th>
                            <th rowspan="2" class="ndt-col-result">Result</th>
                            <th rowspan="2" class="ndt-col-remark">Remark</th>
                            <th rowspan="2" class="ndt-col-action">Aksi</th>
                        </tr>
                        <tr>
                            <th class="ndt-col-size">Diameter</th>
                            <th class="ndt-col-size">Panjang</th>
                            <th class="ndt-col-size">Tebal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inspectionRows as $index => $inspection)
                            <tr class="inspection-row" data-row="{{ $index }}">
                                <td data-label="Description">
                                    <input type="hidden" name="inspection_items[{{ $index }}][id]" class="inspection-item-id" value="{{ $inspection->id ?? '' }}">
                                    <input type="hidden" name="inspection_items[{{ $index }}][sketch_annotations]" class="inspection-sketch-annotations" value="{{ e(json_encode($inspection?->sketch_annotations ?? [])) }}">
                                    <select name="inspection_items[{{ $index }}][description_master_id]" class="form-select inspection-description-select">
                                        <option value="">- Pilih -</option>
                                        @foreach ($ndtMasterData['descriptions'] as $description)
                                            <option value="{{ $description->id }}" data-sketch="{{ $description->sketch_url }}"
                                                @selected(($inspection->description_master_id ?? null) === $description->id)>
                                                {{ $description->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td data-label="Code"><input type="text" name="inspection_items[{{ $index }}][code]" class="form-control" value="{{ $inspection->code ?? '' }}"></td>
                                <td data-label="ID No"><input type="text" name="inspection_items[{{ $index }}][id_no]" class="form-control" value="{{ $inspection->id_no ?? '' }}"></td>
                                <td data-label="Diameter (mm)"><input type="number" step="0.01" name="inspection_items[{{ $index }}][diameter_mm]" class="form-control" value="{{ $inspection->diameter_mm ?? '' }}"></td>
                                <td data-label="Panjang (mm)"><input type="number" step="0.01" name="inspection_items[{{ $index }}][length_mm]" class="form-control" value="{{ $inspection->length_mm ?? '' }}"></td>
                                <td data-label="Tebal (mm)"><input type="number" step="0.01" name="inspection_items[{{ $index }}][thickness_mm]" class="form-control" value="{{ $inspection->thickness_mm ?? '' }}"></td>
                                <td data-label="Result">
                                    <select name="inspection_items[{{ $index }}][result]" class="form-select">
                                        <option value="">-</option>
                                        <option value="acc" @selected(($inspection->result ?? '') === 'acc')>ACC</option>
                                        <option value="reject" @selected(($inspection->result ?? '') === 'reject')>REJECT</option>
                                        <option value="repair" @selected(($inspection->result ?? '') === 'repair')>REPAIR</option>
                                    </select>
                                </td>
                                <td data-label="Remark"><input type="text" name="inspection_items[{{ $index }}][remark]" class="form-control inspection-remark-input" value="{{ $inspection->remark ?? '' }}"></td>
                                <td data-label="Aksi"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-inspection-row">Hapus</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-3 ndt-section">
        <div class="card-header">
            <strong>Section 6 - Sketch</strong>
        </div>
        <div class="card-body">
            <div id="sketchPreviewList" class="row g-3"></div>
        </div>
    </div>

    <div class="card mb-3 ndt-section">
        <div class="card-header">
            <strong>Section 7 - Photo Documentation Area</strong>
        </div>
        <div class="card-body border-bottom">
            <small class="text-muted">Pilih description di Section 5 terlebih dahulu, lalu upload foto. Semua preview foto ditampilkan dalam ukuran yang sama.</small>
        </div>
        <div class="card-body p-0">
            <div class="ndt-table-scroll">
                <table class="table table-bordered align-middle mb-0 ndt-photo-table" id="photoDocumentationTable">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Before</th>
                            <th>During</th>
                            <th>After</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-3 ndt-section">
        <div class="card-header">
            <strong>Section 8 - Approval</strong>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach ($ndtMasterData['approval_roles'] as $role => $label)
                    @php $selectedName = $ndtReport?->{$role . '_name'}; @endphp
                    <div class="col-md-6">
                        <label class="form-label">{{ $label }}</label>
                        <select name="approvals[{{ $role }}]" class="form-select">
                            <option value="">- Pilih -</option>
                            @foreach (($ndtMasterData['approvals'][$role] ?? collect()) as $person)
                                <option value="{{ $person->id }}" data-position="{{ $person->position }}"
                                    data-name="{{ $person->name }}"
                                    @selected($selectedName === $person->name)>
                                    {{ $person->name }} {{ $person->position ? '- ' . $person->position : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card mb-3 ndt-action-card">
        <div class="card-body d-flex justify-content-end gap-2">
            @if ($ndtReport)
                <a href="{{ route('proyek.pekerjaan.ndt.pdf', [$proyek->id, $item->id, $layanan->id]) }}"
                    class="btn btn-outline-primary" target="_blank">
                    Lihat PDF
                </a>
            @endif
            <button type="button" class="btn btn-secondary" id="btnNdtDraft">Simpan Draft</button>
            <button type="button" class="btn btn-primary" id="btnNdtSubmit">Submit Report</button>
        </div>
    </div>
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tableBody = document.querySelector('#inspectionResultTable tbody');
            const photoBody = document.querySelector('#photoDocumentationTable tbody');
            const sketchList = document.getElementById('sketchPreviewList');
            const form = document.getElementById('formNdtReport');
            const btnDraft = document.getElementById('btnNdtDraft');
            const btnSubmit = document.getElementById('btnNdtSubmit');
            const referenceSelect = document.getElementById('ndtReferenceSelect');
            const btnApplyReference = document.getElementById('btnApplyNdtReference');
            const saveUrl = {!! json_encode($ndtSaveUrl) !!};
            const updateUrl = {!! json_encode($ndtUpdateUrl) !!};
            const showUrl = {!! json_encode($ndtShowUrl) !!};
            const referenceUrlTemplate = {!! json_encode($ndtReferenceUrlTemplate) !!};
            const hasReport = @json((bool) $laporan);
            const existingPhotos = @json($existingPhotos);
            const descriptionOptionsHtml = `{!! $ndtMasterData['descriptions']->map(fn ($description) => '<option value="' . $description->id . '" data-sketch="' . e($description->sketch_url) . '">' . e($description->name) . '</option>')->implode('') !!}`;
            let markingRowIndex = null;

            function escapeHtml(value) {
                const div = document.createElement('div');
                div.textContent = value ?? '';
                return div.innerHTML;
            }

            function buildInspectionRow(index) {
                return `
                    <tr class="inspection-row" data-row="${index}">
                        <td data-label="Description">
                            <input type="hidden" name="inspection_items[${index}][id]" class="inspection-item-id" value="">
                            <input type="hidden" name="inspection_items[${index}][sketch_annotations]" class="inspection-sketch-annotations" value="[]">
                            <select name="inspection_items[${index}][description_master_id]" class="form-select inspection-description-select">
                                <option value="">- Pilih -</option>
                                ${descriptionOptionsHtml}
                            </select>
                        </td>
                        <td data-label="Code"><input type="text" name="inspection_items[${index}][code]" class="form-control"></td>
                        <td data-label="ID No"><input type="text" name="inspection_items[${index}][id_no]" class="form-control"></td>
                        <td data-label="Diameter (mm)"><input type="number" step="0.01" name="inspection_items[${index}][diameter_mm]" class="form-control"></td>
                        <td data-label="Panjang (mm)"><input type="number" step="0.01" name="inspection_items[${index}][length_mm]" class="form-control"></td>
                        <td data-label="Tebal (mm)"><input type="number" step="0.01" name="inspection_items[${index}][thickness_mm]" class="form-control"></td>
                        <td data-label="Result">
                            <select name="inspection_items[${index}][result]" class="form-select">
                                <option value="">-</option>
                                <option value="acc">ACC</option>
                                <option value="reject">REJECT</option>
                                <option value="repair">REPAIR</option>
                            </select>
                        </td>
                        <td data-label="Remark"><input type="text" name="inspection_items[${index}][remark]" class="form-control inspection-remark-input"></td>
                        <td data-label="Aksi"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-inspection-row">Hapus</button></td>
                    </tr>
                `;
            }

            function buildPhotoUpload(rowIndex, type, label, itemId) {
                const inputId = `photo-${rowIndex}-${type}`;
                const existingUrl = itemId && existingPhotos[itemId] ? existingPhotos[itemId][type] : null;
                const previewContent = existingUrl
                    ? `<img src="${existingUrl}" alt="${label}">`
                    : `<span>${label}<br><small>Klik untuk upload</small></span>`;

                return `
                    <label class="ndt-photo-upload" for="${inputId}">
                        <div class="ndt-photo-preview" data-preview-for="${inputId}">
                            ${previewContent}
                        </div>
                    </label>
                    <input type="file" id="${inputId}" name="photos[${rowIndex}][${type}]"
                        class="form-control ndt-photo-input" accept="image/*">
                `;
            }

            function updatePhotoPreview(input) {
                const preview = document.querySelector(`[data-preview-for="${input.id}"]`);
                if (!preview || !input.files || !input.files[0]) {
                    return;
                }

                const file = input.files[0];
                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.innerHTML = `<img src="${event.target.result}" alt="${file.name}">`;
                };
                reader.readAsDataURL(file);
            }

            function getInspectionRow(rowIndex) {
                return [...document.querySelectorAll('.inspection-row')]
                    .find(row => row.dataset.row === String(rowIndex));
            }

            function getAnnotationInput(rowIndex) {
                return getInspectionRow(rowIndex)?.querySelector('.inspection-sketch-annotations');
            }

            function getSketchAnnotations(rowIndex) {
                const input = getAnnotationInput(rowIndex);
                if (!input || !input.value) {
                    return [];
                }

                try {
                    const parsed = JSON.parse(input.value);
                    return Array.isArray(parsed) ? parsed : [];
                } catch (error) {
                    return [];
                }
            }

            function setSketchAnnotations(rowIndex, annotations) {
                const input = getAnnotationInput(rowIndex);
                if (input) {
                    input.value = JSON.stringify(annotations);
                }
            }

            function buildMarkerDefaultText(row) {
                const valueOf = (suffix) => row.querySelector(`input[name$="[${suffix}]"]`)?.value?.trim();
                const result = row.querySelector('select[name$="[result]"]')?.value;
                const parts = [];

                if (valueOf('diameter_mm')) {
                    parts.push(`Dia ${valueOf('diameter_mm')} mm`);
                }

                if (valueOf('length_mm')) {
                    parts.push(`P ${valueOf('length_mm')} mm`);
                }

                if (valueOf('thickness_mm')) {
                    parts.push(`T ${valueOf('thickness_mm')} mm`);
                }

                if (result) {
                    parts.push(result.toUpperCase());
                }

                return parts.join(' / ');
            }

            function renderSketchMarkers(rowIndex) {
                const wrap = sketchList.querySelector(`[data-sketch-wrap="${rowIndex}"]`);
                if (!wrap) {
                    return;
                }

                wrap.querySelectorAll('.ndt-sketch-marker').forEach(marker => marker.remove());

                getSketchAnnotations(rowIndex).forEach((annotation, index) => {
                    const marker = document.createElement('div');
                    marker.className = 'ndt-sketch-marker';
                    marker.dataset.markerIndex = index;
                    marker.style.left = `${Number(annotation.x) || 0}%`;
                    marker.style.top = `${Number(annotation.y) || 0}%`;
                    marker.innerHTML = `
                        <span class="ndt-sketch-marker-dot"></span>
                        <span class="ndt-sketch-marker-label">${escapeHtml(annotation.text || '')}</span>
                    `;
                    wrap.appendChild(marker);
                });
            }

            function syncSketchMarkingState() {
                sketchList.querySelectorAll('.ndt-sketch-canvas').forEach(canvas => {
                    canvas.classList.toggle('is-marking', canvas.dataset.rowIndex === String(markingRowIndex));
                });
            }

            function addSketchMarker(event, wrap) {
                const rowIndex = wrap.dataset.sketchWrap;
                if (String(markingRowIndex) !== String(rowIndex)) {
                    return;
                }

                const rect = wrap.getBoundingClientRect();
                if (!rect.width || !rect.height) {
                    return;
                }

                const x = ((event.clientX - rect.left) / rect.width) * 100;
                const y = ((event.clientY - rect.top) / rect.height) * 100;
                if (x < 0 || x > 100 || y < 0 || y > 100) {
                    return;
                }

                const row = getInspectionRow(rowIndex);
                const defaultText = row ? buildMarkerDefaultText(row) : '';
                const label = window.prompt('Label marker sketch', defaultText || 'Marking');
                if (label === null || !label.trim()) {
                    markingRowIndex = null;
                    syncSketchMarkingState();
                    return;
                }

                const annotations = getSketchAnnotations(rowIndex);
                annotations.push({
                    x: Math.round(x * 100) / 100,
                    y: Math.round(y * 100) / 100,
                    text: label.trim(),
                });

                setSketchAnnotations(rowIndex, annotations);
                markingRowIndex = null;
                syncSketchMarkingState();
                renderSketchMarkers(rowIndex);
            }

            function refreshDerivedSections() {
                const rows = [...document.querySelectorAll('.inspection-row')];
                photoBody.innerHTML = '';
                sketchList.innerHTML = '';

                rows.forEach((row, displayIndex) => {
                    const rowIndex = row.dataset.row;
                    const itemId = row.querySelector('.inspection-item-id')?.value;
                    const select = row.querySelector('.inspection-description-select');
                    const selectedOption = select?.selectedOptions?.[0];
                    const itemLabel = selectedOption && selectedOption.value ? selectedOption.textContent.trim() : `Item ${displayIndex + 1}`;
                    const sketchUrl = selectedOption?.dataset?.sketch;
                    const safeItemLabel = escapeHtml(itemLabel);
                    const safeSketchUrl = escapeHtml(sketchUrl || '');

                    photoBody.insertAdjacentHTML('beforeend', `
                        <tr>
                            <td class="fw-semibold" data-label="Item">${safeItemLabel}</td>
                            <td data-label="Before">${buildPhotoUpload(rowIndex, 'before', 'Before', itemId)}</td>
                            <td data-label="During">${buildPhotoUpload(rowIndex, 'during', 'During', itemId)}</td>
                            <td data-label="After">${buildPhotoUpload(rowIndex, 'after', 'After', itemId)}</td>
                        </tr>
                    `);

                    sketchList.insertAdjacentHTML('beforeend', `
                        <div class="col-md-6">
                            <div class="ndt-sketch-board">
                                <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                                    <div class="fw-semibold">${safeItemLabel}</div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-add-sketch-marker" data-row-index="${rowIndex}" ${sketchUrl ? '' : 'disabled'}>
                                            Tambah Marker
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-reset-sketch-marker" data-row-index="${rowIndex}">
                                            Reset
                                        </button>
                                    </div>
                                </div>
                                ${sketchUrl
                                    ? `<div class="ndt-sketch-canvas" data-row-index="${rowIndex}">
                                            <div class="ndt-sketch-image-wrap" data-sketch-wrap="${rowIndex}">
                                                <img src="${safeSketchUrl}" alt="${safeItemLabel}">
                                            </div>
                                        </div>`
                                    : `<div class="ndt-sketch-canvas text-muted">Sketch belum dipilih</div>`}
                                <small class="text-muted d-block mt-2">Klik Tambah Marker, lalu klik posisi di sketch untuk menaruh ukuran atau catatan.</small>
                            </div>
                        </div>
                    `);
                    renderSketchMarkers(rowIndex);
                });

                syncSketchMarkingState();
            }

            function nextRowIndex() {
                const indexes = [...document.querySelectorAll('.inspection-row')]
                    .map(row => Number(row.dataset.row))
                    .filter(index => !Number.isNaN(index));

                return indexes.length ? Math.max(...indexes) + 1 : 0;
            }

            function syncRemarkByResult(row) {
                const resultSelect = row.querySelector('select[name$="[result]"]');
                const remarkInput = row.querySelector('.inspection-remark-input');
                if (!resultSelect || !remarkInput) {
                    return;
                }

                if (resultSelect.value === 'acc') {
                    remarkInput.value = 'No relevant indication was detected during time of tested';
                    remarkInput.readOnly = true;
                    remarkInput.classList.add('bg-light');
                    return;
                }

                remarkInput.readOnly = false;
                remarkInput.classList.remove('bg-light');
            }

            function syncAllRemarks() {
                document.querySelectorAll('.inspection-row').forEach(syncRemarkByResult);
            }

            function setSubmitLoading(isLoading) {
                btnDraft.disabled = isLoading;
                btnSubmit.disabled = isLoading;
                btnDraft.textContent = isLoading ? 'Menyimpan...' : 'Simpan Draft';
                btnSubmit.textContent = isLoading ? 'Menyimpan...' : 'Submit Report';
            }

            function setReferenceLoading(isLoading) {
                if (referenceSelect) {
                    referenceSelect.disabled = isLoading;
                }

                if (btnApplyReference) {
                    btnApplyReference.disabled = isLoading;
                    btnApplyReference.textContent = isLoading ? 'Mengambil...' : 'Terapkan Referensi';
                }
            }

            function controlsByName(name) {
                return [...form.querySelectorAll('[name]')].filter(control => control.name === name);
            }

            function setFieldValue(name, value) {
                const control = controlsByName(name)[0];
                if (!control) {
                    return;
                }

                control.value = value ?? '';
            }

            function setCheckboxGroup(name, values) {
                const selectedValues = new Set((values || []).map(String));
                controlsByName(name).forEach(control => {
                    control.checked = selectedValues.has(control.value);
                });
            }

            function setCertificates(certificateIds) {
                const selectedIds = new Set((certificateIds || []).map(id => String(id)));
                controlsByName('certificates[]').forEach(control => {
                    control.checked = selectedIds.has(control.value);
                });
            }

            function setMaterialRows(materialRows) {
                Object.entries(materialRows || {}).forEach(([key, row]) => {
                    setFieldValue(`material_rows[${key}][trade_name]`, row?.trade_name ?? '');
                    setFieldValue(`material_rows[${key}][batch_number]`, row?.batch_number ?? '');
                });
            }

            function setDwellTimes(dwellTimes) {
                Object.entries(dwellTimes || {}).forEach(([key, value]) => {
                    setFieldValue(`dwell_times[${key}]`, value);
                });
            }

            function setApprovals(approvals) {
                Object.entries(approvals || {}).forEach(([role, approval]) => {
                    const select = controlsByName(`approvals[${role}]`)[0];
                    if (!select) {
                        return;
                    }

                    if (approval?.id) {
                        select.value = String(approval.id);
                        return;
                    }

                    const option = [...select.options].find(candidate => {
                        if (!approval?.name) {
                            return false;
                        }

                        if (candidate.dataset.name !== approval.name) {
                            return false;
                        }

                        return !approval.position || candidate.dataset.position === approval.position;
                    });

                    select.value = option?.value ?? '';
                });
            }

            function applyInspectionItems(inspectionItems) {
                const rows = inspectionItems && inspectionItems.length ? inspectionItems : [{}];
                tableBody.innerHTML = '';

                rows.forEach((item, index) => {
                    tableBody.insertAdjacentHTML('beforeend', buildInspectionRow(index));
                    const row = getInspectionRow(index);
                    if (!row) {
                        return;
                    }

                    row.querySelector('.inspection-item-id').value = '';
                    row.querySelector('.inspection-sketch-annotations').value = JSON.stringify(item.sketch_annotations || []);
                    row.querySelector(`select[name="inspection_items[${index}][description_master_id]"]`).value = item.description_master_id ?? '';
                    row.querySelector(`input[name="inspection_items[${index}][code]"]`).value = item.code ?? '';
                    row.querySelector(`input[name="inspection_items[${index}][id_no]"]`).value = item.id_no ?? '';
                    row.querySelector(`input[name="inspection_items[${index}][diameter_mm]"]`).value = item.diameter_mm ?? '';
                    row.querySelector(`input[name="inspection_items[${index}][length_mm]"]`).value = item.length_mm ?? '';
                    row.querySelector(`input[name="inspection_items[${index}][thickness_mm]"]`).value = item.thickness_mm ?? '';
                    row.querySelector(`select[name="inspection_items[${index}][result]"]`).value = item.result ?? '';
                    row.querySelector(`input[name="inspection_items[${index}][remark]"]`).value = item.remark ?? '';
                    syncRemarkByResult(row);
                });

                refreshDerivedSections();
            }

            function applyReferenceData(reference) {
                const fields = reference.fields || {};

                setCertificates(reference.certificate_ids || []);
                setFieldValue('part_no', fields.part_no);
                setFieldValue('description', fields.description);
                setFieldValue('material', fields.material);
                setFieldValue('temperature', fields.temperature);
                setFieldValue('procedure_id', fields.procedure_id);
                setFieldValue('criteria_id', fields.criteria_id);
                setFieldValue('testing_standard_id', fields.testing_standard_id);
                setCheckboxGroup('surface_conditions[]', fields.surface_conditions);
                setCheckboxGroup('preparation_conditions[]', fields.preparation_conditions);
                setCheckboxGroup('stage_ofs[]', fields.stage_ofs);
                setCheckboxGroup('examinations[]', fields.examinations);
                setCheckboxGroup('techniques[]', fields.techniques);
                setCheckboxGroup('test_types[]', fields.test_types);
                setCheckboxGroup('penetrant_applications[]', fields.penetrant_applications);
                setMaterialRows(fields.material_rows);
                setDwellTimes(fields.dwell_times);
                setFieldValue('blacklight_intensity', fields.blacklight_intensity);
                setApprovals(fields.approvals);
                applyInspectionItems(reference.inspection_items || []);
                syncAllRemarks();
            }

            async function fetchAndApplyReference() {
                const referenceId = referenceSelect?.value;
                if (!referenceId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih report',
                        text: 'Pilih report submitted yang ingin dijadikan referensi terlebih dahulu.',
                    });
                    return;
                }

                const confirmation = await Swal.fire({
                    icon: 'question',
                    title: 'Terapkan referensi?',
                    text: 'Data form yang sedang terisi akan ditimpa oleh report referensi. Report No, data project, tanggal, dan foto tidak ikut disalin.',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, terapkan',
                    cancelButtonText: 'Batal',
                });

                if (!confirmation.isConfirmed) {
                    return;
                }

                setReferenceLoading(true);

                try {
                    const response = await fetch(referenceUrlTemplate.replace('__REFERENCE__', referenceId), {
                        headers: {
                            'Accept': 'application/json',
                        },
                    });
                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Report referensi tidak bisa diambil.');
                    }

                    applyReferenceData(data);

                    Swal.fire({
                        icon: 'success',
                        title: 'Referensi diterapkan',
                        text: `Data dari ${data.report_no} sudah masuk ke form.`,
                        timer: 1600,
                        showConfirmButton: false,
                    });
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: error.message || 'Terjadi kesalahan saat mengambil report referensi.',
                    });
                } finally {
                    setReferenceLoading(false);
                }
            }

            async function submitNdtReport(action) {
                const formData = new FormData(form);
                formData.set('action', action);

                let url = saveUrl;
                if (hasReport) {
                    url = updateUrl;
                    formData.set('_method', 'PATCH');
                }

                setSubmitLoading(true);

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                        },
                        body: formData,
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        const errors = data.errors
                            ? Object.values(data.errors).flat().join('<br>')
                            : data.message || 'Report NDT gagal disimpan.';

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            html: errors,
                        });
                        return;
                    }

                    await Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message || 'Report NDT berhasil disimpan.',
                        timer: 1500,
                        showConfirmButton: false,
                    });

                    window.location.href = data.redirect || showUrl;
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan koneksi saat menyimpan report NDT.',
                    });
                } finally {
                    setSubmitLoading(false);
                }
            }

            document.getElementById('btnAddInspectionRow')?.addEventListener('click', function() {
                tableBody.insertAdjacentHTML('beforeend', buildInspectionRow(nextRowIndex()));
                refreshDerivedSections();
            });

            tableBody?.addEventListener('click', function(event) {
                if (!event.target.classList.contains('btn-remove-inspection-row')) {
                    return;
                }

                if (document.querySelectorAll('.inspection-row').length <= 1) {
                    return;
                }

                event.target.closest('tr')?.remove();
                refreshDerivedSections();
            });

            tableBody?.addEventListener('change', function(event) {
                if (event.target.classList.contains('inspection-description-select')) {
                    const row = event.target.closest('.inspection-row');
                    const rowIndex = row?.dataset?.row;

                    if (rowIndex !== undefined) {
                        setSketchAnnotations(rowIndex, []);
                    }

                    refreshDerivedSections();
                }

                if (event.target.matches('select[name$="[result]"]')) {
                    syncRemarkByResult(event.target.closest('.inspection-row'));
                }
            });

            sketchList?.addEventListener('click', function(event) {
                const addButton = event.target.closest('.btn-add-sketch-marker');
                if (addButton) {
                    markingRowIndex = addButton.dataset.rowIndex;
                    syncSketchMarkingState();
                    return;
                }

                const resetButton = event.target.closest('.btn-reset-sketch-marker');
                if (resetButton) {
                    const rowIndex = resetButton.dataset.rowIndex;
                    setSketchAnnotations(rowIndex, []);
                    renderSketchMarkers(rowIndex);

                    if (String(markingRowIndex) === String(rowIndex)) {
                        markingRowIndex = null;
                        syncSketchMarkingState();
                    }
                    return;
                }

                const wrap = event.target.closest('.ndt-sketch-image-wrap');
                if (wrap) {
                    addSketchMarker(event, wrap);
                }
            });

            photoBody?.addEventListener('change', function(event) {
                if (event.target.classList.contains('ndt-photo-input')) {
                    updatePhotoPreview(event.target);
                }
            });

            btnApplyReference?.addEventListener('click', fetchAndApplyReference);

            btnDraft?.addEventListener('click', function() {
                submitNdtReport('draft');
            });

            btnSubmit?.addEventListener('click', function() {
                submitNdtReport('submit');
            });

            refreshDerivedSections();
            syncAllRemarks();
        });
    </script>
@endpush
