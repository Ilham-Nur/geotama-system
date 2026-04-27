<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Tugas {{ $suratTugas->id }}</title>
    <style>
        @page {
            margin: 170px 25px 90px 25px;
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
            position: fixed;
            top: -170px;
            left: -180;
            right: 0;
            height: 140px;
            width: 180%;
        }

        .first-footer {
            position: fixed;
            bottom: -70px;
            left: -190;
            right: 0;
            height: 90px;
            width: 180%;
        }

        .first-footer img {
            width: 100%;
            height: 110px;
            object-fit: cover;
        }

        .first-content {
            padding: 18px 6px 140px;
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
            padding: 0 4px 10px;
        }

        .account-title {
            text-align: center;
            font-size: 13px;
            letter-spacing: .4px;
            font-weight: bold;
            margin: 8px 0 8px;
        }

        .account-title-line {
            border-top: 2px solid #7a8ea5;
            margin-bottom: 12px;
        }

        .statement-table td {
            padding: 4px 0;
            vertical-align: top;
            font-size: 11px;
        }

        .statement-table .no {
            width: 6%;
            text-align: center;
        }

        .statement-table .field {
            width: 38%;
        }

        .statement-table .colon {
            width: 4%;
            text-align: center;
        }

        .statement-table .content {
            width: 52%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .biaya-list {
            margin-top: 4px;
        }

        .biaya-list td {
            padding: 2px 0;
            font-size: 11px;
        }

        .biaya-list .dash {
            width: 4%;
            text-align: center;
        }

        .biaya-list .item-name {
            width: 54%;
        }

        .biaya-list .rp {
            width: 8%;
            text-align: center;
        }

        .biaya-list .amount {
            width: 34%;
            text-align: right;
            text-decoration: underline;
            font-weight: 500;
        }

        .summary-line {
            border-top: 1.5px solid #777;
            margin-top: 4px;
            padding-top: 4px;
        }

        .text-right {
            text-align: right;
        }

        .terbilang {
            margin-top: 12px;
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

            <div class="section"><span class="label">Untuk pergi ke</span><span class="value">:
                    {{ $permohonan?->lokasi ?? '-' }}</span></div>
            <div class="section"><span class="label">Keperluan dinas survey</span><span class="value">: Pengujian NDT
                    {{ $permohonan?->nama_perusahaan ? '(' . $permohonan->nama_perusahaan . ' - ' . ($permohonan->nama_proyek ?? '-') . ')' : '' }}</span>
            </div>
            <div class="section"><span class="label">Berangkat</span><span class="value">:
                    {{ optional($suratTugas->tanggal_berangkat)->format('d-m-Y') }}</span></div>
            <div class="section"><span class="label">Kembali</span><span class="value">:
                    {{ optional($suratTugas->tanggal_kembali)->format('d-m-Y') }}</span></div>
            <div class="section"><span class="label">Sarana transportasi</span><span class="value">:
                    {{ $suratTugas->transportasi }}</span></div>
            <div class="section"><span class="label">Keterangan</span><span class="value">:
                    {{ $suratTugas->keterangan ?? '-' }}</span></div>

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
            <div class="account-title-line"></div>

            <table class="statement-table">
                <tr>
                    <td class="no">1</td>
                    <td class="field">Nama</td>
                    <td class="colon">:</td>
                    <td class="content">{{ $pic->name }}</td>
                </tr>
                <tr>
                    <td class="no">2</td>
                    <td class="field">No. Surat Tugas</td>
                    <td class="colon">:</td>
                    <td class="content">{{ $suffix }}. {{ $suratTugas->id }}</td>
                </tr>
                <tr>
                    <td class="no">3</td>
                    <td class="field">Tugas ke</td>
                    <td class="colon">:</td>
                    <td class="content">{{ $permohonan?->lokasi ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="no">4</td>
                    <td class="field">Berangkat Tanggal</td>
                    <td class="colon">:</td>
                    <td class="content">{{ optional($suratTugas->tanggal_berangkat)->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="no">5</td>
                    <td class="field">Kembali Tanggal</td>
                    <td class="colon">:</td>
                    <td class="content">{{ optional($suratTugas->tanggal_kembali)->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="no">6</td>
                    <td class="field">Telah dilaksanakan Survey</td>
                    <td class="colon">:</td>
                    <td class="content"><strong>Pengujian NDT {{ $namaPerusahaan }} ({{ $namaProyek }})</strong>
                    </td>
                </tr>
                <tr>
                    <td class="no">7</td>
                    <td class="field">Terlampir Form ST</td>
                    <td class="colon">:</td>
                    <td class="content">Form ST-1 / Form ST-2 yang telah diisi oleh petugas</td>
                </tr>
                <tr>
                    <td class="no">8</td>
                    <td class="field">Perincian biaya dikeluarkan</td>
                    <td class="colon">:</td>
                    <td class="content">
                        <table class="biaya-list">
                            @forelse ($suratTugas->biayaItems as $item)
                                <tr>
                                    <td class="dash">-</td>
                                    <td class="item-name">{{ $item->deskripsi }}</td>
                                    <td class="rp">Rp</td>
                                    <td class="amount">{{ number_format((float) $item->total, 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="dash">-</td>
                                    <td class="item-name">Belum ada rincian biaya</td>
                                    <td class="rp"></td>
                                    <td class="amount">0,00</td>
                                </tr>
                            @endforelse
                            <tr>
                                <td colspan="2" class="text-right summary-line"><strong>Jumlah</strong></td>
                                <td class="rp summary-line">Rp</td>
                                <td class="amount summary-line">
                                    <strong>{{ number_format((float) $suratTugas->grand_total, 2, ',', '.') }}</strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <div class="terbilang">Terbilang : <em>{{ strtoupper(Terbilang::make($suratTugas->grand_total)) }}
                    RUPIAH</em></div>

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
