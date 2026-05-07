@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="title-wrapper pt-20 pb-20 d-flex justify-content-between align-items-center">
    <h2>Dashboard Operasional Tahun {{ $selectedYear }}</h2>
    <form method="GET" action="{{ route('dashboard') }}" class="d-flex align-items-center gap-2">
        <label for="year" class="mb-0">Filter Tahun</label>
        <select name="year" id="year" class="form-select form-select-sm" onchange="this.form.submit()">
            @foreach($availableYears as $year)
                <option value="{{ $year }}" {{ (int) $year === (int) $selectedYear ? 'selected' : '' }}>{{ $year }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="row g-2 mb-3">
    <div class="col-xl-3 col-md-6"><div class="card-style"><small>Total Permohonan (Tahun Terpilih)</small><h4>{{ number_format($permohonanTotal) }}</h4></div></div>
    <div class="col-xl-3 col-md-6"><div class="card-style"><small>Permohonan OPEN</small><h4>{{ number_format($permohonanOpen) }}</h4></div></div>
    <div class="col-xl-3 col-md-6"><div class="card-style"><small>Permohonan CLOSE</small><h4>{{ number_format($permohonanClose) }}</h4></div></div>
    <div class="col-xl-3 col-md-6"><div class="card-style"><small>Proyek Aktif</small><h4>{{ number_format($proyekAktif) }}</h4></div></div>
    <div class="col-xl-3 col-md-6"><div class="card-style"><small>Total Invoice</small><h4>Rp {{ number_format($invoiceTotal,0,',','.') }}</h4></div></div>
    <div class="col-xl-3 col-md-6"><div class="card-style"><small>Outstanding</small><h4>Rp {{ number_format($outstandingTotal,0,',','.') }}</h4></div></div>
    <div class="col-xl-2 col-md-4"><div class="card-style"><small>Invoice Lunas</small><h4>{{ number_format($invoiceLunas) }}</h4></div></div>
    <div class="col-xl-2 col-md-4"><div class="card-style"><small>Invoice Sebagian</small><h4>{{ number_format($invoiceSebagian) }}</h4></div></div>
    <div class="col-xl-2 col-md-4"><div class="card-style"><small>Belum Bayar</small><h4>{{ number_format($invoiceBelumBayar) }}</h4></div></div>
</div>

<div class="row g-2 mb-3">
    <div class="col-lg-4">
        <div class="card-style">
            <h6>Status Proyek</h6>
            @foreach($projectStatusChart as $status => $total)
                <div class="d-flex justify-content-between border-bottom py-1"><span>{{ ucfirst($status) }}</span><strong>{{ $total }}</strong></div>
            @endforeach
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-style">
            <h6>Pembayaran per Bulan ({{ $selectedYear }})</h6>
            @foreach($monthlyPayments as $row)
                <div class="d-flex justify-content-between border-bottom py-1"><span>{{ $row->month }}</span><strong>Rp {{ number_format($row->total,0,',','.') }}</strong></div>
            @endforeach
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-style">
            <h6>PAK per Bulan per Kategori ({{ $selectedYear }})</h6>
            @foreach($pakMonthlyByCategory as $row)
                <div class="d-flex justify-content-between border-bottom py-1"><span>{{ $row->month }} - {{ $row->category }}</span><strong>Rp {{ number_format($row->total,0,',','.') }}</strong></div>
            @endforeach
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card-style">
            <h6>Top 5 Outstanding Invoice (Tahun {{ $selectedYear }})</h6>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr><th>No Invoice</th><th>Grand Total</th><th>Sudah Dibayar</th><th>Sisa Tagihan</th></tr>
                    </thead>
                    <tbody>
                        @forelse($topOutstandingInvoices as $inv)
                            <tr>
                                <td>{{ $inv->no_invoice }}</td>
                                <td>Rp {{ number_format($inv->grand_total,0,',','.') }}</td>
                                <td>Rp {{ number_format($inv->total_dibayar,0,',','.') }}</td>
                                <td>Rp {{ number_format($inv->sisa_tagihan,0,',','.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center">Tidak ada outstanding invoice</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
