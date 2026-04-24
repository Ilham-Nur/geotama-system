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
                            <li class="breadcrumb-item active" aria-current="page">
                                Surat Tugas
                            </li>
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
                    <div class="left">
                        <h6 class="text-medium mb-30">List Surat Tugas</h6>
                    </div>

                    <div class="col-md-6 mb-30 text-end">
                        @can('surat_tugas.create')
                            <a href="{{ route('surat-tugas.create') }}" class="btn btn-primary">
                                + Tambah Surat Tugas
                            </a>
                        @endcan
                    </div>
                </div>

                <div class="table-wrapper table-responsive">
                    <table id="tableSuratTugas" class="table">
                        <thead>
                            <tr>
                                <th><h6>No Surat</h6></th>
                                <th><h6>No Proyek</h6></th>
                                <th><h6>Tanggal Berangkat</h6></th>
                                <th><h6>Tanggal Kembali</h6></th>
                                <th><h6>Transportasi</h6></th>
                                <th><h6>Grand Total</h6></th>
                                <th><h6>Aksi</h6></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($suratTugas as $item)
                                <tr>
                                    <td>{{ $item->no_surat }}</td>
                                    <td>{{ $item->proyek->no_proyek ?? '-' }}</td>
                                    <td>{{ optional($item->tanggal_berangkat)->format('d-m-Y') }}</td>
                                    <td>{{ optional($item->tanggal_kembali)->format('d-m-Y') }}</td>
                                    <td>{{ $item->transportasi ?? '-' }}</td>
                                    <td>Rp {{ number_format((float) $item->grand_total, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="d-flex gap-2 align-items-center">
                                            @can('surat_tugas.edit')
                                                <a href="{{ route('surat-tugas.edit', $item->id) }}" class="text-primary" title="Edit">
                                                    <i class="lni lni-pencil"></i>
                                                </a>
                                            @endcan

                                            @can('surat_tugas.delete')
                                                <form action="{{ route('surat-tugas.destroy', $item->id) }}" method="POST"
                                                    class="form-delete-surat-tugas">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-danger border-0 bg-transparent p-0 btn-delete-surat-tugas"
                                                        title="Hapus">
                                                        <i class="lni lni-trash-can"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada data surat tugas.</td>
                                </tr>
                            @endforelse
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
            $('#tableSuratTugas').DataTable();

            $('.btn-delete-surat-tugas').on('click', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');

                Swal.fire({
                    title: 'Hapus surat tugas ini?',
                    text: 'Data yang dihapus tidak bisa dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.trigger('submit');
                    }
                });
            });
        });
    </script>
@endpush
