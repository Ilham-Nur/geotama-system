<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $status_label }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.45;
            color: #111827;
        }

        h1,
        h2 {
            text-align: center;
            margin: 0;
        }

        h1 {
            font-size: 16px;
            margin-bottom: 4px;
        }

        h2 {
            font-size: 13px;
            margin-bottom: 14px;
        }

        .section-title {
            font-weight: bold;
            margin-top: 14px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            vertical-align: top;
            padding: 2px 4px;
        }

        .signature {
            width: 100%;
            margin-top: 28px;
        }

        .signature td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .placeholder {
            margin-top: 56px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h1>{{ $status_label }}</h1>
    <h2>No: {{ $contract_number }}</h2>

    <p>Pada hari ini, {{ \Carbon\Carbon::parse($signing_date)->translatedFormat('d F Y') }}, telah dibuat kesepakatan kerja antara:
    </p>

    <table class="meta-table">
        <tr>
            <td width="35%">Nama Perusahaan</td>
            <td width="5%">:</td>
            <td>PT. [Nama Perusahaan]</td>
        </tr>
        <tr>
            <td>Alamat Perusahaan</td>
            <td>:</td>
            <td>[Alamat Perusahaan]</td>
        </tr>
        <tr>
            <td>Penandatangan Perusahaan</td>
            <td>:</td>
            <td>[Nama Penanggung Jawab]</td>
        </tr>
    </table>

    <p>Selanjutnya disebut sebagai <strong>Pihak Pertama</strong>.</p>

    <table class="meta-table">
        <tr>
            <td width="35%">Nama Karyawan</td>
            <td width="5%">:</td>
            <td>{{ $employee->full_name }}</td>
        </tr>
        <tr>
            <td>NIK / Kode Karyawan</td>
            <td>:</td>
            <td>{{ $employee->employee_code }}</td>
        </tr>
        <tr>
            <td>No. Identitas</td>
            <td>:</td>
            <td>{{ $employee->identity_number ?: '-' }}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td>{{ $employee->full_address ?: '-' }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>{{ $employee->position ?: '-' }}</td>
        </tr>
    </table>

    <p>Selanjutnya disebut sebagai <strong>Pihak Kedua</strong>.</p>

    <div class="section-title">Pasal 1 - Ruang Lingkup Pekerjaan</div>
    <p>Pihak Kedua bersedia menjalankan tugas sesuai jabatan, SOP perusahaan, serta arahan atasan langsung dengan itikad baik.
    </p>

    <div class="section-title">Pasal 2 - Masa Berlaku</div>
    @if ($employee->employment_status === 'tetap')
        <p>Hubungan kerja berlaku sebagai karyawan tetap sejak
            <strong>{{ \Carbon\Carbon::parse($effective_date)->translatedFormat('d F Y') }}</strong>.</p>
    @else
        <p>Perjanjian berlaku sejak
            <strong>{{ \Carbon\Carbon::parse($contract_start_date)->translatedFormat('d F Y') }}</strong> sampai
            <strong>{{ \Carbon\Carbon::parse($contract_end_date)->translatedFormat('d F Y') }}</strong>.</p>
    @endif

    <div class="section-title">Pasal 3 - Hak & Kewajiban</div>
    <ol>
        <li>Pihak Pertama memberikan hak kerja sesuai kebijakan perusahaan dan ketentuan hukum yang berlaku.</li>
        <li>Pihak Kedua wajib menjaga disiplin, kerahasiaan data, dan mematuhi seluruh peraturan perusahaan.</li>
        <li>Pelanggaran berat dapat menjadi dasar evaluasi atau pengakhiran hubungan kerja sesuai peraturan.</li>
    </ol>

    <div class="section-title">Pasal 4 - Kompensasi</div>
    <p>
        @if ($salary)
            Pihak Kedua menerima kompensasi sebesar <strong>Rp {{ number_format((float) $salary, 0, ',', '.') }}</strong>
            per bulan (belum termasuk tunjangan lain sesuai kebijakan perusahaan).
        @else
            Besaran kompensasi/upah mengikuti keputusan manajemen dan ketentuan internal perusahaan yang berlaku.
        @endif
    </p>

    <div class="section-title">Pasal 5 - Penutup</div>
    <p>Dokumen ini dibuat dalam keadaan sadar, tanpa paksaan dari pihak manapun, dan berlaku sejak ditandatangani kedua belah
        pihak.</p>

    <table class="signature">
        <tr>
            <td>
                Pihak Pertama<br>
                PT. [Nama Perusahaan]
                <div class="placeholder">(_____________________)</div>
            </td>
            <td>
                Pihak Kedua<br>
                {{ $employee->full_name }}
                <div class="placeholder">(_____________________)</div>
            </td>
        </tr>
    </table>
</body>

</html>
