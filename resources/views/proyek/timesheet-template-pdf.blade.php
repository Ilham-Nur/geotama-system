<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Template Timesheet {{ $proyek->no_proyek }}</title>
    <style>
        @page {
            margin: 20px 20px 25px 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: top;
        }

        .no-border {
            border: none !important;
        }

        .center {
            text-align: center;
        }

        .middle {
            vertical-align: middle;
        }

        .bold {
            font-weight: bold;
        }

        .title-company {
            font-size: 18px;
            font-weight: bold;
        }

        .title-form {
            font-size: 14px;
            font-weight: bold;
        }

        .subtitle {
            font-size: 10px;
            font-style: italic;
        }

        .section-title {
            background: #f2f2f2;
            font-weight: bold;
        }

        .logo-box {
            width: 90px;
            text-align: center;
        }

        .logo-img {
            width: 65px;
            height: auto;
        }

        .checkbox {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 1px solid #000;
            margin-right: 4px;
            vertical-align: middle;
        }

        .timesheet-row td {
            height: 36px;
        }
    </style>
</head>

<body>
    <table>
        <tr>
            <td style="padding: 0;">
                <table>
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
                        <td class="center middle title-form">TIMESHEET PROYEK</td>
                    </tr>
                    <tr>
                        <td class="center middle subtitle">FIELD TIMESHEET TEMPLATE</td>
                    </tr>
                </table>

                <table>
                    <tr>
                        <td colspan="3" class="section-title">A. INFORMASI PROYEK</td>
                    </tr>
                    <tr>
                        <td style="width: 180px;">No Proyek</td>
                        <td style="width: 10px;" class="center">:</td>
                        <td>{{ $proyek->no_proyek ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Nama Proyek</td>
                        <td class="center">:</td>
                        <td>{{ $proyek->permohonan->nama_proyek ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Client</td>
                        <td class="center">:</td>
                        <td>{{ $proyek->permohonan->nama_perusahaan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Lokasi</td>
                        <td class="center">:</td>
                        <td>{{ $proyek->permohonan->lokasi ?? '-' }}</td>
                    </tr>
                </table>

                <table>
                    <tr>
                        <td colspan="4" class="section-title">B. DETAIL KEGIATAN / CATATAN LAPANGAN</td>
                    </tr>
                    <tr>
                        <th style="width: 90px;">Jam Mulai</th>
                        <th style="width: 90px;">Jam Selesai</th>
                        <th style="width: 170px;">Layanan</th>
                        <th>Aktivitas / Catatan Lapangan</th>
                    </tr>
                    @for ($i = 0; $i < 12; $i++)
                        <tr class="timesheet-row">
                            <td></td>
                            <td></td>
                            <td>
                                <span class="checkbox"></span>MT &nbsp;
                                <span class="checkbox"></span>PT &nbsp;
                                <span class="checkbox"></span>UT
                            </td>
                            <td></td>
                        </tr>
                    @endfor
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
