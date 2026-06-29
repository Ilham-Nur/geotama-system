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
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div>
                        <h6 class="text-medium mb-1">List Proyek</h6>
                        <small class="text-muted">
                            {{ $proyeks->count() }} proyek terdaftar
                        </small>
                    </div>

                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <label for="projectStatusFilter" class="mb-0 text-muted">Status</label>
                        <select id="projectStatusFilter" class="form-select" style="width: 170px;">
                            <option value="">Semua Status</option>
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}">
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="table-wrapper table-responsive">
                    <table class="table" id="tableProyek">
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
                                <th>Tanggal Dibuat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $statusOrder = [
                                    'progress' => 1,
                                    'reporting' => 2,
                                    'endorse' => 3,
                                    'close' => 4,
                                ];
                            @endphp
                            @foreach ($proyeks as $proyek)
                                <tr>
                                    {{-- Nomor --}}
                                    <td>
                                        <strong>{{ $proyek->no_proyek }}</strong>
                                    </td>

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
                                    <td data-order="{{ $statusOrder[$proyek->status] ?? 5 }}" data-status="{{ $proyek->status }}">
                                        @if ($proyek->status == 'progress')
                                            <span class="badge bg-warning">PROGRESS</span>
                                        @elseif ($proyek->status == 'reporting')
                                            <span class="badge bg-info">REPORTING</span>
                                        @elseif ($proyek->status == 'endorse')
                                            <span class="badge bg-primary">ENDORSE</span>
                                        @elseif ($proyek->status == 'close')
                                            <span class="badge bg-success">SELESAI</span>
                                        @else
                                            <span class="badge bg-secondary">{{ strtoupper($proyek->status) }}</span>
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
                                    <td>{{ optional($proyek->created_at)->timestamp ?? 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const table = $('#tableProyek').DataTable({
                order: [
                    [6, 'asc'],
                    [8, 'desc']
                ],
                columnDefs: [{
                        targets: 7,
                        orderable: false,
                        searchable: false
                    },
                    {
                        targets: 8,
                        visible: false,
                        searchable: false
                    }
                ],
                language: {
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ proyek',
                    info: 'Menampilkan _START_ - _END_ dari _TOTAL_ proyek',
                    infoEmpty: 'Belum ada proyek untuk ditampilkan',
                    infoFiltered: '(difilter dari _MAX_ proyek)',
                    emptyTable: 'Belum ada data proyek.',
                    zeroRecords: 'Tidak ada proyek yang sesuai dengan pencarian.',
                    paginate: {
                        first: 'Pertama',
                        last: 'Terakhir',
                        next: 'Berikutnya',
                        previous: 'Sebelumnya'
                    }
                }
            });

            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'tableProyek') {
                    return true;
                }

                const selectedStatus = $('#projectStatusFilter').val();
                const row = table.row(dataIndex).node();
                const rowStatus = $(row).find('td[data-status]').data('status');

                return !selectedStatus || rowStatus === selectedStatus;
            });

            $('#projectStatusFilter').on('change', function() {
                table.draw();
            });
        });
    </script>
@endpush
