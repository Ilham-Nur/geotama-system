<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Surat Tugas Approval Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background-color:#0b3d91;padding:10px;">
    @php
        $permohonan = $suratTugas->proyek?->permohonan;
        $picNames = $suratTugas->proyek?->users?->pluck('name')->join(', ');
    @endphp

    <div style="max-width:800px;margin:20px auto;border:1px solid #dee2e6;border-radius:12px;padding:25px 20px;background:#fff;box-shadow:0 8px 24px rgba(0,0,0,.12)">
        <div style="text-align:center;margin-bottom:25px;padding-bottom:20px;border-bottom:2px solid #e9ecef;">
            <img src="{{ asset('template/assets/images/logo/logo-geotama-removebg-preview.png') }}" alt="Logo" style="height:70px;margin-bottom:15px;">
            <h3 style="font-size:1.4rem;font-weight:700;margin:0;color:#0b3d91;">Verifikasi Tanda Tangan Elektronik</h3>
        </div>

        <table class="table table-borderless">
            <tr>
                <td width="35%"><strong>Nomor Dokumen</strong></td>
                <td>: Surat Tugas {{ $suratTugas->id }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Dokumen</strong></td>
                <td>: {{ optional($suratTugas->created_at)->format('d F Y') }}</td>
            </tr>
            <tr>
                <td><strong>Nama Perusahaan</strong></td>
                <td>: {{ $permohonan?->nama_perusahaan ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Lokasi Survey</strong></td>
                <td>: {{ $permohonan?->lokasi ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Person In Charge</strong></td>
                <td>: {{ $picNames ?: '-' }}</td>
            </tr>
            <tr>
                <td><strong>Perihal</strong></td>
                <td>: Persetujuan Surat Tugas</td>
            </tr>
            <tr>
                <td><strong>Pengirim</strong></td>
                <td>: {{ $approval['approver_name'] ?? '-' }} | {{ $approval['approver_position'] ?? '-' }}</td>
            </tr>
        </table>

        <div style="border:1px solid #ced4da;padding:15px;margin-top:20px;border-radius:8px;background:#f8f9fa;">
            Ditandatangani secara elektronik oleh:
            <strong>{{ $approval['approver_name'] ?? '-' }}</strong>, {{ $approval['approver_position'] ?? '-' }}
            <br>PT. GEOTAMA GLOBAL INTIJAYA
        </div>

        <div style="border:1px solid #ced4da;padding:15px;margin-top:20px;border-radius:8px;background:#f8f9fa;line-height:1.5;">
            Dokumen surat tugas ini telah ditandatangani secara elektronik menggunakan sistem digital signature
            pada {{ $approval['approval_date'] ?? '-' }}.
        </div>
    </div>
</body>

</html>
