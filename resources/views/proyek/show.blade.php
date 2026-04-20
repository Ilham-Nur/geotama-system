@extends('layouts.app')

@section('title', 'Detail Proyek')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1">Detail Proyek</h3>
                <p class="text-muted mb-0">Informasi lengkap proyek</p>
            </div>

            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                Kembali
            </a>
        </div>

        <div class="row">
            {{-- INFORMASI UTAMA --}}
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Proyek</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>No Proyek</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $proyek->no_proyek ?? '-' }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Nama Proyek</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $proyek->permohonan->nama_proyek ?? '-' }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Client</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $proyek->permohonan->nama_perusahaan ?? '-' }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Lokasi</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $proyek->permohonan->lokasi ?? '-' }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Status</strong>
                            </div>
                            <div class="col-md-8">
                                @if ($proyek->status == 'progress')
                                    <span class="badge bg-warning">Progress</span>
                                @elseif ($proyek->status == 'reporting')
                                    <span class="badge bg-info">Reporting</span>
                                @elseif ($proyek->status == 'endorse')
                                    <span class="badge bg-primary">Endorse</span>
                                @elseif ($proyek->status == 'close')
                                    <span class="badge bg-success">Close</span>
                                @else
                                    <span class="badge bg-secondary">{{ $proyek->status }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-4">
                                <strong>Deskripsi</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $proyek->deskripsi ?: '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PEKERJAAN --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Detail Pekerjaan</h5>
                    </div>
                    <div class="card-body">
                        @if ($proyek->permohonan && $proyek->permohonan->items->count())
                            <div class="table-wrapper table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;">
                                                <h6>No</h6>
                                            </th>
                                            <th>
                                                <h6>Detail Pekerjaan</h6>
                                            </th>
                                            <th>
                                                <h6>Pekerjaan / Layanan</h6>
                                            </th>
                                            <th>
                                                <h6>Tanggal Pelaksanaan</h6>
                                            </th>
                                            <th>
                                                <h6>Durasi</h6>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($proyek->permohonan->items as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->detail_pekerjaan ?? '-' }}</td>
                                                <td>
                                                    @if ($item->layanans && $item->layanans->count())
                                                        @foreach ($item->layanans->unique('id') as $layanan)
                                                            <a href="{{ route('proyek.pekerjaan.show', [$proyek->id, $item->id, $layanan->id]) }}"
                                                                class="badge bg-info text-decoration-none me-1">
                                                                {{ $layanan->nama }}
                                                            </a>
                                                        @endforeach
                                                    @else
                                                        -
                                                    @endif
                                                </td>

                                                <td>
                                                    {{ $item->tanggal_pelaksanaan ?? '-' }}
                                                </td>

                                                <td>
                                                    {{ $item->durasi ?? '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="mb-0 text-muted">Belum ada detail pekerjaan.</p>
                        @endif
                    </div>
                </div>

                {{-- DOKUMEN --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Dokumen Permohonan</h5>
                    </div>
                    <div class="card-body">
                        @if ($proyek->permohonan && $proyek->permohonan->dokumens->count())
                            <div class="table-wrapper table-responsive">
                                <table class="table ">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;">
                                                <h6>No</h6>
                                            </th>
                                            <th>
                                                <h6>Nama Dokumen</h6>
                                            </th>
                                            <th>
                                                <h6>File</h6>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($proyek->permohonan->dokumens as $dokumen)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $dokumen->label ?? 'Dokumen' }}</td>
                                                <td class="text-center">
                                                    @if (!empty($dokumen->file_path) && file_exists(storage_path('app/public/' . $dokumen->file_path)))
                                                        <a href="{{ asset('storage/' . $dokumen->file_path) }}"
                                                            target="_blank" class="btn btn-sm btn-primary">
                                                            Lihat File
                                                        </a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="mb-0 text-muted">Belum ada dokumen.</p>
                        @endif
                    </div>
                </div>

                {{-- TIMESHEET HARDCOPY --}}
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Timesheet Hardcopy</h5>
                        <span class="badge bg-dark">{{ $proyek->timesheets->count() }} Form</span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('proyek.timesheet.store', $proyek->id) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    Generate & Print Form Timesheet
                                </button>
                            </div>
                        </form>

                        @if ($proyek->timesheets->count())
                            @foreach ($proyek->timesheets->sortByDesc('created_at') as $timesheet)
                                <div class="border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between flex-wrap gap-2">
                                        <div>
                                            <strong>Form Timesheet Inspeksi</strong>
                                            <div class="text-muted small">
                                                No Form: GGI-F2-2026-REV 1
                                            </div>
                                            <div class="text-muted small">
                                                Dibuat oleh: {{ $timesheet->generator->name ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            @php
                                                $statusClass = match ($timesheet->status) {
                                                    'generated' => 'bg-secondary',
                                                    'in_field' => 'bg-primary',
                                                    'uploaded_partial' => 'bg-warning text-dark',
                                                    'completed' => 'bg-success',
                                                    'verified' => 'bg-dark',
                                                    default => 'bg-info text-dark',
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }} text-uppercase d-block mb-2">{{ str_replace('_', ' ', $timesheet->status) }}</span>
                                            <a href="{{ route('proyek.timesheet.export-pdf', [$proyek->id, $timesheet->id]) }}"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                Export Template PDF
                                            </a>
                                        </div>
                                    </div>

                                    @if ($timesheet->verified_at)
                                        <p class="mb-3 mt-2 text-success small">
                                            Diverifikasi oleh {{ $timesheet->verifier->name ?? '-' }} pada
                                            {{ $timesheet->verified_at?->format('d M Y H:i') }}.
                                        </p>
                                    @endif

                                    <form action="{{ route('proyek.timesheet.hardcopy.upload', [$proyek->id, $timesheet->id]) }}" method="POST"
                                        enctype="multipart/form-data" class="row g-2 align-items-end mb-3">
                                        @csrf
                                        <div class="col-md-3">
                                            <label class="form-label">Upload hardcopy</label>
                                            <input type="file" name="hardcopy_file"
                                                class="form-control @error('hardcopy_file') is-invalid @enderror"
                                                accept=".pdf,.jpg,.jpeg,.png" required>
                                            @error('hardcopy_file')
                                                <small class="invalid-feedback">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Tanggal Timesheet</label>
                                            <input type="date" name="work_date" class="form-control @error('work_date') is-invalid @enderror"
                                                value="{{ old('work_date') }}" required>
                                            @error('work_date')
                                                <small class="invalid-feedback">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Durasi (hari)</label>
                                            <input type="number" min="1" name="duration_days"
                                                class="form-control @error('duration_days') is-invalid @enderror"
                                                value="{{ old('duration_days', 1) }}" required>
                                            @error('duration_days')
                                                <small class="invalid-feedback">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Catatan upload (opsional)</label>
                                            <input type="text" name="notes" class="form-control @error('notes') is-invalid @enderror"
                                                value="{{ old('notes') }}" placeholder="Contoh: halaman 1 dan 2">
                                            @error('notes')
                                                <small class="invalid-feedback">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-2 d-grid">
                                            <button type="submit" class="btn btn-success">Upload</button>
                                        </div>
                                    </form>

                                    @if ($timesheet->uploads->count() > 0 && $timesheet->status !== 'verified')
                                        <form action="{{ route('proyek.timesheet.verify', [$proyek->id, $timesheet->id]) }}" method="POST"
                                            class="mb-3 text-end">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-dark">Verifikasi Timesheet</button>
                                        </form>
                                    @endif

                                    @if ($timesheet->uploads->count())
                                        <div class="table-responsive">
                                            <table class="table table-sm mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Versi</th>
                                                        <th>File</th>
                                                        <th>Uploader</th>
                                                        <th>Tanggal</th>
                                                        <th>Tanggal Timesheet</th>
                                                        <th>Durasi Hari</th>
                                                        <th>Catatan</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($timesheet->uploads as $upload)
                                                        <tr>
                                                            <td>v{{ $upload->version_no }}</td>
                                                            <td>
                                                                <a href="{{ asset('storage/' . $upload->file_path) }}" target="_blank">
                                                                    {{ $upload->file_name }}
                                                                </a>
                                                            </td>
                                                            <td>{{ $upload->uploader->name ?? '-' }}</td>
                                                            <td>{{ $upload->created_at?->format('d M Y H:i') ?? '-' }}</td>
                                                            <td>{{ $upload->work_date?->format('d M Y') ?? '-' }}</td>
                                                            <td>{{ $upload->duration_days ?? '-' }}</td>
                                                            <td>{{ $upload->notes ?? '-' }}</td>
                                                            <td style="min-width: 260px;">
                                                                <form action="{{ route('proyek.timesheet.hardcopy.update', [$proyek->id, $timesheet->id, $upload->id]) }}"
                                                                    method="POST" enctype="multipart/form-data" class="mb-2">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <div class="input-group input-group-sm mb-1">
                                                                        <input type="text" class="form-control" name="notes"
                                                                            value="{{ $upload->notes }}"
                                                                            placeholder="Edit catatan">
                                                                    </div>
                                                                    <div class="input-group input-group-sm mb-1">
                                                                        <input type="date" class="form-control" name="work_date"
                                                                            value="{{ $upload->work_date?->format('Y-m-d') }}">
                                                                        <input type="number" min="1" class="form-control"
                                                                            name="duration_days" value="{{ $upload->duration_days }}"
                                                                            placeholder="Durasi hari">
                                                                    </div>
                                                                    <div class="input-group input-group-sm">
                                                                        <input type="file" class="form-control" name="hardcopy_file"
                                                                            accept=".pdf,.jpg,.jpeg,.png">
                                                                        <button class="btn btn-outline-primary" type="submit">Update</button>
                                                                    </div>
                                                                </form>

                                                                <form action="{{ route('proyek.timesheet.hardcopy.delete', [$proyek->id, $timesheet->id, $upload->id]) }}"
                                                                    method="POST" onsubmit="return confirm('Yakin hapus dokumen ini?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                                        Hapus Dokumen
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="mb-0 text-muted small">Belum ada upload hardcopy.</p>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <p class="mb-0 text-muted">Belum ada form timesheet. Klik <strong>Generate Form</strong> untuk membuat form baru.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- SIDEBAR --}}
            <div class="col-md-4">
                {{-- PIC --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">PIC Proyek</h5>
                    </div>
                    <div class="card-body">
                        @if ($proyek->users->count())
                            <ul class="list-group">
                                @foreach ($proyek->users as $user)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $user->name }}</strong><br>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mb-0 text-muted">Belum ada PIC.</p>
                        @endif
                    </div>
                </div>

                {{-- INFO PERMOHONAN --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Info Permohonan</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>No Permohonan</strong><br>
                            <span>{{ $proyek->permohonan->nomor ?? '-' }}</span>
                        </div>

                        <div class="mb-3">
                            <strong>PIC Client</strong><br>
                            <span>{{ $proyek->permohonan->nama_pic ?? '-' }}</span>
                        </div>

                        <div class="mb-3">
                            <strong>No Telp</strong><br>
                            <span>{{ $proyek->permohonan->no_telp ?? '-' }}</span>
                        </div>

                        <div class="mb-3">
                            <strong>Email</strong><br>
                            <span>{{ $proyek->permohonan->email ?? '-' }}</span>
                        </div>

                        <div class="mb-0">
                            <strong>Permintaan Khusus</strong><br>
                            <span>{{ $proyek->permohonan->permintaan_khusus ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- AKSI --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Aksi</h5>
                    </div>
                    <div class="card-body d-grid gap-2">
                        <a href="#" class="btn btn-warning">Edit Proyek</a>
                        <a href="#" class="btn btn-primary">Ubah Status</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
