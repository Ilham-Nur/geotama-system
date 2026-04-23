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

        @page {
            margin: 120px 25px 90px 25px;
        }

        /* HEADER FIX */
        .header {
            position: fixed;
            top: -100px;
            left: 0;
            right: 0;
            height: 100px;
        }

        /* FOOTER FIX */
        .footer {
            position: fixed;
            bottom: -70px;
            left: 0;
            right: 0;
            height: 70px;
        }

        /* WATERMARK FIX (NO BACKGROUND!) */
        .watermark {
            position: fixed;
            top: 29%;
            left: 45%;
            width: 640px;
            transform: translate(-50%, -50%);
            opacity: 0.05;
            z-index: -1;
        }

        .page {
            position: relative;
            z-index: 1;
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

    @php

        function base64Image($relativePath)
        {
            $fullPath = public_path($relativePath);

            if (!file_exists($fullPath)) {
                return null;
            }

            $img = imagecreatefromstring(file_get_contents($fullPath));

            if (!$img) {
                return null;
            }

            // 🔥 FIX TRANSPARANSI CORELDRAW PNG
            imagealphablending($img, false);
            imagesavealpha($img, true);

            ob_start();
            imagepng($img);
            $data = ob_get_clean();

            imagedestroy($img);

            return 'data:image/png;base64,' . base64_encode($data);
        }

        $headerBase64 = base64Image('template/assets/header_snipingtool.png');
        $footerBase64 = base64Image('template/assets/footer_geotama.png');
        $watermarkBase64 = base64Image('template/assets/images/logo/logo-geotama-removebg-preview.png');

    @endphp

    <!-- HEADER -->
    <div class="header">
        @if ($headerBase64)
            <img src="{{ $headerBase64 }}" style="width:100%; height:100px; object-fit:cover;">
        @endif
    </div>

    <!-- FOOTER -->
    <div class="footer">
        @if ($footerBase64)
            <img src="{{ $footerBase64 }}" style="width:100%; height:70px; object-fit:cover;">
        @endif
    </div>

    <div class="page">

        <!-- WATERMARK -->
        @if ($watermarkBase64)
            <img src="{{ $watermarkBase64 }}" class="watermark">
        @endif

        <!-- CONTENT -->
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
            <li>Berikut kami sampaikan penawaran jasa untuk kebutuhan pekerjaan.</li>
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
                        <td colspan="5" class="text-right">Tidak ada item</td>
                    </tr>
                @endforelse

                <tr>
                    <td colspan="4" class="text-right"><strong>Grand Total</strong></td>
                    <td class="text-right">
                        <strong>Rp {{ number_format((float) $quotation->grand_total_quo, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">Terms & Conditions</div>

        @if ($quotation->terms->isNotEmpty())
            <ul>
                @foreach ($quotation->terms as $term)
                    <li>{{ $term->name }}</li>
                @endforeach
            </ul>
        @else
            <p>-</p>
        @endif

        <p style="margin-top:16px;">
            Demikian penawaran kami, atas perhatian dan kerjasamanya kami ucapkan terima kasih.
        </p>

        <div class="signature-wrapper">
            <table>
                <tr>
                    <td width="70%">
                        Hormat kami,<br>
                        PT Geotama Global Intijaya
                    </td>
                    <td width="30%" class="qr-box">
                        @if ($qrBase64)
                            <img src="{{ $qrBase64 }}">
                        @else
                            <div class="muted" style="border:1px solid #ddd; padding: 44px 8px;">
                                QR belum tersedia
                            </div>
                        @endif
                        <div class="muted">Scan untuk lihat TTD digital</div>
                    </td>
                </tr>
            </table>
        </div>

    </div>

</body>

</html>
