@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2>Pembayaran</h2>
                </div>
            </div>

            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">
                                Pembayaran
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- ========== title-wrapper end ========== -->

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->has('file_invoice_signed'))
        <div class="alert alert-danger">{{ $errors->first('file_invoice_signed') }}</div>
    @endif


    @if ($proyekBelumInvoice->count())
        <div class="card-style mb-30">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="text-medium mb-0 text-danger">
                    Proyek Belum Dibuatkan Invoice
                </h6>
                <span class="badge bg-danger">
                    {{ $proyekBelumInvoice->count() }} Proyek
                </span>
            </div>

            <div class="table-wrapper table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>
                                <h6>No Proyek</h6>
                            </th>
                            <th>
                                <h6>Nama Proyek</h6>
                            </th>
                            <th>
                                <h6>Perusahaan</h6>
                            </th>
                            <th>
                                <h6>Nominal</h6>
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
                        @foreach ($proyekBelumInvoice as $row)
                            <tr>
                                <td>{{ $row->no_proyek }}</td>
                                <td>{{ $row->permohonan->nama_proyek ?? '-' }}</td>
                                <td>{{ $row->permohonan->nama_perusahaan ?? '-' }}</td>
                                <td>Rp {{ number_format($row->nominal ?? 0, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-warning text-dark">Belum Ada Invoice</span>
                                </td>
                                <td>
                                    <a href="{{ route('invoice.create', ['proyek_id' => $row->id]) }}"
                                        class="btn btn-sm btn-primary">
                                        Buat Invoice
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="card-style mb-30">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-30 gap-2">
            <div class="left">
                <h6 class="text-medium mb-0">Data Invoice & Pembayaran</h6>
            </div>

            <div class="text-end">
                <a href="{{ route('invoice.create') }}" class="btn btn-outline-primary me-2">
                    + Buat Invoice
                </a>
                <a href="{{ route('pembayaran.create') }}" class="btn btn-primary">
                    + Tambah Pembayaran
                </a>
            </div>
        </div>

        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-3" id="pembayaranTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="invoice-tab" data-bs-toggle="tab" data-bs-target="#invoice-pane"
                    type="button" role="tab" aria-controls="invoice-pane" aria-selected="true">
                    Invoice
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pembayaran-tab" data-bs-toggle="tab" data-bs-target="#pembayaran-pane"
                    type="button" role="tab" aria-controls="pembayaran-pane" aria-selected="false">
                    Pembayaran
                </button>
            </li>
        </ul>

        <div class="tab-content" id="pembayaranTabContent">
            {{-- TAB INVOICE --}}
            <div class="tab-pane fade show active" id="invoice-pane" role="tabpanel" aria-labelledby="invoice-tab">
                <div class="table-wrapper table-responsive">
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
                                    <h6>Invoice</h6>
                                </th>
                                <th class="ps-3">
                                    <h6>Sisa Tagihan</h6>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($proyeks as $proyek)
                                @php
                                    // TOTAL INVOICE (yang harus dibayar)
                                    $totalInvoice = $proyek->invoices->sum(function ($invoice) {
                                        return (float) $invoice->grand_total;
                                    });

                                    // TOTAL DIBAYAR
                                    $totalDibayar = $proyek->invoices->sum(function ($invoice) {
                                        return (float) $invoice->pembayarans->sum('nominal_bayar');
                                    });

                                    // SISA TAGIHAN
                                    $sisaTagihan = max($totalInvoice - $totalDibayar, 0);

                                    // OPTIONAL (buat info saja)
                                    $nilaiProyek = (float) ($proyek->nominal ?? 0);
                                    $selisihProyek = $nilaiProyek - $totalInvoice; // diskon / selisih
                                @endphp

                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $proyek->permohonan->nama_proyek ?? '-' }}</strong><br>
                                            <small class="text-muted">{{ $proyek->no_proyek }}</small><br>
                                            <small class="text-muted">
                                                {{ $proyek->permohonan->nama_perusahaan ?? '-' }}
                                            </small>
                                        </div>
                                    </td>

                                    <td>
                                        <strong>Rp {{ number_format($nilaiProyek, 0, ',', '.') }}</strong>
                                    </td>

                                    <td>
                                        @if ($proyek->invoices->count())
                                            <div class="d-flex flex-column gap-2">
                                                @foreach ($proyek->invoices as $invoice)
                                                    @php
                                                        $dibayar = (float) $invoice->pembayarans->sum('nominal_bayar');

                                                        if ($dibayar <= 0) {
                                                            $statusPembayaran = 'belum_bayar';
                                                        } elseif ($dibayar < (float) $invoice->grand_total) {
                                                            $statusPembayaran = 'sebagian';
                                                        } else {
                                                            $statusPembayaran = 'lunas';
                                                        }
                                                    @endphp

                                                    <div class="border rounded p-2">
                                                        <div
                                                            class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                                            <div>
                                                                <strong>{{ $invoice->no_invoice }}</strong><br>
                                                                <small class="text-muted">
                                                                    {{ $invoice->tanggal_invoice ? $invoice->tanggal_invoice->format('d-m-Y') : '-' }}
                                                                </small><br>
                                                                <small>
                                                                    Total: Rp
                                                                    {{ number_format($invoice->grand_total, 0, ',', '.') }}
                                                                </small><br>
                                                                <small>
                                                                    Dibayar: Rp {{ number_format($dibayar, 0, ',', '.') }}
                                                                </small><br>
                                                                <small>
                                                                    Hardcopy:
                                                                    @if ($invoice->file_invoice_signed)
                                                                        <span class="text-success">Sudah Upload</span>
                                                                    @else
                                                                        <span class="text-danger">Belum Upload</span>
                                                                    @endif
                                                                </small>
                                                            </div>

                                                            <div class="text-end">
                                                                @if ($statusPembayaran == 'belum_bayar')
                                                                    <span class="badge bg-danger">Belum Bayar</span>
                                                                @elseif ($statusPembayaran == 'sebagian')
                                                                    <span class="badge bg-warning text-dark">Sebagian</span>
                                                                @elseif ($statusPembayaran == 'lunas')
                                                                    <span class="badge bg-success">Lunas</span>
                                                                @endif

                                                                <div class="mt-2">

                                                                    @if ($invoice->file_invoice_signed)
                                                                        <a href="{{ asset('storage/' . $invoice->file_invoice_signed) }}"
                                                                            class="text-info me-2" title="Lihat File Signed"
                                                                            target="_blank">
                                                                            <i class="lni lni-eye"></i>
                                                                        </a>
                                                                    @endif

                                                                    <button type="button"
                                                                        class="border-0 bg-transparent text-primary me-2 btn-upload-signed"
                                                                        title="Upload Hardcopy Signed"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#uploadSignedModal"
                                                                        data-id="{{ $invoice->id }}"
                                                                        data-no_invoice="{{ $invoice->no_invoice }}"
                                                                        data-upload_url="{{ route('invoice.upload-signed', $invoice->id) }}"
                                                                        data-file_url="{{ $invoice->file_invoice_signed ? asset('storage/' . $invoice->file_invoice_signed) : '' }}">
                                                                        <i class="lni lni-upload"></i>
                                                                    </button>

                                                                    <a href="{{ route('invoice.export-pdf', $invoice->id) }}"
                                                                        class="text-warning me-2" title="Export PDF"
                                                                        target="_blank">
                                                                        <i class="lni lni-download"></i>
                                                                    </a>
                                                                    
                                                                    @if ($statusPembayaran == 'belum_bayar')
                                                                        <a href="{{ route('pembayaran.create', ['invoice_id' => $invoice->id]) }}"
                                                                            class="text-success me-2"
                                                                            title="Tambah Pembayaran">
                                                                            <i class="lni lni-plus"></i>
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">Belum ada invoice</span>
                                        @endif
                                    </td>

                                    <td class="ps-3">
                                        <strong class="{{ $sisaTagihan > 0 ? 'text-danger' : 'text-success' }}">
                                            Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                                        </strong>
                                        <br>

                                        <small class="text-muted">
                                            Total Invoice: Rp {{ number_format($totalInvoice, 0, ',', '.') }}
                                            <br>
                                            Dibayar: Rp {{ number_format($totalDibayar, 0, ',', '.') }}

                                            @if ($selisihProyek > 0)
                                                <br>
                                                Selisih Proyek: Rp {{ number_format($selisihProyek, 0, ',', '.') }}
                                            @endif
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

            {{-- TAB PEMBAYARAN --}}
            <div class="tab-pane fade" id="pembayaran-pane" role="tabpanel" aria-labelledby="pembayaran-tab">
                <div class="table-wrapper table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>
                                    <h6>No Pembayaran</h6>
                                </th>
                                <th>
                                    <h6>Tanggal</h6>
                                </th>
                                <th>
                                    <h6>No Invoice</h6>
                                </th>
                                <th>
                                    <h6>Proyek</h6>
                                </th>
                                <th>
                                    <h6>Pemohon</h6>
                                </th>
                                <th>
                                    <h6>Metode</h6>
                                </th>
                                <th>
                                    <h6>Nominal Bayar</h6>
                                </th>
                                <th>
                                    <h6>Bukti</h6>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pembayarans as $row)
                                <tr>
                                    <td>{{ $row->no_pembayaran }}</td>
                                    <td>{{ $row->tanggal_bayar?->format('d-m-Y') }}</td>
                                    <td>{{ $row->invoice->no_invoice ?? '-' }}</td>
                                    <td>{{ $row->invoice->proyek->permohonan->nama_proyek ?? '-' }}</td>
                                    <td>{{ $row->invoice->proyek->permohonan->nama_perusahaan ?? '-' }}</td>
                                    <td>{{ ucfirst($row->metode_pembayaran) }}</td>
                                    <td>Rp {{ number_format($row->nominal_bayar, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($row->bukti_pembayaran)
                                            <a href="{{ asset('storage/' . $row->bukti_pembayaran) }}" target="_blank">
                                                Lihat File
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Belum ada data pembayaran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $pembayarans->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Global Modal Upload Signed Invoice --}}
    <div class="modal fade" id="uploadSignedModal" tabindex="-1" aria-labelledby="uploadSignedModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="uploadSignedForm" action="" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadSignedModalLabel">
                            Upload Hardcopy Invoice Signed
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">No Invoice</label>
                            <input type="text" id="modal_no_invoice" class="form-control" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Upload File</label>
                            <input type="file" name="file_invoice_signed" class="form-control"
                                accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="text-muted">
                                Format: PDF, JPG, JPEG, PNG. Maksimal 5 MB.
                            </small>
                        </div>

                        <div class="mb-2" id="currentFileWrapper" style="display: none;">
                            <small class="text-muted d-block">File saat ini:</small>
                            <a href="#" id="currentFileLink" target="_blank">Lihat file uploaded</a>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.btn-upload-signed');
            const form = document.getElementById('uploadSignedForm');
            const noInvoiceInput = document.getElementById('modal_no_invoice');
            const currentFileWrapper = document.getElementById('currentFileWrapper');
            const currentFileLink = document.getElementById('currentFileLink');

            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    const uploadUrl = this.dataset.upload_url;
                    const noInvoice = this.dataset.no_invoice;
                    const fileUrl = this.dataset.file_url;

                    form.action = uploadUrl;
                    noInvoiceInput.value = noInvoice;

                    if (fileUrl) {
                        currentFileWrapper.style.display = 'block';
                        currentFileLink.href = fileUrl;
                    } else {
                        currentFileWrapper.style.display = 'none';
                        currentFileLink.href = '#';
                    }
                });
            });
        });
    </script>
@endpush
