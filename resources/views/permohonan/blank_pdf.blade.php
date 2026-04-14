<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Permohonan</title>
    <style>
        @page {
            margin: 20px 20px 25px 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #000;
        }

        .outer-table,
        .table,
        .header-table,
        .sign-table {
            width: 100%;
            border-collapse: collapse;
        }

        .outer-table td,
        .outer-table th,
        .table td,
        .table th,
        .header-table td,
        .header-table th,
        .sign-table td,
        .sign-table th {
            border: 1px solid #000;
            padding: 4px 6px;
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
            letter-spacing: 0.5px;
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
            font-weight: bold;
            background: #f2f2f2;
        }

        .label-cell {
            width: 180px;
        }

        .colon-cell {
            width: 10px;
            text-align: center;
        }

        .checkbox {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 1px solid #000;
            text-align: center;
            line-height: 9px;
            font-size: 9px;
            margin-right: 4px;
        }

        .signature-box {
            height: 70px;
        }

        .small {
            font-size: 9px;
        }

        .mt-5 {
            margin-top: 5px;
        }

        .text-top {
            vertical-align: top;
        }

        .logo-box {
            width: 90px;
            text-align: center;
        }

        .logo-img {
            width: 65px;
            height: auto;
        }
    </style>
</head>

<body>

    <table class="outer-table">
        <tr>
            <td style="padding: 0;">

                {{-- HEADER --}}
                <table class="header-table">
                    <tr>
                        <td rowspan="3" class="logo-box middle" style="width: 90px;">
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
                        <td class="center middle title-form">PERMOHONAN JASA INSPEKSI</td>
                    </tr>
                    <tr>
                        <td class="center middle subtitle">APPLICATION FOR INSPECTION SERVICES</td>
                    </tr>
                </table>

                {{-- A --}}
                <table class="table">
                    <tr>
                        <td colspan="3" class="section-title">A. INFORMASI PEMOHON / CUSTOMER INFORMATION</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Nomor / Number</td>
                        <td class="colon-cell">:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="label-cell">Nama Perusahaan / Company Name</td>
                        <td class="colon-cell">:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Alamat / Address</td>
                        <td class="colon-cell">:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Nama PIC / Contact Person</td>
                        <td class="colon-cell">:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>No. Telepon / Phone Number</td>
                        <td class="colon-cell">:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td class="colon-cell">:</td>
                        <td></td>
                    </tr>
                </table>

                {{-- B --}}
                <table class="table">
                    <tr>
                        <td colspan="3" class="section-title">B. INFORMASI PEKERJAAN / JOB INFORMATION</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Tujuan Uji / Test Objective</td>
                        <td class="colon-cell">:</td>
                        <td>
                            <span class="checkbox"></span> Quality Internal
                            &nbsp;&nbsp;&nbsp;
                            <span class="checkbox"></span> Quality External
                            (........................................)
                        </td>
                    </tr>
                    <tr>
                        <td>Lokasi Pekerjaan / Work Location</td>
                        <td class="colon-cell">:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Nama Proyek / Project Name</td>
                        <td class="colon-cell">:</td>
                        <td></td>
                    </tr>
                </table>

                {{-- TABLE ITEM --}}
                <table class="table">
                    <thead>
                        <tr class="center bold">
                            <th style="width: 35px;">No.</th>
                            <th>Detail Pekerjaan<br><span class="small">Job Detail</span></th>
                            <th style="width: 180px;">Jenis Layanan NDT<br><span class="small">NDT Service
                                    Needed</span></th>
                            <th style="width: 110px;">Tanggal Permintaan<br><span class="small">Request Date</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 1; $i <= 5; $i++)
                            <tr>
                                <td class="center"></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>

                {{-- C --}}
                <table class="table">
                    <tr>
                        <td class="section-title">C. DOKUMEN PENDUKUNG / SUPPORTING DOCUMENTS</td>
                    </tr>
                    <tr>
                        <td>
                            <div><span class="checkbox"></span> Drawing</div>
                            <div><span class="checkbox"></span> P&amp;ID / Isometric</div>
                            <div><span class="checkbox"></span> WPS / PQR</div>
                            <div><span class="checkbox"></span> Standard / Standar</div>
                            <div><span class="checkbox"></span> Photo / Foto</div>
                            <div><span class="checkbox"></span> Schedule</div>
                            <div>
                                <span class="checkbox"></span>
                                Others / Lainnya :
                                ........................................
                            </div>
                        </td>
                    </tr>
                </table>

                {{-- D --}}
                <table class="table">
                    <tr>
                        <td class="section-title">D. PERMINTAAN KHUSUS / SPECIAL REQUEST</td>
                    </tr>
                    <tr>
                        <td style="height: 55px;"></td>
                    </tr>
                </table>

                {{-- E --}}
                <table class="table">
                    <tr>
                        <td class="section-title">E. PERNYATAAN PEMOHON / APPLICANT DECLARATION</td>
                    </tr>
                    <tr>
                        <td>
                            Dengan ini kami menyatakan bahwa informasi yang diberikan benar dan dapat digunakan
                            sebagai dasar evaluasi pekerjaan.<br>
                            <span class="subtitle">
                                We hereby declare that the information provided is true and can be used as the basis
                                for job evaluation.
                            </span>
                        </td>
                    </tr>
                </table>

                {{-- SIGN --}}
                <table class="sign-table">
                    <tr>
                        <td style="width: 50%;">
                            <strong>Diajukan oleh / Submitted by,</strong><br><br>
                            Nama / Name : ___________________________<br>
                            Tanda Tangan / Signature :
                            <div class="signature-box"></div>
                            (______________________)
                        </td>
                        <td style="width: 50%;">
                            <strong>Disetujui / Approved by</strong><br><br>
                            Nama / Name : ___________________________<br>
                            Tanda Tangan / Signature :
                            <div class="signature-box"></div>
                            (______________________)
                        </td>
                    </tr>
                </table>

                {{-- FOOT --}}
                <table class="table">
                    <tr>
                        <td class="no-border"></td>
                        <td class="no-border" style="text-align: right;">
                            GGI-F1-REV-2
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>

</html>
