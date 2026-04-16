@extends('layouts.app')

@section('title', 'Detail PAK')

@section('content')

    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2> Detail PAK</h2>
                </div>
            </div>
            <!-- end col -->
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('pak.index') }}">PAK</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Detail PAK
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

    {{-- ========================= --}}
    {{-- HEADER --}}
    {{-- ========================= --}}
    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Detail PAK</h4>
                <small class="text-muted">{{ $pak->pak_number }}</small>
            </div>

            <div>
                <a href="{{ route('pak.export-pdf', $pak->id) }}" target="_blank" class="btn btn-danger btn-sm">
                    <i class="lni lni-printer"></i> Export PDF
                </a>
                <a href="{{ route('pak.index') }}" class="btn btn-secondary btn-sm">
                    <i class="lni lni-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- DATA PERMOHONAN --}}
    {{-- ========================= --}}
    <div class="card mb-3">
        <div class="card-header"><b>Data Permohonan</b></div>
        <div class="card-body">

            <div class="row">
                <div class="col-md-6"><b>Perusahaan:</b> {{ $permohonan->nama_perusahaan ?? '-' }}</div>
                <div class="col-md-6"><b>PIC:</b> {{ $permohonan->nama_pic ?? '-' }}</div>
                <div class="col-md-6"><b>No Telp:</b> {{ $permohonan->no_telp ?? '-' }}</div>
                <div class="col-md-6"><b>Email:</b> {{ $permohonan->email ?? '-' }}</div>
                <div class="col-md-12"><b>Alamat:</b> {{ $permohonan->alamat ?? '-' }}</div>
                <div class="col-md-6"><b>Lokasi:</b> {{ $permohonan->lokasi ?? '-' }}</div>
                <div class="col-md-6"><b>Proyek:</b> {{ $permohonan->nama_proyek ?? '-' }}</div>
                <div class="col-md-12"><b>Permintaan:</b> {{ $permohonan->permintaan_khusus ?? '-' }}</div>
            </div>

        </div>
    </div>

    {{-- ========================= --}}
    {{-- DETAIL PEKERJAAN --}}
    {{-- ========================= --}}
    <div class="card mb-3">
        <div class="card-header"><b>Detail Pekerjaan</b></div>
        <div class="card-body table-responsive">

            <table class="table table-bordered">
                <thead class="table-light text-center">
                    <tr>
                        <th width="50">No</th>
                        <th>Detail Pekerjaan</th>
                        <th>Layanan</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($permohonan->items ?? [] as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>

                            <td>{{ $item->detail_pekerjaan }}</td>

                            <td>
                                {{ collect($item->layanans)->pluck('id')->map(fn($id) => $layanans[$id] ?? '-')->implode(', ') }}
                            </td>

                            <td>{{ $item->tanggal_permintaan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

    {{-- ========================= --}}
    {{-- DOKUMEN --}}
    {{-- ========================= --}}
    <div class="card mb-3">
        <div class="card-header"><b>Dokumen</b></div>
        <div class="card-body">

            @forelse($permohonan->dokumens ?? [] as $doc)
                <div class="mb-2">
                    <i class="lni lni-file"></i>
                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank">
                        {{ $doc->file_name }}
                    </a>
                </div>
            @empty
                <i>Tidak ada dokumen</i>
            @endforelse

        </div>
    </div>

    {{-- ========================= --}}
    {{-- BIAYA --}}
    {{-- ========================= --}}
    <div class="card mb-3">
        <div class="card-header"><b>Perhitungan Biaya</b></div>

        <div class="card-body table-responsive">

            <table class="table table-bordered">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Kategori</th>
                        <th>Nama</th>
                        <th>Qty</th>
                        <th>Unit Cost</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($pak->items->groupBy('category.name') as $category => $items)
                        <tr class="table-secondary">
                            <td colspan="5"><b>{{ $category }}</b></td>
                        </tr>

                        @foreach ($items as $item)
                            <tr>
                                <td></td>
                                <td>{{ $item->name }}</td>
                                <td class="text-center">{{ $item->qty }}</td>
                                <td>Rp {{ number_format($item->unit_cost, 0, ',', '.') }}</td>
                                <td class="fw-bold">Rp {{ number_format($item->total_cost, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @endforeach

                </tbody>
            </table>

        </div>
    </div>

    {{-- ========================= --}}
    {{-- SUMMARY --}}
    {{-- ========================= --}}
    <div class="card">
        <div class="card-body">

            <div class="row text-center">

                <div class="col-md-2">
                    <small>Nilai Project</small>
                    <h6>Rp {{ number_format($pak->project_value, 0, ',', '.') }}</h6>
                </div>

                <div class="col-md-2">
                    <small>Total Cost</small>
                    <h6>Rp {{ number_format($pak->total_cost, 0, ',', '.') }}</h6>
                </div>

                <div class="col-md-2">
                    <small>Pajak</small>
                    <h6>Rp {{ number_format($pak->tax ?? 0, 0, ',', '.') }}</h6>
                </div>

                <div class="col-md-3">
                    <small>Total + Pajak</small>
                    <h6>Rp {{ number_format(($pak->total_cost ?? 0) + ($pak->tax ?? 0), 0, ',', '.') }}</h6>
                </div>

                <div class="col-md-2">
                    <small>Profit</small>
                    <h6 class="{{ $pak->profit < 0 ? 'text-danger' : 'text-success' }}">
                        Rp {{ number_format($pak->profit, 0, ',', '.') }}
                    </h6>
                </div>

                <div class="col-md-1">
                    <small>Margin</small>
                    <h6 class="{{ $pak->profit_percentage < 10 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($pak->profit_percentage, 1) }}%
                    </h6>
                </div>

            </div>

            <div class="text-end mt-3">
                <button class="btn btn-success btn-convert" data-id="{{ $pak->id }}">
                    Convert ke Permohonan
                </button>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
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

                        if (res.success) {
                            Swal.fire('Berhasil!', res.message, 'success')
                                .then(() => {
                                    window.location.href = res.redirect;
                                });
                        } else {
                            Swal.fire('Error!', res.message, 'error');
                        }
                    },
                    error: function(xhr) {

                        let msg = xhr.responseJSON?.message || 'Terjadi kesalahan server';

                        Swal.fire('Error!', msg, 'error');
                    }
                });

            });

        });
    </script>
@endpush
