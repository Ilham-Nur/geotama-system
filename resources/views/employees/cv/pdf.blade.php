<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CV {{ $employee->full_name }}</title>
    <style>
        @page { margin: 34px 42px 38px; }
        body { color: #263238; font-family: DejaVu Sans, sans-serif; font-size: 10px; line-height: 1.45; }
        .header { border-bottom: 3px solid #1f5f8b; padding-bottom: 14px; margin-bottom: 18px; width: 100%; }
        .header td { vertical-align: top; }
        .name { color: #163d5a; font-size: 22px; font-weight: bold; margin: 0 0 3px; text-transform: uppercase; }
        .position { color: #4c697c; font-size: 12px; font-weight: bold; margin-bottom: 8px; }
        .contact { color: #455a64; font-size: 9px; }
        .photo { border: 1px solid #cfd8dc; height: 128px; object-fit: cover; width: auto; }
        .section { margin-bottom: 15px; }
        .section-title { border-bottom: 1px solid #90a4ae; color: #1f5f8b; font-size: 11px; font-weight: bold; letter-spacing: .5px; margin-bottom: 8px; padding-bottom: 3px; text-transform: uppercase; }
        .item { margin-bottom: 9px; page-break-inside: avoid; }
        .item-title { font-size: 10px; font-weight: bold; }
        .item-meta { color: #607d8b; font-size: 9px; }
        .description { margin-top: 2px; white-space: pre-line; }
        .personal-table { width: 100%; }
        .personal-table td { padding: 3px 8px 3px 0; vertical-align: top; }
        .personal-label { color: #607d8b; font-weight: bold; width: 18%; }
        .personal-value { width: 32%; }
        .empty { color: #90a4ae; font-style: italic; }
        .footer { bottom: 12px; color: #90a4ae; font-size: 8px; left: 42px; position: fixed; right: 42px; text-align: center; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                <div class="name">{{ $employee->full_name }}</div>
                <div class="position">{{ $employee->position ?: 'Karyawan' }}</div>
                @if ($sections->contains('contact'))
                    <div class="contact">
                        @if ($employee->phone)Telepon: {{ $employee->phone }}<br>@endif
                        @if ($employee->user?->email)Email: {{ $employee->user->email }}<br>@endif
                        @if ($employee->nationality)Kewarganegaraan: {{ $employee->nationality }}@endif
                    </div>
                @endif
            </td>
            @if ($photoDataUri)
                <td style="text-align: right; width: auto;">
                    <img src="{{ $photoDataUri }}" class="photo" alt="Foto">
                </td>
            @endif
        </tr>
    </table>

    @if ($sections->contains('personal'))
        <div class="section">
            <div class="section-title">Informasi Personal</div>
            <table class="personal-table">
                <tr>
                    <td class="personal-label">Tempat, Tgl Lahir</td>
                    <td class="personal-value">{{ collect([$employee->birth_place, optional($employee->birth_date)->format('d M Y')])->filter()->join(', ') ?: '-' }}</td>
                    <td class="personal-label">Jenis Kelamin</td>
                    <td class="personal-value">{{ $employee->gender ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="personal-label">Kode Karyawan</td>
                    <td class="personal-value">{{ $employee->employee_code }}</td>
                    <td class="personal-label">Bergabung</td>
                    <td class="personal-value">{{ optional($employee->hire_date)->format('d M Y') ?: '-' }}</td>
                </tr>
            </table>
        </div>
    @endif

    @if ($sections->contains('education'))
        <div class="section">
            <div class="section-title">Pendidikan Terakhir</div>
            @forelse ($employee->educations->sortByDesc('end_year') as $education)
                <div class="item">
                    <div class="item-title">{{ collect([$education->education_level, $education->major])->filter()->join(' - ') ?: $education->institution_name }}</div>
                    <div class="item-meta">
                        {{ $education->institution_name }}
                        @if ($education->start_year || $education->end_year || $education->is_current)
                            | {{ $education->start_year ?: '?' }} - {{ $education->is_current ? 'Sekarang' : ($education->end_year ?: '?') }}
                        @endif
                        @if ($education->grade) | Nilai/IPK: {{ $education->grade }}@endif
                    </div>
                    @if ($education->description)<div class="description">{{ $education->description }}</div>@endif
                </div>
            @empty
                <div class="empty">Riwayat pendidikan belum tersedia.</div>
            @endforelse
        </div>
    @endif

    @if ($sections->contains('work_experiences'))
        <div class="section">
            <div class="section-title">Pengalaman Kerja</div>
            @forelse ($latestWorkExperiences as $experience)
                <div class="item">
                    <div class="item-title">{{ $experience->position ?: 'Posisi tidak dicantumkan' }}</div>
                    <div class="item-meta">{{ $experience->company_name }} | {{ $experience->start_year ?: '?' }} - {{ $experience->is_current ? 'Sekarang' : ($experience->end_year ?: '?') }}</div>
                </div>
            @empty
                <div class="empty">Pengalaman kerja belum tersedia.</div>
            @endforelse
        </div>
    @endif

    @if ($sections->contains('certificates'))
        <div class="section">
            <div class="section-title">Sertifikat</div>
            @forelse ($employee->certificates->sortByDesc('issued_at') as $certificate)
                <div class="item">
                    <div class="item-title">{{ $certificate->certificate_name }}</div>
                    <div class="item-meta">
                        {{ $certificate->issuer ?: 'Penerbit tidak dicantumkan' }}
                        @if ($certificate->issued_at) | Terbit {{ $certificate->issued_at->format('d M Y') }}@endif
                        @if ($certificate->expired_at) | Berlaku hingga {{ $certificate->expired_at->format('d M Y') }}@endif
                    </div>
                </div>
            @empty
                <div class="empty">Sertifikat belum tersedia.</div>
            @endforelse
        </div>
    @endif

    @if ($sections->contains('projects'))
        <div class="section">
            <div class="section-title">Pengalaman Proyek</div>
            @forelse ($projects as $project)
                <div class="item">
                    <div class="item-title">{{ $project->permohonan?->nama_proyek ?? $project->no_proyek }}</div>
                    <div class="item-meta">
                        {{ $project->permohonan?->nama_perusahaan ?: 'Klien tidak dicantumkan' }}
                        @if ($project->permohonan?->lokasi) | {{ $project->permohonan->lokasi }}@endif
                    </div>
                    @if ($project->deskripsi)<div class="description">{{ $project->deskripsi }}</div>@endif
                </div>
            @empty
                <div class="empty">Proyek belum dipilih atau belum tersedia.</div>
            @endforelse
        </div>
    @endif

    <div class="footer">Curriculum Vitae {{ $employee->full_name }} | Dibuat {{ now()->format('d M Y') }}</div>
</body>
</html>
