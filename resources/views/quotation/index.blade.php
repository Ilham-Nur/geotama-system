@extends('layouts.app')

@section('title', 'Quotation')

@section('content')
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2>Quotation</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">
                                Quotation
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
                        <h6 class="text-medium mb-30">List Quotation</h6>
                    </div>

                    <div class="col-md-6 mb-30 text-end">
                        @can('quotation.create')
                            <a href="{{ route('quotation.create') }}" class="btn btn-primary">
                                + Tambah Quotation
                            </a>
                        @endcan
                    </div>
                </div>

                <div class="table-wrapper table-responsive">
                    <table id="tableQuotation" class="table">
                        <thead>
                            <tr>
                                <th><h6>No Quotation</h6></th>
                                <th><h6>Tanggal</h6></th>
                                <th><h6>Client</h6></th>
                                <th><h6>Grand Total</h6></th>
                                <th><h6>Aksi</h6></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($quotations as $quotation)
                                <tr>
                                    <td>{{ $quotation->no_quo }}</td>
                                    <td>{{ optional($quotation->tanggal)->format('d-m-Y') }}</td>
                                    <td>{{ $quotation->client->nama_perusahaan ?? '-' }}</td>
                                    <td>Rp {{ number_format((float) $quotation->grand_total_quo, 0, ',', '.') }}</td>
                                    <td>
                                        @can('quotation.edit')
                                            <a href="{{ route('quotation.edit', $quotation->id) }}" class="text-primary"
                                                title="Edit">
                                                <i class="lni lni-pencil"></i>
                                            </a>
                                        @else
                                            -
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada data quotation.</td>
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
            $('#tableQuotation').DataTable();
        });
    </script>
@endpush
