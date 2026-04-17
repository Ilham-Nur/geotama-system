<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Timesheet {{ $timesheet->form_no }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1f2937;
            margin: 28px;
        }

        h1 {
            margin: 0 0 6px 0;
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        h2 {
            margin: 0;
            font-size: 14px;
            font-weight: normal;
            color: #4b5563;
        }

        .section {
            margin-top: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta td {
            padding: 6px 8px;
            border: 1px solid #d1d5db;
            vertical-align: top;
        }

        .meta td.label {
            width: 25%;
            background: #f3f4f6;
            font-weight: bold;
        }

        .grid th,
        .grid td {
            border: 1px solid #d1d5db;
            padding: 8px;
            vertical-align: top;
            height: 30px;
        }

        .grid th {
            background: #f3f4f6;
            text-align: left;
            font-size: 11px;
        }

        .text-center {
            text-align: center;
        }

        .signature-box {
            margin-top: 42px;
        }

        .sign {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }

        .line {
            margin-top: 54px;
            border-top: 1px solid #111827;
            padding-top: 5px;
            font-size: 11px;
        }

        .footer-note {
            margin-top: 24px;
            font-size: 11px;
            color: #6b7280;
        }
    </style>
</head>

<body>
    <h1>Timesheet Inspeksi</h1>
    <h2>Template Baku Lapangan</h2>

    <div class="section">
        <table class="meta">
            <tr>
                <td class="label">Nomor Form</td>
                <td>{{ $timesheet->form_no }}</td>
                <td class="label">Tanggal Form</td>
                <td>{{ $timesheet->created_at?->format('d M Y') ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">No Proyek</td>
                <td>{{ $proyek->no_proyek ?? '-' }}</td>
                <td class="label">Tanggal Inspeksi</td>
                <td>{{ optional($timesheet->inspection_date)->format('d M Y') ?? '............................' }}</td>
            </tr>
            <tr>
                <td class="label">Nama Proyek</td>
                <td>{{ $proyek->permohonan->nama_proyek ?? '-' }}</td>
                <td class="label">Client</td>
                <td>{{ $proyek->permohonan->nama_perusahaan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Lokasi</td>
                <td colspan="3">{{ $proyek->permohonan->lokasi ?? '............................................................' }}</td>
            </tr>
            <tr>
                <td class="label">Catatan Form</td>
                <td colspan="3">{{ $timesheet->remarks ?? '............................................................' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table class="grid">
            <thead>
                <tr>
                    <th style="width: 6%;">No</th>
                    <th style="width: 14%;">Jam Mulai</th>
                    <th style="width: 14%;">Jam Selesai</th>
                    <th style="width: 14%;">Durasi</th>
                    <th>Aktivitas / Catatan Lapangan</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 1; $i <= 8; $i++)
                    <tr>
                        <td class="text-center">{{ $i }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

    <div class="signature-box">
        <div class="sign">
            Petugas Inspeksi,
            <div class="line">Nama & Tanda Tangan</div>
        </div>
        <div class="sign" style="float:right; text-align: right;">
            Supervisor / Verifikator,
            <div class="line">Nama & Tanda Tangan</div>
        </div>
    </div>

    <div class="footer-note">
        Dokumen ini adalah template baku timesheet inspeksi. Setelah diisi manual di lapangan,
        upload hardcopy pada detail proyek menggunakan nomor form yang sama.
    </div>
</body>

</html>
