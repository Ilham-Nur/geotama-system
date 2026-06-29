@php
    use Illuminate\Support\Facades\Storage;

    $serviceTitle = $ndtReport->service_code === 'MT'
        ? 'Magnetic Particle Testing Report'
        : 'Penetrant Testing Report';

    $surfaceOptions = $ndtReport->service_code === 'MT'
        ? ['as_welded' => 'AS - WELDED', 'as_rolled' => 'AS ROLLED', 'as_forged' => 'AS - FORGED', 'as_cast' => 'AS - CAST']
        : ['as_welded' => 'As Welded', 'machined' => 'Machined', 'ground' => 'Ground', 'painted' => 'Painted'];
    $preparationOptions = $ndtReport->service_code === 'MT'
        ? ['grinding' => 'GRINDING', 'machining' => 'MACHINING', 'recondition' => 'RECONDITION', 'others' => 'OTHERS']
        : ['wire_brush' => 'Wire Brush', 'grinding' => 'Grinding', 'solvent_cleaning' => 'Solvent Cleaning', 'dry' => 'Dry'];
    $stageOfOptions = ['before_pwht' => 'Before PWHT', 'after_pwht' => 'After PWHT', 'before_hydrotest' => 'Before Hydrotest', 'after_hydrotest' => 'After Hydrotest'];
    $examinationOptions = ['after_repair' => 'After Repair', 'after_machining' => 'After Machining', 'other' => 'Other'];
    $techniqueOptions = ['color_contrast' => 'Color Contrast', 'fluorescent' => 'Fluorescent'];
    $testTypeOptions = ['solvent_removable' => 'Solvent Removable', 'post' => 'Post'];
    $penetrantApplicationOptions = ['spraying' => 'Spraying', 'brushing' => 'Brushing'];
    $materialOptions = $ndtReport->service_code === 'MT'
        ? ['magnetic_testing' => 'Magnetic Testing', 'magnetic_spray' => 'Magnetic Spray']
        : ['cleaner' => 'Cleaner', 'penetrant' => 'Penetrant', 'developer' => 'Developer'];

    $approvalRoles = [
        'examiner' => 'Examiner ASNT II',
        'qc_inspector' => 'QC Inspector',
        'owner_representative' => 'Owner Representative',
        'surveyor' => 'Surveyor',
    ];

    $localImage = function (string $path) {
        $fullPath = public_path($path);
        if (!is_file($fullPath)) {
            return null;
        }

        return 'data:' . mime_content_type($fullPath) . ';base64,' . base64_encode(file_get_contents($fullPath));
    };

    $storagePath = function (?string $path) {
        if (!$path || !Storage::disk('public')->exists($path)) {
            return null;
        }

        return Storage::disk('public')->path($path);
    };

    $isImagePath = function (?string $path) {
        return $path && preg_match('/\.(jpe?g|png|webp)$/i', $path);
    };

    $checkBoxes = function (array $options, ?array $selected) {
        $selected = $selected ?? [];

        return collect($options)->map(function ($label, $value) use ($selected) {
            $checked = in_array($value, $selected, true) ? '&#10003;' : '';

            return '<span class="checkbox-item"><span class="pdf-checkbox">' . $checked . '</span>' . e($label) . '</span>';
        })->implode('');
    };

    $formatMaster = function ($master) {
        if (!$master) {
            return '-';
        }

        return trim(($master->code ?? '') . ' ' . (($master->name ?? '') ? '- ' . $master->name : ''));
    };

    $logoBase64 = $localImage('template/assets/images/logo/logo-geotama-removebg-preview.png');
    $headerBase64 = $localImage('template/assets/images/kop_header_geotama.png');
    $footerBase64 = $localImage('template/assets/images/kop_footer_geotama.png');
    $coverTemplateBase64 = $localImage('template/assets/images/ndt-cover-template.png');
    $certificatePages = $ndtReport->certificates->values();
    $certificatePageCount = max(1, $certificatePages->count());
    $mainReportPage = 4 + $certificatePageCount;
    $documentationPage = $mainReportPage + 1;
    $projectCoverTitle = ($ndtReport->part_no || $ndtReport->description)
        ? trim(strtoupper(($ndtReport->part_no ? $ndtReport->part_no . ' ' : '') . ($ndtReport->description ? '( ' . $ndtReport->description . ' )' : '')))
        : strtoupper($proyek->permohonan->nama_proyek ?? '-');
    $sketchGridRows = [];
    $currentSketchRow = [];
    $currentSketchUnits = 0;

    foreach ($ndtReport->inspectionItems as $inspection) {
        $sketch = $inspection->description_master_id ? $sketches->get($inspection->description_master_id) : null;
        $sketchPath = $storagePath($sketch?->sketch_path);
        $ratio = 1;

        if ($sketchPath && is_file($sketchPath)) {
            $imageSize = @getimagesize($sketchPath);
            if ($imageSize && ! empty($imageSize[1])) {
                $ratio = $imageSize[0] / $imageSize[1];
            }
        }

        $slotUnits = $ratio >= 1.7 ? 6 : ($ratio >= 0.85 ? 3 : 2);
        $sketchItem = [
            'inspection' => $inspection,
            'path' => $sketchPath,
            'ratio' => $ratio,
            'slot_units' => $slotUnits,
            'display_units' => $slotUnits,
        ];

        if ($slotUnits === 6) {
            if (! empty($currentSketchRow)) {
                $sketchGridRows[] = $currentSketchRow;
                $currentSketchRow = [];
                $currentSketchUnits = 0;
            }

            $sketchGridRows[] = [$sketchItem];
            continue;
        }

        if ($currentSketchUnits + $slotUnits > 6) {
            $sketchGridRows[] = $currentSketchRow;
            $currentSketchRow = [];
            $currentSketchUnits = 0;
        }

        $currentSketchRow[] = $sketchItem;
        $currentSketchUnits += $slotUnits;

        if ($currentSketchUnits === 6) {
            $sketchGridRows[] = $currentSketchRow;
            $currentSketchRow = [];
            $currentSketchUnits = 0;
        }
    }

    if (! empty($currentSketchRow)) {
        $sketchGridRows[] = $currentSketchRow;
    }

    foreach ($sketchGridRows as $rowIndex => $sketchRow) {
        $usedUnits = collect($sketchRow)->sum('slot_units');
        $remainingUnits = 6 - $usedUnits;

        if ($remainingUnits <= 0) {
            continue;
        }

        if (count($sketchRow) === 1) {
            $sketchGridRows[$rowIndex][0]['display_units'] = 6;
            continue;
        }

        if (count($sketchRow) === 2 && $usedUnits <= 4) {
            $sketchGridRows[$rowIndex][0]['display_units'] = 3;
            $sketchGridRows[$rowIndex][1]['display_units'] = 3;
            continue;
        }

        $lastIndex = count($sketchRow) - 1;
        $sketchGridRows[$rowIndex][$lastIndex]['display_units'] += $remainingUnits;
    }
