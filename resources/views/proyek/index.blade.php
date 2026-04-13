@extends('layouts.app')

@section('title', 'Proyek')

@section('content')
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2>Proyek</h2>
                </div>
            </div>
            <!-- end col -->
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            {{-- <li class="breadcrumb-item">
                                <a href="#0">Dashboard</a>
                            </li> --}}
                            <li class="breadcrumb-item active" aria-current="page">
                                Proyek
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    </div>
    <!-- ========== title-wrapper end ========== -->


    <div class="row mt-3">
        <div class="col-12">
            <div class="card-style mb-30">
                <div class="title d-flex flex-wrap align-items-center justify-content-between">
                    <div class="left">
                        <h6 class="text-medium mb-30">List Proyek</h6>
                    </div>

                    {{-- <div class="col-md-6 mb-30 text-end">
                        <a href="" class="btn btn-primary">
                            + Tambah Proyek
                        </a>
                    </div> --}}
                </div>

                <div class="table-wrapper table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>
                                    <h6>Nomor</h6>
                                </th>
                                <th>
                                    <h6>Nama Proyek</h6>
                                </th>
                                <th>
                                    <h6>Client</h6>
                                </th>
                                <th>
                                    <h6>Pekerjaan</h6>
                                </th>
                                <th>
                                    <h6>Lokasi</h6>
                                </th>
                                <th>
                                    <h6>PIC</h6>
                                </th>
                                <th>
                                    <h6>Status</h6>
                                </th>
                                <th>
                                    <h6>Aksi</h6>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($proyeks as $proyek)
                                <tr>
                                    {{-- Nomor --}}
                                    <td>{{ $proyek->no_proyek }}</td>

                                    {{-- Nama Proyek --}}
                                    <td>{{ $proyek->permohonan->nama_proyek ?? '-' }}</td>

                                    {{-- Client --}}
                                    <td>{{ $proyek->permohonan->nama_perusahaan ?? '-' }}</td>

                                    {{-- Pekerjaan --}}
                                    <td>
                                        {{ $proyek->permohonan->items->pluck('layanans')->flatten()->pluck('nama')->unique()->implode(', ') ?: '-' }}
                                    </td>

                                    {{-- Lokasi --}}
                                    <td>{{ $proyek->permohonan->lokasi ?? '-' }}</td>

                                    {{-- PIC (MULTIPLE) --}}
                                    <td>
                                        @foreach ($proyek->users as $user)
                                            <span class="badge bg-primary">
                                                {{ $user->name }}
                                            </span>
                                        @endforeach
                                    </td>

                                    {{-- Status --}}
                                    <td>
                                        @if ($proyek->status == 'progress')
                                            <span class="badge bg-warning">PROGRESS</span>
                                        @elseif ($proyek->status == 'reporting')
                                            <span class="badge bg-info">REPORTING</span>
                                        @elseif ($proyek->status == 'endorse')
                                            <span class="badge bg-primary">ENDORSE</span>
                                        @elseif ($proyek->status == 'close')
                                            <span class="badge bg-secondary">CLOSE</span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td>
                                        @can('proyek.show')
                                            <a href="{{ route('proyek.show', $proyek->id) }}" class="main-btn btn-sm info-btn">
                                                Detail
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Belum ada data invoice.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
