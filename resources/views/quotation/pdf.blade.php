<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $quotation->no_quo }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
            margin: 0;
        }

        .page {
            padding: 0 24px;
        }

        .header-space {
            height: 100px;
            border-bottom: 1px dashed #b5b5b5;
            margin-bottom: 14px;
        }

        .footer-space {
            height: 70px;
            border-top: 1px dashed #b5b5b5;
            margin-top: 20px;
        }

        .row {
            width: 100%;
            margin-bottom: 10px;
        }

        .left,
        .right {
            display: inline-block;
            vertical-align: top;
        }

        .left {
            width: 58%;
        }

        .right {
            width: 40%;
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta td {
            padding: 2px 0;
        }

        .items th,
        .items td {
            border: 1px solid #777;
            padding: 6px;
        }

        .items th {
            text-align: center;
            background: #f5f5f5;
        }

        .text-right {
            text-align: right;
        }

        .section-title {
            margin: 12px 0 6px;
            font-weight: bold;
        }

        ol,
        ul {
            margin: 6px 0 0 18px;
            padding: 0;
        }

        .signature-wrapper {
            margin-top: 24px;
        }

        .qr-box {
            width: 120px;
            text-align: center;
        }

        .qr-box img {
            width: 110px;
            height: 110px;
            border: 1px solid #ddd;
            padding: 4px;
        }

        .muted {
            color: #7d7d7d;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="header-space"></div>

        <div class="row">
            <div class="left">
                <strong>Kepada Yth</strong><br>
                {{ $quotation->client->nama_perusahaan ?? '-' }}<br>
                {{ $quotation->client->alamat ?? '-' }}
            </div>
            <div class="right">
                <table class="meta">
                    <tr>
                        <td><strong>Tanggal</strong></td>
                        <td>: {{ optional($quotation->tanggal)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nomor</strong></td>
                        <td>: {{ $quotation->no_quo }}</td>
                    </tr>
                    <tr>
                        <td><strong>Perihal</strong></td>
                        <td>: Penawaran Jasa</td>
                    </tr>
                </table>
            </div>
        </div>

        <ol>
            <li>Berikut kami sampaikan penawaran jasa untuk kebutuhan pekerjaan yang diajukan.</li>
            <li class="section-title">Rincian biaya pekerjaan:</li>
        </ol>

        <table class="items">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Description</th>
                    <th width="15%">Satuan</th>
                    <th width="12%">Qty</th>
                    <th width="22%">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quotation->items as $item)
                    <tr>
                        <td class="text-right">{{ $loop->iteration }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->satuan ?? '-' }}</td>
                        <td class="text-right">{{ number_format((float) $item->qty, 2, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format((float) $item->total, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-right">Tidak ada item.</td>
                    </tr>
                @endforelse
                <tr>
                    <td colspan="4" class="text-right"><strong>Grand Total</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format((float) $quotation->grand_total_quo, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">Terms & Conditions</div>
        @if($quotation->terms->isNotEmpty())
            <ul>
                @foreach($quotation->terms as $term)
                    <li>{{ $term->name }}</li>
                @endforeach
            </ul>
        @else
            <p>-</p>
        @endif

        <p style="margin-top: 16px;">Demikian penawaran dari kami, atas perhatian dan kerjasamanya kami ucapkan terima kasih.</p>

        <div class="signature-wrapper">
            <table>
                <tr>
                    <td width="70%">
                        Hormat kami,<br>
                        PT Geotama Global Intijaya
                    </td>
                    <td width="30%" class="qr-box">
                        @if ($qrBase64)
                            <img src="{{ $qrBase64 }}" alt="QR Digital Signature">
                        @else
                            <div class="muted" style="border:1px solid #ddd; padding: 44px 8px;">QR belum tersedia</div>
                        @endif
                        <div class="muted">Scan untuk lihat TTD digital</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer-space"></div>
    </div>
</body>

</html>
