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
