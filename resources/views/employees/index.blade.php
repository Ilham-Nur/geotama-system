@extends('layouts.app')

@section('title', 'Karyawan')

@section('content')
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2>Halaman Karyawan</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Karyawan</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card-style mb-30">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h6 class="mb-0">Daftar Karyawan</h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <form action="{{ route('employees.index') }}" method="GET" class="d-flex gap-2">
                            <input type="text" id="search-input" name="search" class="form-control"
                                placeholder="Cari nama / NIK / posisi" value="{{ $search }}">
                            <button type="submit" class="btn btn-outline-primary">Cari</button>
                        </form>
                        @can('employees.create')
                            <a href="{{ route('employees.create') }}" class="btn btn-primary">
                                <i class="lni lni-plus"></i> Tambah Karyawan
                            </a>
                        @endcan
                    </div>
                </div>

                @include('employees.partials.table', ['employees' => $employees])
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('.btn-delete-employee').on('click', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');

                Swal.fire({
                    title: 'Hapus karyawan ini?',
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
