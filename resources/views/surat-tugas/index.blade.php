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
                                <th>Keterangan</th>
                                <th>Grand Total</th>
                                <th>Item Biaya</th>
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
                                    <td>{{ $surat->keterangan ?? '-' }}</td>
                                    <td>Rp {{ number_format($surat->grand_total, 0, ',', '.') }}</td>
                                    <td>{{ $surat->biayaItems->count() }} item</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            @can('surat_tugas.edit')
                                                <a href="{{ route('surat-tugas.edit', $surat) }}"
                                                    class="btn btn-sm btn-warning">Edit</a>
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
                                    <td colspan="8" class="text-center text-muted">Belum ada data surat tugas.</td>
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