@endphp

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $serviceTitle }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #111827;
            margin: 0;
        }

        @page {
            margin: 48px;
        }

        .header-table,
        .section-table,
        .data-table,
        .approval-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            border: 1px solid #111827;
            padding: 7px;
            vertical-align: middle;
        }

        .brand {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: .5px;
        }

        .title {
            font-size: 14px;
            font-weight: 700;
            text-align: center;
        }

        .report-no {
            font-size: 10px;
            text-align: right;
        }

        .main-report-brand {
            width: 100%;
            border-collapse: collapse;
            background: #252323;
        }

        .main-report-brand td {
            border: 0;
            padding: 0;
            vertical-align: middle;
        }

        .main-report-logo-cell {
            width: 82px;
            height: 52px;
            text-align: center;
        }

        .main-report-logo {
            max-width: 70px;
            max-height: 48px;
        }

        .main-report-company {
            color: #76b8ff;
            font-size: 24px;
            font-weight: 400;
            line-height: 52px;
            padding-left: 4px;
        }

        .main-report-meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .main-report-meta td {
            border: 1px solid #111827;
            padding: 4px 6px;
            vertical-align: middle;
        }

        .main-report-title {
            font-size: 12px;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
        }

        .main-report-number {
            width: 32%;
            font-size: 9px;
            text-align: right;
        }

        .section-title {
            background: #e5e7eb;
            border: 1px solid #111827;
            font-weight: 700;
            padding: 3px 5px;
            margin-top: 0;
            text-transform: uppercase;
        }

        .section-title + table {
            margin-top: 0;
        }

        .section-table td {
            border: 1px solid #111827;
            padding: 3px 4px;
            vertical-align: top;
        }

        .label {
            width: 22%;
            font-weight: 700;
            background: #f9fafb;
        }

        .data-table th,
        .data-table td,
        .approval-table th,
        .approval-table td {
            border: 1px solid #111827;
            padding: 3px;
            vertical-align: top;
        }

        .data-table th,
        .approval-table th {
            background: #f3f4f6;
            font-weight: 700;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .photo-cell {
            width: 33.33%;
            height: 96px;
            text-align: center;
        }

        .photo-cell img {
            max-width: 100%;
            max-height: 88px;
        }

        .sketch-grid-table {
            table-layout: fixed;
        }

        .sketch-cell {
            height: 112px;
            text-align: center;
            page-break-inside: avoid;
        }

        .sketch-cell img {
            max-width: 100%;
            max-height: 104px;
        }

        .pdf-sketch-wrapper {
            position: relative;
            display: inline-block;
            max-width: 100%;
            max-height: 104px;
        }

        .pdf-sketch-wrapper img {
            display: block;
        }

        .sketch-cell-unit-2 img,
        .sketch-cell-unit-2 .pdf-sketch-wrapper {
            max-height: 102px;
        }

        .sketch-cell-unit-6 img,
        .sketch-cell-unit-6 .pdf-sketch-wrapper {
            max-height: 110px;
        }

        .pdf-sketch-marker {
            position: absolute;
            transform: translate(-50%, -50%);
            white-space: nowrap;
            color: #b91c1c;
            font-size: 7px;
            line-height: 1;
        }

        .pdf-marker-dot {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 6px;
            background: #b91c1c;
            border: 1px solid #fff;
            vertical-align: middle;
        }

        .pdf-marker-label {
            display: inline-block;
            margin-left: 2px;
            padding: 1px 2px;
            border: 1px solid #b91c1c;
            background: #fff;
            vertical-align: middle;
        }

        .muted {
            color: #6b7280;
        }

        .signature-box {
            height: 74px;
        }

        .approval-table {
            table-layout: fixed;
        }

        .approval-table th,
        .approval-table td {
            width: 25%;
        }

        .name-line {
            font-weight: 700;
            text-align: center;
        }

        .date-line {
            height: 18px;
            text-align: left;
            vertical-align: bottom;
        }

        .small {
            font-size: 8px;
        }

        .checkbox-item {
            display: inline-block;
            min-width: 118px;
            margin: 0 8px 2px 0;
            white-space: nowrap;
        }

        .pdf-checkbox {
            display: inline-block;
            width: 10px;
            height: 10px;
            line-height: 9px;
            border: 1px solid #111827;
            margin-right: 4px;
            text-align: center;
            font-size: 8px;
            font-weight: 700;
            vertical-align: middle;
        }

        .page-break {
            page-break-before: always;
        }

        .standard-page {
            padding: 12px;
        }

        .cover-template-page {
            position: relative;
            margin: 0;
            padding: 0;
            width: 1px;
            height: 1px;
            overflow: visible;
            line-height: 0;
        }

        .cover-template-image {
            position: absolute;
            top: -48px;
            left: -48px;
            display: block;
            width: 794px;
            height: 1123px;
            max-width: none;
        }

        .cover-page {
            height: 760px;
            position: relative;
            border: 2px solid #111827;
            padding: 48px 42px;
            text-align: center;
        }

        .cover-logo {
            height: 86px;
            margin-bottom: 30px;
        }

        .cover-title {
            font-size: 24px;
            font-weight: 700;
            margin-top: 90px;
            text-transform: uppercase;
        }

        .cover-subtitle {
            font-size: 15px;
            margin-top: 10px;
            text-transform: uppercase;
        }

        .cover-meta {
            width: 70%;
            margin: 60px auto 0;
            border-collapse: collapse;
            font-size: 11px;
            text-align: left;
        }

        .cover-meta td {
            border: 1px solid #111827;
            padding: 8px;
        }

        .cover-footer {
            position: absolute;
            bottom: 38px;
            left: 42px;
            right: 42px;
            font-size: 11px;
            text-align: center;
        }

        .page-header-image,
        .page-footer-image {
            width: 100%;
        }

        .page-header-image {
            max-height: 82px;
            margin-bottom: 8px;
        }

        .page-footer-image {
            max-height: 54px;
            margin-top: 8px;
        }

        .page-title {
            font-size: 16px;
            font-weight: 700;
            text-align: center;
            margin: 8px 0 12px;
            text-transform: uppercase;
        }

        .content-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .content-table th,
        .content-table td {
            border: 1px solid #111827;
            padding: 8px;
        }

        .content-table th {
            background: #e5e7eb;
        }

        .certificate-standard-page {
            padding: 0;
        }

        .certificate-page {
            text-align: center;
            line-height: 0;
        }

        .certificate-image {
            display: inline-block;
            max-width: 100%;
            max-height: 1018px;
        }

        .certificate-title {
            font-size: 11px;
            font-weight: 700;
            line-height: 1.2;
            margin: 0 0 6px;
            text-align: center;
        }

        .certificate-empty {
            border: 1px solid #111827;
            padding: 18px;
            text-align: center;
        }

        .reference-project-page {
            position: relative;
            height: 980px;
            color: #5c5860;
            font-family: DejaVu Sans, sans-serif;
        }

        .reference-approved {
            position: absolute;
            top: 78px;
            right: 86px;
            text-align: center;
            font-size: 12px;
            color: #6b6870;
        }

        .reference-bki {
            display: block;
            margin-top: 12px;
            color: #2f67b1;
            font-size: 40px;
            font-weight: 700;
            letter-spacing: -2px;
            line-height: 1;
        }

        .reference-title {
            position: absolute;
            top: 490px;
            left: 92px;
            font-size: 24px;
            font-weight: 700;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .reference-meta {
            position: absolute;
            top: 570px;
            left: 92px;
            font-size: 14px;
            line-height: 2.1;
            font-weight: 700;
        }

        .reference-meta-label {
            display: inline-block;
            width: 150px;
            font-style: italic;
            white-space: nowrap;
        }

        .reference-meta-separator {
            display: inline-block;
            width: 20px;
        }

        .reference-object-title {
            position: absolute;
            top: 790px;
            left: 92px;
            right: 70px;
            font-size: 27px;
            font-weight: 700;
            text-transform: uppercase;
            color: #5b5760;
        }
    </style>
</head>
<body>
    <div class="cover-template-page">
        @if ($coverTemplateBase64)
            <img src="{{ $coverTemplateBase64 }}" class="cover-template-image" alt="Geotama Cover">
        @endif
    </div>

    <div class="page-break"></div>

    <div class="reference-project-page">
        <div class="reference-approved">
            Approved By
            <span class="reference-bki">bki</span>
        </div>

        <div class="reference-title">{{ $serviceTitle }}</div>

        <div class="reference-meta">
            <div>
                <span class="reference-meta-label">Report No.</span>
                <span class="reference-meta-separator">:</span>
                <span>{{ $ndtReport->report_no }}</span>
            </div>
            <div>
                <span class="reference-meta-label">Client</span>
                <span class="reference-meta-separator">:</span>
                <span>{{ $proyek->permohonan->nama_perusahaan ?? '-' }}</span>
            </div>
            <div>
                <span class="reference-meta-label">Location</span>
                <span class="reference-meta-separator">:</span>
                <span>{{ $proyek->permohonan->lokasi ?? '-' }}</span>
            </div>
            <div>
                <span class="reference-meta-label">Date of Inspection</span>
                <span class="reference-meta-separator">:</span>
                <span>{{ optional($laporan->tanggal_pelaksanaan)->format('d F Y') ?? '-' }}</span>
            </div>
        </div>

        <div class="reference-object-title">{{ $projectCoverTitle }}</div>
    </div>

    <div class="page-break"></div>

    <div class="standard-page">
        {{-- @if ($headerBase64)
            <img src="{{ $headerBase64 }}" class="page-header-image" alt="Header">
        @endif --}}
        <div class="page-title">List Content</div>
        <table class="content-table">
            <thead>
                <tr>
                    <th style="width: 12%;">No</th>
                    <th>Description</th>
                    <th style="width: 18%;">Page</th>
                </tr>
            </thead>
            <tbody>
                <tr><td class="text-center">1</td><td>Cover</td><td class="text-center">1</td></tr>
                <tr><td class="text-center">2</td><td>Project Data</td><td class="text-center">2</td></tr>
                <tr><td class="text-center">3</td><td>List Content</td><td class="text-center">3</td></tr>
                <tr><td class="text-center">4</td><td>Certificates</td><td class="text-center">4{{ $certificatePageCount > 1 ? '-' . (3 + $certificatePageCount) : '' }}</td></tr>
                <tr><td class="text-center">5</td><td>{{ $serviceTitle }}</td><td class="text-center">{{ $mainReportPage }}</td></tr>
                <tr><td class="text-center">6</td><td>Photo Documentation Area</td><td class="text-center">{{ $documentationPage }}</td></tr>
            </tbody>
        </table>
        {{-- @if ($footerBase64)
            <img src="{{ $footerBase64 }}" class="page-footer-image" alt="Footer">
        @endif --}}
    </div>

    <div class="page-break"></div>

    @if ($certificatePages->isNotEmpty())
        @foreach ($certificatePages as $certificate)
            @if (!$loop->first)
                <div class="page-break"></div>
            @endif
            <div class="standard-page certificate-standard-page">
                {{-- @if ($headerBase64)
                    <img src="{{ $headerBase64 }}" class="page-header-image" alt="Header">
                @endif --}}
                <div class="certificate-page">
                    @php
                        $certificateDisplayPath = $certificate->preview_path
                            ?: $certificate->certificate?->preview_path
                            ?: ($isImagePath($certificate->file_path) ? $certificate->file_path : null);
                        $certificatePath = $storagePath($certificateDisplayPath);
                    @endphp
                    <div class="certificate-title">
                        {{ $certificate->certificate_title ?? 'Certificate' }}
                        {{ $certificate->certificate_no ? ' - ' . $certificate->certificate_no : '' }}
                    </div>
                    @if ($certificatePath)
                        <img src="{{ $certificatePath }}" class="certificate-image" alt="{{ $certificate->certificate_title }}">
                    @else
                        <span class="muted">Preview sertifikat belum tersedia.</span>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="standard-page certificate-standard-page">
            {{-- @if ($headerBase64)
                <img src="{{ $headerBase64 }}" class="page-header-image" alt="Header">
            @endif --}}
            <div class="page-title">Certificates</div>
            <div class="certificate-empty">
                <span class="muted">Belum ada sertifikat yang dipilih untuk report ini.</span>
            </div>
        </div>
    @endif

    <div class="page-break"></div>

    <div class="standard-page">
    <table class="main-report-brand">
        <tr>
            <td class="main-report-logo-cell">
                @if ($logoBase64)
                    <img src="{{ $logoBase64 }}" class="main-report-logo" alt="Geotama Global Intijaya">
                @endif
            </td>
            <td class="main-report-company">Geotama Global Intijaya</td>
        </tr>
    </table>
    <table class="main-report-meta">
        <tr>
            <td class="main-report-title">{{ strtoupper($serviceTitle) }}</td>
            <td class="main-report-number">
                <strong>Report No</strong><br>
                {{ $ndtReport->report_no }}
            </td>
        </tr>
    </table>

    <div class="section-title">Section 1 - Project Data</div>
    <table class="section-table">
        <tr>
            <td class="label">Client</td>
            <td>{{ $proyek->permohonan->nama_perusahaan ?? '-' }}</td>
            <td class="label">Date</td>
            <td>{{ optional($laporan->tanggal_pelaksanaan)->format('d/m/Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Location</td>
            <td>{{ $proyek->permohonan->lokasi ?? '-' }}</td>
            <td class="label">Project No</td>
            <td>{{ $proyek->no_proyek ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Project Name</td>
            <td colspan="3">{{ $proyek->permohonan->nama_proyek ?? '-' }}</td>
        </tr>
    </table>

    <div class="section-title">Section 2 - Material Identification</div>
    <table class="section-table">
        <tr>
            <td class="label">Part No</td>
            <td>{{ $ndtReport->part_no ?? '-' }}</td>
            <td class="label">Material</td>
            <td>{{ $ndtReport->material ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Description</td>
            <td>{{ $ndtReport->description ?? '-' }}</td>
            <td class="label">Temp</td>
            <td>{{ $ndtReport->temperature ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Procedure No</td>
            <td>{{ $formatMaster($masterData['procedure']) }}</td>
            <td class="label">Criteria</td>
            <td>{{ $formatMaster($masterData['criteria']) }}</td>
        </tr>
        <tr>
            <td class="label">Testing Standard</td>
            <td colspan="3">{{ $formatMaster($masterData['standard']) }}</td>
        </tr>
    </table>

    <div class="section-title">Section 3 - Condition</div>
    <table class="section-table">
        <tr>
            <td class="label">Surface</td>
            <td>{!! $checkBoxes($surfaceOptions, $ndtReport->surface_conditions) !!}</td>
        </tr>
        <tr>
            <td class="label">Preparation</td>
            <td>{!! $checkBoxes($preparationOptions, $ndtReport->preparation_conditions) !!}</td>
        </tr>
        <tr>
            <td class="label">Stage Of</td>
            <td>{!! $checkBoxes($stageOfOptions, $ndtReport->stage_ofs) !!}</td>
        </tr>
        <tr>
            <td class="label">Examination</td>
            <td>{!! $checkBoxes($examinationOptions, $ndtReport->examinations) !!}</td>
        </tr>
    </table>

    <div class="section-title">Section 4 - Technique / Equipment / Media</div>
    <table class="section-table">
        <tr>
            <td class="label">Technique</td>
            <td>{!! $checkBoxes($techniqueOptions, $ndtReport->techniques) !!}</td>
        </tr>
        <tr>
            <td class="label">Type</td>
            <td>{!! $checkBoxes($testTypeOptions, $ndtReport->test_types) !!}</td>
        </tr>
        <tr>
            <td class="label">Penetrant Application</td>
            <td>{!! $checkBoxes($penetrantApplicationOptions, $ndtReport->penetrant_applications) !!}</td>
        </tr>
    </table>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 28%;">Material</th>
                <th>Trade Name</th>
                <th>Batch Number</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($materialOptions as $key => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ $ndtReport->material_rows[$key]['trade_name'] ?? '-' }}</td>
                    <td>{{ $ndtReport->material_rows[$key]['batch_number'] ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if ($ndtReport->service_code !== 'MT')
        <table class="section-table">
            <tr>
                <td class="label">Dwell Time - Penetrant</td>
                <td>{{ $ndtReport->dwell_times['penetrant_minutes'] ?? '-' }} minutes</td>
                <td class="label">Dwell Time - Developer</td>
                <td>{{ $ndtReport->dwell_times['developer_minutes'] ?? '-' }} minutes</td>
            </tr>
            <tr>
                <td class="label">Blacklight Intensity</td>
                <td colspan="3">
                    @if ($ndtReport->blacklight_intensity)
                        {{ $ndtReport->blacklight_intensity }} mW/cm<sup>2</sup>
                    @else
                        -
                    @endif
                </td>
            </tr>
        </table>
    @else
        <table class="section-table">
            <tr>
                <td class="label">Blacklight Intensity</td>
                <td>
                    @if ($ndtReport->blacklight_intensity)
                        {{ $ndtReport->blacklight_intensity }} mW/cm<sup>2</sup>
                    @else
                        -
                    @endif
                </td>
            </tr>
        </table>
    @endif

    <div class="section-title">Section 5 - Inspection Result</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Code</th>
                <th>ID No</th>
                <th>Dia</th>
                <th>Length</th>
                <th>Thick</th>
                <th>Result</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ndtReport->inspectionItems as $inspection)
                <tr>
                    <td>{{ $inspection->description ?? '-' }}</td>
                    <td>{{ $inspection->code ?? '-' }}</td>
                    <td>{{ $inspection->id_no ?? '-' }}</td>
                    <td class="text-center">{{ $inspection->diameter_mm ?? '-' }}</td>
                    <td class="text-center">{{ $inspection->length_mm ?? '-' }}</td>
                    <td class="text-center">{{ $inspection->thickness_mm ?? '-' }}</td>
                    <td class="text-center">{{ $inspection->result ? strtoupper($inspection->result) : '-' }}</td>
                    <td>{{ $inspection->remark ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center muted">No inspection item.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Section 6 - Sketch</div>
    <table class="data-table sketch-grid-table">
        <colgroup>
            <col style="width: 16.66%;">
            <col style="width: 16.66%;">
            <col style="width: 16.66%;">
            <col style="width: 16.66%;">
            <col style="width: 16.66%;">
            <col style="width: 16.66%;">
        </colgroup>
        <tbody>
            @forelse ($sketchGridRows as $sketchRow)
                <tr>
                    @foreach ($sketchRow as $sketchItem)
                        @php
                            $inspection = $sketchItem['inspection'];
                            $sketchPath = $sketchItem['path'];
                            $displayUnits = (int) $sketchItem['display_units'];
                        @endphp
                        <td class="sketch-cell sketch-cell-unit-{{ $displayUnits }}" colspan="{{ $displayUnits }}">
                            @if ($sketchPath)
                            <div class="pdf-sketch-wrapper">
                                <img src="{{ $sketchPath }}" alt="{{ $inspection->description }}">
                                @foreach (($inspection->sketch_annotations ?? []) as $annotation)
                                    <span class="pdf-sketch-marker" style="left: {{ (float) ($annotation['x'] ?? 0) }}%; top: {{ (float) ($annotation['y'] ?? 0) }}%;">
                                        <span class="pdf-marker-dot"></span>
                                        <span class="pdf-marker-label">{{ $annotation['text'] ?? '' }}</span>
                                    </span>
                                @endforeach
                            </div>
                            @else
                                <span class="muted">Sketch belum tersedia.</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center muted">Sketch belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Section 7 - Approval</div>
    <table class="approval-table">
        <thead>
            <tr>
                @foreach ($approvalRoles as $role => $label)
                    <th>{{ $label }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach ($approvalRoles as $role => $label)
                    <td class="signature-box"></td>
                @endforeach
            </tr>
            <tr>
                @foreach ($approvalRoles as $role => $label)
                    <td>
                        <div class="name-line">{{ $ndtReport->{$role . '_name'} ?? '' }}</div>
                        <div class="text-center small">{{ $ndtReport->{$role . '_position'} ?? '' }}</div>
                    </td>
                @endforeach
            </tr>
            <tr>
                @foreach ($approvalRoles as $role => $label)
                    <td class="date-line">Date:</td>
                @endforeach
            </tr>
        </tbody>
    </table>
    </div>

    <div class="page-break"></div>

    <div class="standard-page">
        <div class="section-title">Section 8 - Photo Documentation Area</div>
        @foreach ($ndtReport->inspectionItems as $inspection)
            @php $photoMap = $inspection->photos->keyBy('type'); @endphp
            <table class="data-table">
                <thead>
                    <tr>
                        <th colspan="3">{{ $inspection->description ?? 'Item ' . $loop->iteration }}</th>
                    </tr>
                    <tr>
                        <th>Before</th>
                        <th>During</th>
                        <th>After</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @foreach (['before', 'during', 'after'] as $type)
                            @php $photoPath = $storagePath($photoMap->get($type)?->file_path); @endphp
                            <td class="photo-cell">
                                @if ($photoPath)
                                    <img src="{{ $photoPath }}" alt="{{ $type }}">
                                @else
                                    <span class="muted">No photo</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        @endforeach
    </div>
</body>
</html>
