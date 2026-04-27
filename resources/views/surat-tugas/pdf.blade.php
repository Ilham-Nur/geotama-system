<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Tugas {{ $suratTugas->id }}</title>
    <style>
        @page {
            margin: 24px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #222;
            font-size: 11px;
            margin: 0;
        }

        .first-page {
            min-height: 1120px;
            position: relative;
        }

        .first-header img {
            width: 100%;
            height: 115px;
            object-fit: cover;
        }

        .first-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }

        .first-footer img {
            width: 100%;
            height: 85px;
            object-fit: cover;
        }

        .first-content {
            padding: 18px 6px 120px;
        }

        h1,
        h2,
        h3,
        h4,
        p {
            margin: 0;
        }

        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .section {
            margin-bottom: 12px;
        }

        .label {
            font-weight: bold;
            width: 150px;
            display: inline-block;
            vertical-align: top;
        }

        .value {
            display: inline-block;
            width: calc(100% - 160px);
            vertical-align: top;
        }

        .list-pic {
            margin: 0;
            padding-left: 18px;
        }

        .list-pic li {
            margin-bottom: 4px;
        }

        .barcode-box {
            margin-top: 18px;
            border: 1px solid #d8d8d8;
            border-radius: 8px;
            padding: 10px;
        }

        .barcode-img {
            margin-top: 10px;
            text-align: center;
        }

        .barcode-img img {
            width: 110px;
            height: 110px;
            border: 1px solid #d8d8d8;
            padding: 4px;
        }

        .barcode-note {
            margin-top: 8px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .page-break {
            page-break-before: always;
        }

        .account-page {
            padding: 10px 4px;
        }

        .account-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .biaya-table th,
        .biaya-table td {
            border: 1px solid #777;
            padding: 6px;
        }

        .biaya-table th {
            background: #f3f3f3;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .terbilang {
            margin-top: 8px;
            font-weight: bold;
        }

        .sign {
            margin-top: 30px;
            width: 100%;
        }

        .sign .right {
            width: 45%;
            margin-left: auto;
            text-align: left;
        }

        .name {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    @php
        use App\Support\Terbilang;

        function localBase64Image($relativePath)
        {
            $fullPath = public_path($relativePath);

            if (!file_exists($fullPath)) {
                return null;
            }

            $img = imagecreatefromstring(file_get_contents($fullPath));

            if (!$img) {
                return null;
            }

            imagealphablending($img, false);
            imagesavealpha($img, true);

            ob_start();
            imagepng($img);
            $data = ob_get_clean();

            imagedestroy($img);

            return 'data:image/png;base64,' . base64_encode($data);
        }

        $headerBase64 = localBase64Image('template/assets/images/header_snipingtool.png');
        $footerBase64 = localBase64Image('template/assets/images/footer_snipingtool-removebg.png');
        $permohonan = $suratTugas->proyek?->permohonan;
        $pics = $suratTugas->proyek?->users ?? collect();
    @endphp

    <div class="first-page">
        <div class="first-header">
            @if ($headerBase64)
                <img src="{{ $headerBase64 }}" alt="Header">
            @endif
        </div>

        <div class="first-content">
            <div class="title">SURAT TUGAS</div>

            <div class="section">
                <span class="label">Ditugaskan kepada</span>
                <span class="value">:
                    <ol class="list-pic">
                        @forelse($pics as $pic)
                            <li>
                                {{ $pic->name }}
                                @if ($pic->employee?->position)
                                    ({{ $pic->employee->position }})
                                @endif
                            </li>
                        @empty
                            <li>-</li>
                        @endforelse
                    </ol>
                </span>
            </div>

            <div class="section"><span class="label">Untuk pergi ke</span><span class="value">: {{ $permohonan?->lokasi ?? '-' }}</span></div>
            <div class="section"><span class="label">Keperluan dinas survey</span><span class="value">: Pengujian NDT {{ $permohonan?->nama_perusahaan ? '(' . $permohonan->nama_perusahaan . ' - ' . ($permohonan->nama_proyek ?? '-') . ')' : '' }}</span></div>
            <div class="section"><span class="label">Berangkat</span><span class="value">: {{ optional($suratTugas->tanggal_berangkat)->format('d-m-Y') }}</span></div>
            <div class="section"><span class="label">Kembali</span><span class="value">: {{ optional($suratTugas->tanggal_kembali)->format('d-m-Y') }}</span></div>
            <div class="section"><span class="label">Sarana transportasi</span><span class="value">: {{ $suratTugas->transportasi }}</span></div>
            <div class="section"><span class="label">Keterangan</span><span class="value">: {{ $suratTugas->keterangan ?? '-' }}</span></div>

            <div class="barcode-box">
                <div><strong>Dikeluarkan di</strong> : Batam</div>
                <div><strong>Pada tanggal</strong> : {{ optional($suratTugas->created_at)->format('d F Y') }}</div>
                <div><strong>PT. GEOTAMA GLOBAL INTI JAYA</strong></div>

                <div class="barcode-img">
                    @if ($qrBase64)
                        <img src="{{ $qrBase64 }}" alt="Barcode Surat Tugas">
                    @else
                        <div>[BARCODE]</div>
                    @endif
                </div>

                <div class="barcode-note">
                    Scan barcode untuk melihat halaman tanda tangan digital surat tugas.
                </div>
            </div>
        </div>

        <div class="first-footer">
            @if ($footerBase64)
                <img src="{{ $footerBase64 }}" alt="Footer">
            @endif
        </div>
    </div>

    @foreach ($pics as $pic)
        @php
            $suffix = chr(64 + $loop->iteration);
            $namaPerusahaan = $permohonan?->nama_perusahaan ?? '-';
            $namaProyek = $permohonan?->nama_proyek ?? '-';
        @endphp
        <div class="page-break account-page">
            <div class="account-title">PERTANGGUNG JAWABAN<br>PELAKSANAAN PERJALANAN SURVEY</div>

            <div class="section"><span class="label">Nama</span><span class="value">: {{ $pic->name }}</span></div>
            <div class="section"><span class="label">No Surat Tugas</span><span class="value">: {{ $suratTugas->id }}-{{ $suffix }}</span></div>
            <div class="section"><span class="label">Tugas ke</span><span class="value">: {{ $permohonan?->lokasi ?? '-' }}</span></div>
            <div class="section"><span class="label">Berangkat</span><span class="value">: {{ optional($suratTugas->tanggal_berangkat)->format('d-m-Y') }}</span></div>
            <div class="section"><span class="label">Kembali</span><span class="value">: {{ optional($suratTugas->tanggal_kembali)->format('d-m-Y') }}</span></div>
            <div class="section"><span class="label">Telah dilaksanakan survey</span><span class="value">: Pengujian NDT ({{ $namaPerusahaan }} - {{ $namaProyek }})</span></div>

            <table class="biaya-table" style="margin-top: 12px;">
                <thead>
                    <tr>
                        <th width="8%">No</th>
                        <th>Rincian Biaya</th>
                        <th width="12%">Qty</th>
                        <th width="25%">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suratTugas->biayaItems as $item)
                        <tr>
                            <td class="text-right">{{ $loop->iteration }}</td>
                            <td>{{ $item->deskripsi }}</td>
                            <td class="text-right">{{ $item->qty }}</td>
                            <td class="text-right">Rp {{ number_format((float) $item->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-right">Tidak ada rincian biaya.</td>
                        </tr>
                    @endforelse
                    <tr>
                        <td colspan="3" class="text-right"><strong>Grand Total</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format((float) $suratTugas->grand_total, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>

            <div class="terbilang">Terbilang: {{ ucwords(Terbilang::make($suratTugas->grand_total)) }} Rupiah</div>

            <div class="sign">
                <div class="right">
                    Batam, {{ optional($suratTugas->created_at)->format('d F Y') }}<br>
                    <br>
                    <div class="name">{{ $pic->name }}</div>
                </div>
            </div>
        </div>
    @endforeach
</body>

</html>
