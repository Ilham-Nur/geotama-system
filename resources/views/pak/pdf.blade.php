<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>PAK {{ $pak->pak_number }}</title>
    <style>
        body {
            font-family: "DejaVu Sans", "Times New Roman", Times, serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        @page {
            margin-top: 230px;
            margin-bottom: 80px;
            margin-left: 20px;
            margin-right: 20px;
        }

        .header-page {
            position: fixed;
            top: -230px;
            left: 0;
            right: 0;
            height: 200px;
            background: white;
            z-index: 100;
        }

        .content {
            margin-top: 0;
        }

        .header {
            width: 100%;
            padding-bottom: 4px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            border: none;
            vertical-align: middle;
            padding: 2px 4px;
        }

        .logo {
            width: 80px;
            height: auto;
        }

        .header-text h1 {
            margin: 0;
            font-size: 16px;
            color: #005f73;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .header-text p {
            margin: 1px 0;
            font-size: 9px;
        }

        .doc-title-bar {
            margin-top: 4px;
            padding: 6px 8px;
            background-color: #005f73;
            color: #ffffff;
            text-align: center;
            font-weight: bold;
            font-size: 13px;
            text-transform: uppercase;
            border-radius: 3px;
        }

        .info-box {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            font-size: 9px;
        }

        .info-box td {
            border: 0.5px solid #005f73;
            padding: 3px 4px;
            vertical-align: top;
        }

        .footer {
            position: fixed;
            bottom: -100px;
            left: 0;
            right: 0;
            height: 80px;
            text-align: center;
            font-size: 10px;
            color: #555;
            border-top: 1px solid #ccc;
            padding: 5px 0;
            background-color: white;
            z-index: 100;
        }

        .footer img {
            height: 15px;
            margin-right: 5px;
            vertical-align: middle;
            padding-top: 7px;
        }

        .pagenum:before {
            content: counter(page);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px 4px;
            text-align: left;
            font-size: 10px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .items-table th,
        .items-table td {
            border: 0.5px solid #000;
            padding: 3px 4px;
        }

        .items-table thead th {
            background: #005f73;
            color: #fff;
            text-align: center;
        }

        .row-category td {
            background: #cae9ef;
            font-weight: bold;
        }

        .row-subtotal td {
            background: #f1f1f1;
        }

        .row-grand td {
            background: #dbdbdb;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge-ok {
            display: inline-block;
            padding: 1px 4px;
            font-size: 8px;
            border-radius: 3px;
            background: #28a745;
            color: #fff;
        }

        .badge-over {
            display: inline-block;
            padding: 1px 4px;
            font-size: 8px;
            border-radius: 3px;
            background: #dc3545;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="header-page">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td style="width:130px;">
                        @php
                            $path = public_path('template/logo-geotama.jpeg');
                            $type = pathinfo($path, PATHINFO_EXTENSION);
                            $base64 = file_exists($path)
                                ? 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($path))
                                : '';
                        @endphp
                        @if ($base64)
                            <img src="{{ $base64 }}" class="logo" />
                        @endif
                    </td>
                    <td>
                        <div class="header-text">
                            <h1>PT. GEOTAMA GLOBAL INTIJAYA</h1>
                            <p>Inspection, Testing, Engineering, and Industrial Services</p>
                            <p>Batam, Kepulauan Riau - Indonesia</p>
                        </div>
                    </td>
                </tr>
            </table>

            <div class="doc-title-bar">
                Proposal Anggaran Kerja (PAK)
            </div>

            <table class="info-box">
                <tr>
                    <td style="width:50%;">
                        <strong>PAK Number</strong> : {{ $pak->pak_number }}<br>
                        <strong>Project Name</strong> : {{ $pak->pak_name }}<br>
                        <strong>Project Value</strong> : Rp {{ number_format($pak->project_value, 0, ',', '.') }}<br>
                        <strong>Date</strong> : {{ optional($pak->created_at)->format('d M Y') }}
                    </td>
                    <td style="width:50%;">
                        <strong>Perusahaan</strong> : {{ $permohonan->nama_perusahaan ?? '-' }}<br>
                        <strong>PIC</strong> : {{ $permohonan->nama_pic ?? '-' }}<br>
                        <strong>No. Telp</strong> : {{ $permohonan->no_telp ?? '-' }}<br>
                        <strong>Lokasi</strong> : {{ $permohonan->lokasi ?? '-' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="footer">
        <img src="{{ public_path('template/logo-geotama.jpeg') }}" alt="Logo">
        PT. GEOTAMA GLOBAL INTIJAYA | Page <span class="pagenum"></span>
    </div>

    <div class="content">
        <h5>DETAIL ITEMS</h5>

        @php
            $projectValue = (float) $pak->project_value;
            $grandTotal = 0;
            $groupedItems = $pak->items->groupBy('category_id');
        @endphp

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:30px;">NO</th>
                    <th>Operational Needs</th>
                    <th>Description</th>
                    <th style="width:50px;">Qty</th>
                    <th style="width:90px;">Unit Cost</th>
                    <th style="width:90px;">Total Cost</th>
                    <th style="width:90px;">MAX COST</th>
                    <th style="width:40px;">%</th>
                    <th style="width:60px;">Status</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($groupedItems as $catId => $rows)
                    @php
                        $cat =
                            $categories[$catId] ??
                            (object) [
                                'code' => '-',
                                'name' => 'Uncategorized',
                                'max_percentage' => 0,
                            ];

                        $allowed = $projectValue * ((float) $cat->max_percentage / 100);
                        $sectionTotal = $rows->sum('total_cost');
                        $percent = $projectValue > 0 ? ($sectionTotal / $projectValue) * 100 : 0;
                        $sectionStatus = $sectionTotal > $allowed ? 'OVER' : 'OK';
                        $grandTotal += $sectionTotal;
                    @endphp

                    <tr class="row-category">
                        <td class="text-center"><strong>{{ $cat->code }}</strong></td>
                        <td colspan="8">
                            <strong>{{ strtoupper($cat->name) }}</strong> (Max {{ $cat->max_percentage }}%)
                        </td>
                    </tr>

                    @foreach ($rows as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->description }}</td>
                            <td class="text-center">{{ rtrim(rtrim(number_format($item->qty, 2, ',', '.'), '0'), ',') }}</td>
                            <td class="text-right">Rp {{ number_format($item->unit_cost, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($item->total_cost, 0, ',', '.') }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endforeach

                    <tr class="row-subtotal">
                        <td colspan="5" class="text-right"><strong>TOTAL {{ $cat->code }} (Max {{ $cat->max_percentage }}%)</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($sectionTotal, 0, ',', '.') }}</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($allowed, 0, ',', '.') }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($percent, 0) }}%</strong></td>
                        <td class="text-center">
                            <span class="{{ $sectionStatus == 'OK' ? 'badge-ok' : 'badge-over' }}">{{ $sectionStatus }}</span>
                        </td>
                    </tr>
                @endforeach

                @php
                    $totalCost = $grandTotal;
                    $profit = $projectValue - $totalCost;
                    $profitPercent = $projectValue > 0 ? ($profit / $projectValue) * 100 : 0;
                    $profitLabel = $profit >= 0 ? 'OK' : 'OVER';
                    $profitClass = $profit >= 0 ? 'badge-ok' : 'badge-over';
                @endphp

                <tr class="row-grand">
                    <td colspan="5" class="text-right"><strong>PROJECT VALUE / NILAI KONTRAK (Rp)</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($projectValue, 0, ',', '.') }}</strong></td>
                    <td colspan="3"></td>
                </tr>

                <tr class="row-grand">
                    <td colspan="5" class="text-right"><strong>Total Pengeluaran (Rp)</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($totalCost, 0, ',', '.') }}</strong></td>
                    <td class="text-right"><strong>Pengeluaran (%)</strong></td>
                    <td colspan="2" class="text-center"><strong>{{ number_format($projectValue > 0 ? ($totalCost / $projectValue) * 100 : 0, 0) }}%</strong></td>
                </tr>

                <tr class="row-grand">
                    <td colspan="5" class="text-right"><strong>PROFIT (Rp)</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($profit, 0, ',', '.') }}</strong></td>
                    <td class="text-right"><strong>Profit (%)</strong></td>
                    <td class="text-center"><strong>{{ number_format($profitPercent, 0) }}%</strong></td>
                    <td class="text-center"><span class="{{ $profitClass }}">{{ $profitLabel }}</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
