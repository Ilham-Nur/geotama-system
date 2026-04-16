@extends('layouts.app')

@section('title', 'Data PAK')

@section('content')
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2>PAK (Proposal Anggaran Kerja)</h2>
                </div>
            </div>

            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">
                                PAK
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- ========== title-wrapper end ========== -->




    <div class="card">
        <div class="card-body">

            <div class="title d-flex flex-wrap align-items-center justify-content-between">
                <div class="left">
                    <h6 class="text-medium mb-30">List PAK</h6>
                </div>

                <div class="col-md-6 mb-30 text-end">
                    @can('pak.create')
                        <a href="{{ route('pak.create') }}" class="btn btn-primary">
                            + Tambah PAK
                        </a>
                    @endcan
                </div>
            </div>


            <div class="table-wrapper table-responsive">
                <table id="tablePAK" class="table table-striped table-hover align-middle">

                    <thead class="table text-center">
                        <tr>
                            <th>
                                <h6>No PAK</h6>
                            </th>
                            <th>
                                <h6>Nama Proyek</h6>
                            </th>
                            <th>
                                <h6>Perusahaan</h6>
                            </th>
                            <th>
                                <h6>Nilai Proyek</h6>
                            </th>
                            <th>
                                <h6>Total Cost</h6>
                            </th>
                            <th>
                                <h6>Profit</h6>
                            </th>
                            <th>
                                <h6>%</h6>
                            </th>
                            <th width="120">
                                <h6>Aksi</h6>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($paks as $row)
                            @php
                                $raw = $row->permohonan_data;

                                // kalau masih string → decode lagi
                                if (is_string($raw)) {
                                    $raw = json_decode($raw, true);
                                }

                                $p = (object) $raw;
                            @endphp

                            <tr>

                                {{-- NO PAK --}}
                                <td>
                                    {{ $row->pak_number }}
                                </td>

                                {{-- NAMA PROYEK --}}
                                <td>
                                    {{ $p->nama_proyek ?? '-' }}
                                </td>

                                {{-- PERUSAHAAN --}}
                                <td>
                                    {{ $p->nama_perusahaan ?? '-' }}
                                </td>

                                {{-- NILAI PROJECT --}}
                                <td class="text-end">
                                    Rp {{ number_format($row->project_value ?? 0, 0, ',', '.') }}
                                </td>

                                {{-- TOTAL COST --}}
                                <td class="text-end">
                                    Rp {{ number_format($row->total_cost ?? 0, 0, ',', '.') }}
                                </td>

                                {{-- PROFIT --}}
                                <td class="text-end">
                                    @if ($row->profit < 0)
                                        <span class="text-danger fw-bold">
                                            - Rp {{ number_format(abs($row->profit), 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-success fw-bold">
                                            Rp {{ number_format($row->profit, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </td>

                                {{-- PERCENT --}}
                                <td class="text-center">
                                    <span class="badge bg-{{ $row->profit_percentage < 10 ? 'danger' : 'success' }}">
                                        {{ number_format($row->profit_percentage ?? 0, 1) }}%
                                    </span>
                                </td>

                                {{-- AKSI --}}
                                <td>
                                    <div class="d-flex justify-content-center gap-2">

                                        {{-- DETAIL --}}
                                        <a href="{{ route('pak.show', $row->id) }}" class="text-info" title="Detail">
                                            <i class="lni lni-eye"></i>
                                        </a>

                                        <a href="{{ route('pak.export-pdf', $row->id) }}" class="text-danger"
                                            title="Export PDF" target="_blank">
                                            <i class="lni lni-printer"></i>
                                        </a>

                                        {{-- EDIT --}}
                                        @can('pak.edit')
                                            <a href="{{ route('pak.edit', $row->id) }}" class="text-primary"
                                                title="Edit">
                                                <i class="lni lni-pencil"></i>
                                            </a>
                                        @endcan

                                        {{-- CONVERT --}}
                                        @can('pak.convert')
                                            <button type="button" class="border-0 bg-transparent p-0 text-success btn-convert"
                                                data-id="{{ $row->id }}" title="Convert">
                                                <i class="lni lni-reload"></i>
                                            </button>
                                        @endcan

                                    </div>
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    Tidak ada data PAK
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            <div class="mt-3">
                {{ $paks->links() }}
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#tablePAK').DataTable();
        });


        $(document).on('click', '.btn-convert', function() {

            let id = $(this).data('id');

            Swal.fire({
                title: 'Convert ke Permohonan?',
                text: 'Data PAK akan dijadikan permohonan',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Convert'
            }).then((result) => {

                if (!result.isConfirmed) return;

                $.ajax({
                    url: '/pak/' + id + '/convert',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        window.location.href = "{{ route('permohonan.index') }}";
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal convert data', 'error');
                    }
                });

            });

        });
    </script>
@endpush
