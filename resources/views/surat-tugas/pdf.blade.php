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
            font-size: 13px;
            color: #222;
            margin: 0;
        }

        /* WATERMARK */
        .watermark {
            position: fixed;
            top: 29%;
            left: 45%;
            width: 640px;
            transform: translate(-50%, -50%);
            opacity: 0.10;
            z-index: -1;
        }

        /* HEADER (FIXED - VERSI BENAR) */
        .first-header img {
            position: fixed;
            top: -170px;
            left: -240px;
            right: 0;
            width: 180%;
            height: 140px;
        }

        /* FOOTER (FIXED - VERSI BENAR) */
        .first-footer {
            position: fixed;
            bottom: -70px;
            left: -190px;
            right: 0;
            width: 180%;
        }

        .first-footer img {
            width: 100%;
            height: 110px;
            object-fit: cover;
        }

        .first-content {
            padding: 30px 40px 140px;
        }

        /* TITLE */
        .title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 3px;
            margin-bottom: 25px;
        }

        .section {
            margin-bottom: 8px;
        }

        .label {
            display: inline-block;
            width: 180px;
            vertical-align: top;
        }

        .value {
            display: inline-block;
            width: calc(100% - 170px);
        }

        .list-pic {
            margin: 0;
            padding-left: 18px;
        }

        /* SIGNATURE */
        .sign {
            margin-top: 70px;
            width: 100%;
        }

        .sign .right {
            width: 40%;
            margin-left: auto;
            text-align: left;
        }

        .name {
            margin-top: 40px;
            font-weight: bold;
            text-decoration: underline;
        }

        /* PAGE BREAK */
        .page-break {
            page-break-before: always;
            padding: 20px 40px;
        }

        /* HALAMAN 2 */
        .account-title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            margin: 10px 0;
        }

        .account-title-line {
            border-top: 2px solid #000;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .statement-table td {
            padding: 8px 0;
            vertical-align: top;
        }

        .no {
            width: 6%;
            text-align: center;
        }

        .field {
            width: 38%;
        }

        .colon {
            width: 4%;
            text-align: center;
        }

        .content {
            width: 52%;
        }

        /* BIAYA */
        .biaya-list td {
            padding: 3px 0;
        }

        .dash {
            width: 4%;
        }

        .item-name {
            width: 54%;
        }

        .rp {
            width: 8%;
            text-align: center;
        }

        .amount {
            width: 34%;
            text-align: right;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }

        .summary-line {
            border-top: 2px solid #000;
        }

        .terbilang {
            margin-top: 15px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    @php
        use App\Support\Terbilang;

        function localBase64Image($path)
        {
            $fullPath = public_path($path);
            if (!file_exists($fullPath)) {
                return null;
            }
            return 'data:image/png;base64,' . base64_encode(file_get_contents($fullPath));
        }

        $headerBase64 = localBase64Image('template/assets/images/header_snipingtool.png');
        $footerBase64 = localBase64Image('template/assets/images/footer_snipingtool-removebg.png');
        $watermarkBase64 = localBase64Image('template/assets/images/logo/logo-geotama-removebg-preview.png');

        $permohonan = $suratTugas->proyek?->permohonan;
        $pics = $suratTugas->proyek?->users ?? collect();
    @endphp

    <!-- ================= HALAMAN 1 ================= -->
    <div class="first-page">

        @if ($watermarkBase64)
            <img src="{{ $watermarkBase64 }}" class="watermark">
        @endif

        <div class="first-header">
            @if ($headerBase64)
                <img src="{{ $headerBase64 }}">
            @endif
        </div>

        <div class="first-content">

            <div class="title">SURAT TUGAS</div>

            <table style="width:100%; border-collapse:collapse; font-size:13px;">

                <tr>
                    <td style="width:30%; vertical-align:top;">Ditugaskan kepada</td>
                    <td style="width:3%;">:</td>
                    <td>
                        @foreach ($pics as $pic)
                            {{ $pic->name }}@if (!$loop->last)
                                ,
                            @endif
                        @endforeach
                    </td>
                </tr>

                <tr>
                    <td>Untuk pergi ke</td>
                    <td>:</td>
                    <td>{{ $permohonan->lokasi ?? '-' }}</td>
                </tr>

                <tr>
                    <td>Keperluan</td>
                    <td>:</td>
                    <td>Pengujian NDT {{ $permohonan->nama_perusahaan ?? '' }}</td>
                </tr>

                <tr>
                    <td>Berangkat</td>
                    <td>:</td>
                    <td>{{ optional($suratTugas->tanggal_berangkat)->format('d F Y') }}</td>
                </tr>

                <tr>
                    <td>Kembali</td>
                    <td>:</td>
                    <td>{{ optional($suratTugas->tanggal_kembali)->format('d F Y') }}</td>
                </tr>

                <tr>
                    <td>Transportasi</td>
                    <td>:</td>
                    <td>{{ $suratTugas->transportasi }}</td>
                </tr>

                <tr>
                    <td>Keterangan</td>
                    <td>:</td>
                    <td>{{ $suratTugas->keterangan }}</td>
                </tr>

            </table>

            <!-- SIGN + BARCODE -->
            <div class="sign">
                <div class="right">
                    Dikeluarkan di : Batam<br>
                    Pada Tanggal : {{ optional($suratTugas->created_at)->format('d F Y') }}<br>
                    <strong>PT. GEOTAMA GLOBAL INTI JAYA</strong>

                    <br><br>

                    @if ($qrBase64)
                        <img src="{{ $qrBase64 }}" style="width:90px;">
                    @endif

                    <div class="name">WAHYUDI KUSUMA</div>
                    <div style="font-size:10px;">DIRECTOR</div>
                </div>
            </div>

        </div>

        <div class="first-footer">
            @if ($footerBase64)
                <img src="{{ $footerBase64 }}">
            @endif
        </div>

    </div>

    <!-- ================= HALAMAN 2 ================= -->
    @foreach ($pics as $pic)
        <div class="page-break">

            <div class="account-title">
                PERTANGGUNG JAWABAN<br>
                PELAKSANAAN PERJALANAN SURVEY
            </div>

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
                    <td class="field">No Surat</td>
                    <td class="colon">:</td>
                    <td class="content">{{ $suratTugas->id }}</td>
                </tr>
                <tr>
                    <td class="no">3</td>
                    <td class="field">Tugas ke</td>
                    <td class="colon">:</td>
                    <td class="content">{{ $permohonan->lokasi ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="no">4</td>
                    <td class="field">Berangkat</td>
                    <td class="colon">:</td>
                    <td class="content">{{ optional($suratTugas->tanggal_berangkat)->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="no">5</td>
                    <td class="field">Kembali</td>
                    <td class="colon">:</td>
                    <td class="content">{{ optional($suratTugas->tanggal_kembali)->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="no">6</td>
                    <td class="field">Survey</td>
                    <td class="colon">:</td>
                    <td class="content"><strong>Pengujian NDT {{ $permohonan->nama_perusahaan }}</strong></td>
                </tr>

                <tr>
                    <td class="no">8</td>
                    <td class="field">Biaya</td>
                    <td class="colon">:</td>
                    <td class="content">
                        <table class="biaya-list">
                            @foreach ($suratTugas->biayaItems as $item)
                                <tr>
                                    <td class="dash">-</td>
                                    <td class="item-name">{{ $item->deskripsi }}</td>
                                    <td class="rp">Rp</td>
                                    <td class="amount">{{ number_format($item->total, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach

                            <tr>
                                <td colspan="2" class="summary-line"><strong>Jumlah</strong></td>
                                <td class="rp summary-line">Rp</td>
                                <td class="amount summary-line">
                                    <strong>{{ number_format($suratTugas->grand_total, 2, ',', '.') }}</strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <div class="terbilang">
                Terbilang : {{ strtoupper(Terbilang::make($suratTugas->grand_total)) }} RUPIAH
            </div>

            <div class="sign">
                <div class="right">
                    Batam, {{ optional($suratTugas->created_at)->format('d F Y') }}<br><br>
                    <div class="name">{{ $pic->name }}</div>
                </div>
            </div>

        </div>
    @endforeach

</body>

</html>
