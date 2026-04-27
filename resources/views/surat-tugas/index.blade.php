@extends('layouts.app')

@section('title', 'Surat Tugas')

@section('content')
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2>Surat Tugas</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Surat Tugas</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card-style mb-30">
                <div class="title d-flex flex-wrap align-items-center justify-content-between">
                    <h6 class="text-medium mb-30">List Surat Tugas</h6>

                    @can('surat_tugas.create')
                        <a href="{{ route('surat-tugas.create') }}" class="btn btn-primary mb-30">+ Tambah Surat Tugas</a>
                    @endcan
                </div>

                <div class="table-wrapper table-responsive">
                    <table id="tableSuratTugas" class="table">
                        <thead>
                            <tr>
                                <th>Proyek</th>
                                <th>Tgl Berangkat</th>
                                <th>Tgl Kembali</th>
                                <th>Transportasi</th>
                                <th>Grand Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($suratTugas as $surat)
                                <tr>
                                    <td>{{ $surat->proyek?->no_proyek ?? '-' }}</td>
                                    <td>{{ $surat->tanggal_berangkat?->format('d-m-Y') }}</td>
                                    <td>{{ $surat->tanggal_kembali?->format('d-m-Y') }}</td>
                                    <td>{{ $surat->transportasi }}</td>
                                    <td>Rp {{ number_format($surat->grand_total, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                data-bs-target="#detailSuratModal{{ $surat->id }}">Detail</button>
                                            <a href="{{ route('surat-tugas.export-pdf', $surat) }}" class="btn btn-sm btn-danger"
                                                target="_blank">PDF</a>

                                            @can('surat_tugas.edit')
                                                <a href="{{ route('surat-tugas.edit', $surat) }}" class="btn btn-sm btn-warning">Edit</a>
                                            @endcan

                                            @can('surat_tugas.delete')
                                                <form method="POST" action="{{ route('surat-tugas.destroy', $surat) }}"
                                                    class="form-delete-surat">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada data surat tugas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $suratTugas->links() }}
                </div>
            </div>
        </div>
    </div>

    @foreach ($suratTugas as $surat)
        <div class="modal fade" id="detailSuratModal{{ $surat->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Surat Tugas - {{ $surat->proyek?->no_proyek ?? '-' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @php
                            $permohonan = $surat->proyek?->permohonan;
                        @endphp

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Nama Perusahaan</label>
                                <div class="fw-semibold">{{ $permohonan?->nama_perusahaan ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">PC Proyek</label>
                                <div class="fw-semibold">{{ $surat->proyek?->users?->pluck('name')->join(', ') ?: '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Alamat</label>
                                <div class="fw-semibold">{{ $permohonan?->alamat ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Lokasi</label>
                                <div class="fw-semibold">{{ $permohonan?->lokasi ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Test Uji</label>
                                <div class="fw-semibold">
                                    @if ($permohonan?->testuji === 'quality_internal')
                                        Quality Internal
                                    @elseif ($permohonan?->testuji === 'quality_external')
                                        Quality External
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Tanggal Berangkat</label>
                                <div class="fw-semibold">{{ $surat->tanggal_berangkat?->format('d-m-Y') }}</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Tanggal Kembali</label>
                                <div class="fw-semibold">{{ $surat->tanggal_kembali?->format('d-m-Y') }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Transportasi</label>
                                <div class="fw-semibold">{{ $surat->transportasi }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Keterangan</label>
                                <div class="fw-semibold">{{ $surat->keterangan ?? '-' }}</div>
                            </div>
                        </div>

                        <h6 class="mb-2">Detail Biaya</h6>
                        <div class="table-wrapper table-responsive mb-3">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Deskripsi</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($surat->biayaItems as $item)
                                        <tr>
                                            <td>{{ $item->deskripsi }}</td>
                                            <td>{{ $item->qty }}</td>
                                            <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Belum ada item biaya.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-end">Grand Total</th>
                                        <th>Rp {{ number_format($surat->grand_total, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.form-delete-surat').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Hapus surat tugas?',
                        text: 'Data yang dihapus tidak bisa dikembalikan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });

            @if (session('success'))
                Swal.fire('Berhasil', @json(session('success')), 'success');
            @endif
        });
    </script>
@endpush
