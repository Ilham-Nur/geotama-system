<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $invoice->no_invoice }}</title>
    <style>
        @page {
            margin: 12px 16px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #4a4450;
            margin: 0;
            padding: 0;
        }

        .page {
            left: 5%;
            position: relative;
            width: 90%;
        }

        .watermark {
            position: absolute;
            top: 230px;
            height: 620px;
            left: 35px;
            width: 620px;
            opacity: 0.07;
            z-index: 0;
        }

        .content {
            position: relative;
            z-index: 1;
        }

        .clearfix::after {
            content: "";
            display: block;
            clear: both;
        }

        .left {
            float: left;
        }

        .right {
            float: right;
        }

        .w-55 {
            width: 55%;
        }

        .w-40 {
            width: 40%;
        }

        .company-logo {
            height: 200px;
            width: 130px;
            max-height: 140px;
            object-fit: contain;
            margin-bottom: 8px;
        }

        .company-name {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .company-info p {
            margin: 0 0 5px 0;
        }

        .invoice-title {
            text-align: right;
            font-size: 36px;
            letter-spacing: 2px;
            color: #51424d;
            font-weight: 300;
            margin-top: 25px;
            margin-bottom: 89px;
        }

        .billto-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        /* .billto-box p {
            margin: 0 0 5px 0;
        } */

        .meta-inline {
            margin-top: 12px;
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .meta-item {
            display: table-cell;
            vertical-align: top;
            padding-right: 12px;
        }

        .meta-label {
            color: #4b3b45;
            font-weight: bold;
            margin-right: 6px;
        }

        .meta-value {
            font-weight: 600;
            color: #4b3b45;
        }

        .section-gap {
            margin-top: 18px;
        }

        .project-name {
            margin-top: 28px;
            margin-bottom: 14px;
            font-size: 14px;
        }

        .project-name .label {
            display: inline-block;
            width: 135px;
            color: #666;
        }

        .notes-box {
            margin-top: 8px;
            margin-bottom: 12px;
            font-size: 11px;
            line-height: 1.5;
        }

        .notes-box .label {
            font-weight: bold;
            color: #4b3b45;
            margin-right: 6px;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 8px;
        }

        table.items th,
        table.items td {
            padding: 6px 4px;
            border-bottom: 1px solid #7c767c;
            vertical-align: middle;
            font-size: 11px;
        }

        table.items thead th {
            border-bottom: 2px solid #5a545a;
            font-weight: bold;
        }

        /* lebar kolom */
        .col-desc {
            width: 48%;
            text-align: left;
        }

        .col-price {
            width: 20%;
            text-align: center;
        }

        .col-qty {
            width: 10%;
            text-align: center;
        }

        .col-amount {
            width: 22%;
            text-align: right;
        }

        .empty-row td {
            height: 16px;
        }

        .summary-table {
            width: 320px;
            margin-left: auto;
            margin-top: 18px;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 3px 0;
            font-size: 13px;
        }

        .summary-label {
            text-align: right;
            font-weight: bold;
            padding-right: 10px;
        }

        .summary-currency {
            width: 28px;
            text-align: center;
            font-weight: bold;
        }

        .summary-value {
            text-align: right;
            font-weight: bold;
            width: 140px;
        }

        .grand-line td {
            border-top: 2px solid #5a545a;
            padding-top: 8px;
            font-size: 16px;
        }

        .terbilang-box {
            margin-top: 28px;
        }

        .terbilang-label {
            font-size: 13px;
            margin-bottom: 10px;
        }

        .terbilang-value {
            text-align: center;
            color: #8a80a2;
            font-weight: bold;
        }

        .bottom-section {
            margin-top: 42px;
        }

        .payment-box,
        .bank-box,
        .signature-box {
            float: left;
            vertical-align: top;
        }

        .payment-box {
            width: 32%;
        }

        .bank-box {
            width: 32%;
            margin-left: 3%;
        }

        .signature-box {
            width: 28%;
            float: right;
            text-align: center;
        }

        .section-title {
            font-weight: bold;
            color: #4b3b45;
            margin-bottom: 8px;
        }

        .bank-table {
            font-size: 12px;
            border-collapse: collapse;
        }

        .bank-table td {
            padding: 2px 0;
        }

        .sign-space {
            height: 95px;
            margin-bottom: 6px;
            border-bottom: 1px solid #444;
        }

        .sign-name {
            margin-top: 8px;
            font-size: 15px;
            font-weight: bold;
            color: #4b3b45;
        }

        .thanks-box {
            margin-top: 34px;
            width: 360px;
            background: #4b3b45;
            color: #fff;
            text-align: center;
            padding: 9px 12px;
            font-size: 15px;
            font-weight: bold;
        }

        .muted {
            color: #666;
        }

        .page {
            page-break-after: avoid;
        }

        table {
            page-break-inside: avoid;
        }

        tr {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    @php
        $permohonan = $invoice->proyek->permohonan ?? null;

        function base64Image($relativePath)
        {
            $fullPath = public_path($relativePath);

            if (!file_exists($fullPath)) {
                return null;
            }

            $type = pathinfo($fullPath, PATHINFO_EXTENSION);
            $data = file_get_contents($fullPath);

            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $logoBase64 = base64Image('template/assets/images/logo/logo-geotama-removebg-preview.png');
        $watermarkBase64 = base64Image('template/assets/images/logo/logo-geotama-removebg-preview.png');
    @endphp

    <div class="page">
        @if ($watermarkBase64)
            <img src="{{ $watermarkBase64 }}" class="watermark">
        @endif

        <div class="content">
            <div class="clearfix">
                <div class="left w-55">
                    @if ($logoBase64)
                        <img src="{{ $logoBase64 }}" class="company-logo">
                    @endif

                    <div class="company-info">
                        <div class="company-name">PT. Geotama Global Intijaya</div>
                        <p>Tanjung Buntung Block A1 No. 10</p>
                        <p>Kec. Bengkong</p>
                        <p>Batam - Indonesia</p>
                        <p>Phone: 0778-87893065</p>
                        <p>Email: admin@geotamaglobalintijaya.com</p>
                        <p>www.geotamaglobalintijaya.com</p>
                    </div>
                </div>

                <div class="right w-40">
                    <div class="invoice-title">INVOICE</div>

                    <div class="billto-box">
                        <div class="billto-title">BILL TO :</div>
                        <p><strong>{{ $permohonan->nama_perusahaan ?? '-' }}</strong></p>
                        <p>{{ $permohonan->alamat ?? '-' }}</p>
                        <p>Tip : {{ $permohonan->no_telp ?? '-' }}</p>
                        <p>Email : {{ $permohonan->email ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="section-gap clearfix">
                <div class="meta-inline">
                    <div class="meta-item">
                        <span class="meta-label">Invoice Number :</span>
                        <span class="meta-value">{{ $invoice->no_invoice }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Invoice Date :</span>
                        <span class="meta-value">
                            {{ $invoice->tanggal_invoice ? \Carbon\Carbon::parse($invoice->tanggal_invoice)->format('F d, Y') : '-' }}
                        </span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Due Date :</span>
                        <span class="meta-value">
                            {{ $invoice->tanggal_invoice ? \Carbon\Carbon::parse($invoice->tanggal_invoice)->addDays(7)->format('F d, Y') : '-' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="project-name">
                <span class="label">Project Name :</span>
                <strong>{{ $permohonan->nama_proyek ?? ($invoice->proyek->deskripsi ?? ($invoice->proyek->no_proyek ?? '-')) }}</strong>
            </div>

            @if (!empty($invoice->notes))
                <div class="notes-box">
                    <span class="label">Notes :</span>
                    {!! nl2br(e($invoice->notes)) !!}
                </div>
            @endif

            <table class="items">
                <thead>
                    <tr>
                        <th class="col-desc">ITEM DESCRIPTION</th>
                        <th class="col-price">UNIT PRICE</th>
                        <th class="col-qty">QTY</th>
                        <th class="col-amount">AMOUNT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->items as $item)
                        <tr>
                            <td class="col-desc">{{ $item->description }}</td>
                            <td class="col-price">
                                {{ $item->unit ?: '-' }}
                            </td>
                            <td class="col-qty">
                                {{ rtrim(rtrim(number_format($item->qty, 2, '.', ''), '0'), '.') }}
                            </td>
                            <td class="col-amount">
                                Rp {{ number_format($item->total, 2, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach

                    @for ($i = 0; $i < max(0, 4 - $invoice->items->count()); $i++)
                        <tr class="empty-row">
                            <td class="col-desc">&nbsp;</td>
                            <td class="col-price"></td>
                            <td class="col-qty"></td>
                            <td class="col-amount"></td>
                        </tr>
                    @endfor
                </tbody>
            </table>

            <table class="summary-table">
                <tr>
                    <td class="summary-label">Sub Total :</td>
                    <td class="summary-currency">Rp</td>
                    <td class="summary-value">{{ number_format($invoice->sub_total, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="summary-label">Discount :</td>
                    <td class="summary-currency">Rp</td>
                    <td class="summary-value">
                        {{ $invoice->discount > 0 ? number_format($invoice->discount, 2, ',', '.') : '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="summary-label">Tax :</td>
                    <td class="summary-currency">Rp</td>
                    <td class="summary-value">
                        {{ $invoice->tax > 0 ? number_format($invoice->tax, 2, ',', '.') : '-' }}
                    </td>
                </tr>
                <tr class="grand-line">
                    <td class="summary-label">GRAND TOTAL :</td>
                    <td class="summary-currency">Rp</td>
                    <td class="summary-value">{{ number_format($invoice->grand_total, 2, ',', '.') }}</td>
                </tr>
            </table>

            <div class="bottom-section" style="margin-top: 25px;">

                <table width="100%" style="border-collapse: collapse;">
                    <tr>
                        {{-- LEFT 75% --}}
                        <td width="70%" valign="top">

                            {{-- TERBILANG --}}
                            <div style="margin-bottom: 15px;">
                                <div style="font-weight: bold; margin-bottom: 6px;">
                                    TERBILANG :
                                </div>

                                @php
                                    use App\Support\Terbilang;
                                @endphp

                                <div
                                    style="padding: 5px; text-align: center; background-color: #c5c5c5; color: #484749; font-weight: bold;">
                                    {{ ucwords(Terbilang::make($invoice->grand_total)) }} Rupiah
                                </div>
                            </div>

                            {{-- PAYMENT + BANK --}}
                            <table width="100%" style="border-collapse: collapse;">
                                <tr>
                                    {{-- PAYMENT --}}
                                    <td width="50%" valign="top">
                                        <div style="font-weight: bold; margin-bottom: 6px;">
                                            PAYMENT TERMS:
                                        </div>
                                        <div style="font-size: 8px;">One Week After the Invoice is Sent</div>
                                        <div style="font-size: 8px;">(Satu Minggu Setelah Invoice Terkirim)</div>
                                    </td>

                                    {{-- BANK --}}
                                    <td width="50%" valign="top">
                                        <div style="font-weight: bold; margin-bottom: 6px;">
                                            BANK INFO
                                        </div>

                                        <table style="font-size: 8px;">
                                            <tr>
                                                <td>Bank Name</td>
                                                <td>: BCA Bank</td>
                                            </tr>
                                            <tr>
                                                <td>Account Holder</td>
                                                <td>: GEOTAMA GLOBAL INTIJAYA</td>
                                            </tr>
                                            <tr>
                                                <td>Account Number</td>
                                                <td>: 0614631313</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                        </td>

                        {{-- RIGHT 25% --}}
                        <td width="30%" valign="top" align="center">

                            <div style="height: 70px;"></div>

                            <div style="border-bottom: 1px solid #444; height: 60px;"></div>

                            <div style="margin: 10px 8px; font-weight: bold;">
                                ADAM SAPUTRA
                            </div>

                        </td>
                    </tr>
                </table>

            </div>

            <div class="thanks-box">
                Thank You For Your Business With Us!
            </div>
        </div>
    </div>
</body>

</html>
