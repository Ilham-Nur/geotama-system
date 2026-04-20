<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Timesheet Inspeksi</title>
    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td,
        .main-table td,
        .main-table th,
        .activity-table td,
        .activity-table th {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: top;
        }

        .center {
            text-align: center;
        }

        .middle {
            vertical-align: middle;
        }

        .title-company {
            font-size: 17px;
            font-weight: bold;
            letter-spacing: .3px;
        }

        .title-form {
            font-size: 14px;
            font-weight: bold;
        }

        .subtitle {
            font-size: 10px;
            font-style: italic;
        }

        .logo-box {
            width: 90px;
            text-align: center;
        }

        .logo-img {
            width: 65px;
            height: auto;
        }

        .label {
            width: 22%;
            font-weight: bold;
            background: #f2f2f2;
        }

        .activity-table th {
            background: #f2f2f2;
            text-align: left;
            font-size: 10px;
        }

        .activity-table td {
            height: 28px;
        }

        .sig-wrap {
            margin-top: 16px;
        }

        .sig-col {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }

        .sig-line {
            margin-top: 56px;
            border-top: 1px solid #000;
            padding-top: 4px;
        }

        .form-code {
            margin-top: 14px;
            text-align: right;
            font-size: 10px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td rowspan="3" class="logo-box middle">
                @php
                    $path = public_path('template/assets/images/logo/logo-geotama-removebg-preview.png');
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $base64 = file_exists($path)
                        ? 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($path))
                        : '';
                @endphp

                @if ($base64)
                    <img src="{{ $base64 }}" class="logo-img">
                @else
                    LOGO
                @endif
            </td>
            <td class="center middle title-company">PT. GEOTAMA GLOBAL INTIJAYA</td>
        </tr>
        <tr>
            <td class="center middle title-form">TIMESHEET INSPEKSI LAPANGAN</td>
        </tr>
        <tr>
            <td class="center middle subtitle">FIELD INSPECTION TIMESHEET</td>
        </tr>
    </table>

    <table class="main-table" style="margin-top: 8px;">
        <tr>
            <td class="label">Nama Proyek</td>
            <td>{{ $proyek->permohonan->nama_proyek ?? '-' }}</td>
            <td class="label">Tanggal</td>
            <td>................................................</td>
        </tr>
        <tr>
            <td class="label">No Proyek</td>
            <td>{{ $proyek->no_proyek ?? '-' }}</td>
            <td class="label">Durasi Hari</td>
            <td>................................................</td>
        </tr>
        <tr>
            <td class="label">Client</td>
            <td>{{ $proyek->permohonan->nama_perusahaan ?? '-' }}</td>
            <td class="label">Lokasi</td>
            <td>{{ $proyek->permohonan->lokasi ?? '-' }}</td>
        </tr>
    </table>

    <table class="activity-table" style="margin-top: 8px;">
        <thead>
            <tr>
                <th style="width: 22%;">Jam Mulai</th>
                <th style="width: 22%;">Jam Selesai</th>
                <th style="width: 56%;">Aktivitas / Catatan Lapangan</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 1; $i <= 10; $i++)
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="sig-wrap">
        <div class="sig-col">
            Petugas Inspeksi,
            <div class="sig-line">Nama & Tanda Tangan</div>
        </div>
        <div class="sig-col" style="float:right; text-align:right;">
            Supervisor,
            <div class="sig-line">Nama & Tanda Tangan</div>
        </div>
    </div>

    <div class="form-code">GGI-F2-2026-REV 1</div>
</body>

</html>
