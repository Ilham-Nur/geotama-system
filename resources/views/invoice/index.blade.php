@extends('layouts.app')

@section('title', 'Invoice')

@section('content')
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2>Invoice</h2>
                </div>
            </div>
            <!-- end col -->
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            {{-- <li class="breadcrumb-item">
                                <a href="{{ route('permohonan.index') }}">Permohonan</a>
                            </li> --}}
                            <li class="breadcrumb-item active" aria-current="page">
                                Invoice
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

    <div class="card-style mb-30">

        <div class="title d-flex flex-wrap align-items-center justify-content-between">
            <div class="left">
                <h6 class="text-medium mb-30">List Invoice</h6>
            </div>

            <div class="col-md-6 mb-30 text-end">
                @can('invoice.create')
                    <a href="{{ route('invoice.create') }}" class="btn btn-primary">
                        + Buat Invoice
                    </a>
                @endcan
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>
                            <h6>List Proyek</h6>
                        </th>
                        <th>
                            <h6>Nilai Total</h6>
                        </th>
                        <th>
                            <h6>Pembayaran</h6>
                        </th>
                        <th class="ps-3">
                            <h6>Sisa Tagihan</h6>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proyeks as $proyek)
                        @php
                            $nilaiTotal = (float) ($proyek->nominal ?? 0);

                            // total pembayaran yang benar-benar sudah masuk
                            $totalDibayar = $proyek->invoices->sum(function ($invoice) {
                                return (float) $invoice->total_dibayar;
                            });

                            // total tax dari semua invoice
                            $totalTax = $proyek->invoices->sum(function ($invoice) {
                                return (float) $invoice->tax;
                            });

                            // sisa proyek berdasarkan pembayaran masuk
                            $sisaProyek = max($nilaiTotal - $totalDibayar, 0);

                            // kalau memang mau tetap ditambah tax
                            $sisaTagihan = $sisaProyek + $totalTax;
                        @endphp

                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $proyek->permohonan->nama_proyek ?? '-' }}</strong><br>
                                    <small class="text-muted">{{ $proyek->no_proyek }}</small>
                                </div>
                            </td>

                            <td>
                                <strong>Rp {{ number_format($nilaiTotal, 0, ',', '.') }}</strong>
                            </td>

                            <td>
                                @if ($proyek->invoices->count())
                                    <div class="d-flex flex-column gap-2">
                                        @foreach ($proyek->invoices as $invoice)
                                            <div class="border rounded p-2">
                                                <div
                                                    class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                                    <div>
                                                        <strong>{{ $invoice->no_invoice }}</strong><br>
                                                        <small class="text-muted">
                                                            {{ $invoice->tanggal_invoice ? $invoice->tanggal_invoice->format('d-m-Y') : '-' }}
                                                        </small><br>
                                                        <small>
                                                            Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}
                                                        </small>
                                                    </div>

                                                    <div class="text-end">
                                                        @if ($invoice->status_pembayaran == 'belum_bayar')
                                                            <span class="badge bg-danger">Belum Bayar</span>
                                                        @elseif ($invoice->status_pembayaran == 'sebagian')
                                                            <span class="badge bg-warning">Sebagian</span>
                                                        @elseif ($invoice->status_pembayaran == 'lunas')
                                                            <span class="badge bg-success">Lunas</span>
                                                        @endif

                                                        <div class="mt-1">
                                                            @can('invoice.export_pdf')
                                                                <a href="{{ route('invoice.export-pdf', $invoice->id) }}"
                                                                    class="text-warning" title="Export PDF" target="_blank">
                                                                    <i class="lni lni-download"></i>
                                                                </a>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                        <div class="mt-2">
                                            <strong>Total Invoice: Rp
                                                {{ number_format($totalInvoice, 0, ',', '.') }}</strong>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">Belum ada invoice</span>
                                @endif
                            </td>

                            <td>
                                <strong class="{{ $sisaTagihan > 0 ? 'text-danger' : 'text-success' }} ps-3">
                                    Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                                </strong>
                                <br>
                                <small class="text-muted ps-3">
                                    Sisa proyek: Rp {{ number_format($sisaProyek, 0, ',', '.') }}
                                    + Tax: Rp {{ number_format($totalTax, 0, ',', '.') }}
                                </small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data proyek dengan invoice.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
