@extends('layouts.app')

@section('title', 'Permohonan')

@section('content')
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2>Permohonan</h2>
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
                                Permohonan
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
                        <h6 class="text-medium mb-30">List Permohonan</h6>
                    </div>

                    <div class="col-md-6 mb-30 text-end">
                        <a href="{{ route('permohonan.blank-pdf') }}" class="btn btn-success" target="_blank">
                            <i class="lni lni-printer"></i> Form Blank PDF
                        </a>
                        @can('permohonan.create')
                            <a href="{{ route('permohonan.create') }}" class="btn btn-primary">
                                + Tambah Permohonan
                            </a>
                        @endcan
                    </div>
                </div>

                <div class="table-wrapper table-responsive">
                    <table id="tablePermohonan" class="table">
                        <thead>
                            <tr>
                                {{-- <th>No</th> --}}
                                <th>
                                    <h6>Nomor</h6>
                                </th>
                                <th>
                                    <h6>Nama Perusahaan</h6>
                                </th>
                                <th>
                                    <h6>Nama Proyek</h6>
                                </th>
                                <th>
                                    <h6>PIC</h6>
                                </th>
                                <th>
                                    <h6>Tanggal Dibuat</h6>
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
                            @forelse($permohonans as $row)
                                <tr>
                                    {{-- <td>{{ $loop->iteration }}</td> --}}
                                    <td>{{ $row->nomor }}</td>
                                    <td>{{ $row->nama_perusahaan }}</td>
                                    <td>{{ $row->nama_proyek }}</td>
                                    <td>{{ $row->nama_pic }}</td>
                                    <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d-m-Y') }}</td>
                                    <td>
                                        @if ($row->status == 'OPEN')
                                            <span class="status-btn warning-btn">OPEN</span>
                                        @else
                                            <span class="status-btn success-btn">CLOSE</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action d-flex align-items-center gap-2">
                                            <a href="{{ route('permohonan.show', $row->id) }}" class="text-info"
                                                title="Detail">
                                                <i class="lni lni-eye"></i>
                                            </a>

                                            @can('permohonan.edit')
                                                <a href="{{ route('permohonan.edit', $row->id) }}" class="text-primary"
                                                    title="Edit">
                                                    <i class="lni lni-pencil"></i>
                                                </a>
                                            @endcan

                                            <a href="{{ route('permohonan.export-pdf', $row->id) }}" class="text-warning"
                                                title="Export PDF" target="_blank">
                                                <i class="lni lni-download"></i>
                                            </a>

                                            @can('permohonan.delete')
                                                <form action="{{ route('permohonan.destroy', $row->id) }}" method="POST"
                                                    class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-danger border-0 bg-transparent p-0"
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
                                    <td colspan="7" class="text-center">Belum ada data permohonan.</td>
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
            $('#tablePermohonan').DataTable();
        });
    </script>
@endpush
