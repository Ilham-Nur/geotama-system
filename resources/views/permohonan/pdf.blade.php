<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>
        Permohonan {{ $permohonan->nomor }}
        {{ $permohonan->items->contains(fn($item) => !empty($item->tanggal_pelaksanaan) || !empty($item->durasi)) ? ' Menyusul' : '' }}
    </title>
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
    @php
        $isPermohonanMenyusul = $permohonan->items->contains(function ($item) {
            return !empty($item->tanggal_pelaksanaan) || !empty($item->durasi);
        });
    @endphp

    <table class="outer-table">
        {{-- KOP --}}
        <tr>
            <td style="padding: 0;">
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
                        <td class="center middle title-form">
                            PERMOHONAN JASA INSPEKSI{{ $isPermohonanMenyusul ? ' MENYUSUL' : '' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="center middle subtitle">APPLICATION FOR INSPECTION SERVICES</td>
                    </tr>
                </table>

                {{-- A. INFORMASI PEMOHON --}}
                <table class="table">
                    <tr>
                        <td colspan="3" class="section-title">A. INFORMASI PEMOHON / CUSTOMER INFORMATION</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Nomor / Number</td>
                        <td class="colon-cell">:</td>
                        <td>{{ $permohonan->nomor }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Nama Perusahaan / Company Name</td>
                        <td class="colon-cell">:</td>
                        <td>{{ $permohonan->nama_perusahaan }}</td>
                    </tr>
                    <tr>
                        <td>Alamat / Address</td>
                        <td class="colon-cell">:</td>
                        <td>{{ $permohonan->alamat }}</td>
                    </tr>
                    <tr>
                        <td>Nama PIC / Contact Person</td>
                        <td class="colon-cell">:</td>
                        <td>{{ $permohonan->nama_pic }}</td>
                    </tr>
                    <tr>
                        <td>No. Telepon / Phone Number</td>
                        <td class="colon-cell">:</td>
                        <td>{{ $permohonan->no_telp }}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td class="colon-cell">:</td>
                        <td>{{ $permohonan->email ?: '-' }}</td>
                    </tr>
                </table>

                {{-- B. INFORMASI PEKERJAAN --}}
                <table class="table">
                    <tr>
                        <td colspan="3" class="section-title">B. INFORMASI PEKERJAAN / JOB INFORMATION</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Tujuan Uji / Test Objective</td>
                        <td class="colon-cell">:</td>
                        <td>
                            <span class="checkbox">{{ $permohonan->testuji == 'quality_internal' ? '✓' : '' }}</span>
                            Quality Internal
                            &nbsp;&nbsp;&nbsp;
                            <span class="checkbox">{{ $permohonan->testuji == 'quality_external' ? '✓' : '' }}</span>
                            Quality External
                            @if ($permohonan->testuji == 'quality_external' && $permohonan->testuji_external_keterangan)
                                ({{ $permohonan->testuji_external_keterangan }})
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Lokasi Pekerjaan / Work Location</td>
                        <td class="colon-cell">:</td>
                        <td>{{ $permohonan->lokasi }}</td>
                    </tr>
                    <tr>
                        <td>Nama Proyek / Project Name</td>
                        <td class="colon-cell">:</td>
                        <td>{{ $permohonan->nama_proyek }}</td>
                    </tr>
                </table>

                {{-- TABEL ITEM --}}
                <table class="table">
                    <thead>
                        <tr class="center bold">
                            <th style="width: 35px;">No.</th>
                            <th>Detail Pekerjaan<br><span class="small">Job Detail</span></th>
                            <th style="width: 180px;">Jenis Layanan NDT<br><span class="small">NDT Service
                                    Needed</span></th>
                            <th style="width: 110px;">Tanggal Permintaan<br><span class="small">Request Date</span>
                            </th>
                            @if ($isPermohonanMenyusul)
                                <th style="width: 110px;">Tanggal Pelaksanaan<br><span class="small">Requested Execution
                                        Date</span></th>
                                <th style="width: 80px;">Durasi Pekerjaan<br><span class="small">Duration of Work</span>
                                </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($permohonan->items as $item)
                            <tr>
                                <td class="center">{{ $loop->iteration }}</td>
                                <td>{{ $item->detail_pekerjaan }}</td>
                                <td class="center">{{ $item->layanans->pluck('nama')->implode(' / ') ?: '-' }}</td>
                                <td class="center">
                                    {{ $item->tanggal_permintaan ? \Carbon\Carbon::parse($item->tanggal_permintaan)->format('d M Y') : '-' }}
                                </td>
                                @if ($isPermohonanMenyusul)
                                    <td class="center">
                                        {{ $item->tanggal_pelaksanaan ? \Carbon\Carbon::parse($item->tanggal_pelaksanaan)->format('d M Y') : '-' }}
                                    </td>
                                    <td class="center">{{ $item->durasi ?: '-' }}</td>
                                @endif
                            </tr>
                        @empty
                            @for ($i = 1; $i <= 5; $i++)
                                <tr>
                                    <td class="center"></td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    @if ($isPermohonanMenyusul)
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    @endif
                                </tr>
                            @endfor
                        @endforelse

                        @if ($permohonan->items->count() < 5)
                            @for ($i = $permohonan->items->count() + 1; $i <= 5; $i++)
                                <tr>
                                    <td class="center"></td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    @if ($isPermohonanMenyusul)
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    @endif
                                </tr>
                            @endfor
                        @endif
                    </tbody>
                </table>

                {{-- C. DOKUMEN PENDUKUNG --}}
                <table class="table">
                    <tr>
                        <td class="section-title">C. DOKUMEN PENDUKUNG / SUPPORTING DOCUMENTS</td>
                    </tr>
                    <tr>
                        <td>
                            @php
                                $jenisDok = $permohonan->dokumens->pluck('jenis')->toArray();
                                $dokLainnya = optional($permohonan->dokumens->where('jenis', 'lainnya')->first())
                                    ->label;
                            @endphp

                            <div><span class="checkbox">{{ in_array('drawing', $jenisDok) ? '✓' : '' }}</span> Drawing
                            </div>
                            <div><span class="checkbox">{{ in_array('p_id_isometric', $jenisDok) ? '✓' : '' }}</span>
                                P&amp;ID / Isometric</div>
                            <div><span class="checkbox">{{ in_array('wps_pqr', $jenisDok) ? '✓' : '' }}</span> WPS /
                                PQR</div>
                            <div><span class="checkbox">{{ in_array('standar', $jenisDok) ? '✓' : '' }}</span> Standard
                                / Standar</div>
                            <div><span class="checkbox">{{ in_array('foto', $jenisDok) ? '✓' : '' }}</span> Photo /
                                Foto</div>
                            <div><span class="checkbox">{{ in_array('schedule', $jenisDok) ? '✓' : '' }}</span>
                                Schedule</div>
                            <div>
                                <span class="checkbox">{{ in_array('lainnya', $jenisDok) ? '✓' : '' }}</span>
                                Others / Lainnya :
                                {{ $dokLainnya ?: '........................................' }}
                            </div>
                        </td>
                    </tr>
                </table>

                {{-- D. PERMINTAAN KHUSUS --}}
                <table class="table">
                    <tr>
                        <td class="section-title">D. PERMINTAAN KHUSUS / SPECIAL REQUEST</td>
                    </tr>
                    <tr>
                        <td style="height: 55px;">{{ $permohonan->permintaan_khusus ?: '' }}</td>
                    </tr>
                </table>

                {{-- E. PERNYATAAN --}}
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

                {{-- TANDA TANGAN --}}
                <table class="sign-table">
                    <tr>
                        <td style="width: 50%;">
                            <strong>Diajukan oleh / Submitted by,</strong><br><br>
                            Nama / Name &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                            ___________________________<br>
                            Tanda Tangan / Signature &nbsp;:
                            <div class="signature-box"></div>
                            (______________________)
                        </td>
                        <td style="width: 50%;">
                            <strong>Disetujui / Approved by</strong><br><br>
                            Nama / Name &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                            ___________________________<br>
                            Tanda Tangan / Signature &nbsp;:
                            <div class="signature-box"></div>
                            (______________________)
                        </td>
                    </tr>
                </table>

                {{-- FOOT NOTE --}}
                <table class="table">
                    <tr>
                        <td class="no-border" style="text-align: left; padding-top: 10px;">
                            {{-- {{ $permohonan->nomor }} --}}
                        </td>
                        <td class="no-border" style="text-align: right; padding-top: 10px;">
                            GGI-F1-REV-2
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>

</html>
