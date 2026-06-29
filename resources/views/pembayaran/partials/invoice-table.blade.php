<form method="GET" action="{{ route('pembayaran.index') }}" class="mb-3" id="invoiceFilterForm">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Search</label>
            <input type="text" name="invoice_search" class="form-control"
                value="{{ request('invoice_search') }}"
                placeholder="No invoice, no proyek, proyek, perusahaan">
        </div>
        <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="invoice_status" class="form-control">
                <option value="">Semua</option>
                <option value="belum_bayar" @selected(request('invoice_status') === 'belum_bayar')>Belum Bayar</option>
                <option value="sebagian" @selected(request('invoice_status') === 'sebagian')>Sebagian</option>
                <option value="lunas" @selected(request('invoice_status') === 'lunas')>Lunas</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Jenis</label>
            <select name="invoice_jenis" class="form-control">
                <option value="">Semua</option>
                <option value="dp" @selected(request('invoice_jenis') === 'dp')>DP</option>
                <option value="termin" @selected(request('invoice_jenis') === 'termin')>Termin</option>
                <option value="pelunasan" @selected(request('invoice_jenis') === 'pelunasan')>Pelunasan</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Hardcopy</label>
            <select name="invoice_signed" class="form-control">
                <option value="">Semua</option>
                <option value="uploaded" @selected(request('invoice_signed') === 'uploaded')>Sudah Upload</option>
                <option value="missing" @selected(request('invoice_signed') === 'missing')>Belum Upload</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Per Halaman</label>
            <select name="invoice_per_page" class="form-control">
                @foreach ([10, 25, 50] as $pageSize)
                    <option value="{{ $pageSize }}" @selected((int) request('invoice_per_page', 10) === $pageSize)>
                        {{ $pageSize }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Tanggal Dari</label>
            <input type="date" name="invoice_date_from" class="form-control"
                value="{{ request('invoice_date_from') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">Tanggal Sampai</label>
            <input type="date" name="invoice_date_to" class="form-control"
                value="{{ request('invoice_date_to') }}">
        </div>
        <div class="col-md-8 d-flex gap-2 justify-content-md-end">
            <button type="submit" class="btn btn-primary">Terapkan</button>
            <a href="{{ route('pembayaran.index') }}" class="btn btn-outline-secondary" id="invoiceFilterReset">Reset</a>
        </div>
    </div>
</form>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <small class="text-muted">
        @if ($proyeks->total())
            Menampilkan {{ $proyeks->firstItem() }} - {{ $proyeks->lastItem() }} dari {{ $proyeks->total() }} proyek invoice.
        @else
            Tidak ada proyek invoice sesuai filter.
        @endif
    </small>
</div>

<div class="table-wrapper table-responsive">
    <table class="table align-middle">
        <thead>
            <tr>
                <th><h6>List Proyek</h6></th>
                <th><h6>Nilai Total</h6></th>
                <th><h6>Invoice</h6></th>
                <th class="ps-3"><h6>Sisa Tagihan</h6></th>
            </tr>
        </thead>
        <tbody>
            @forelse($proyeks as $proyek)
                @php
                    $totalInvoice = $proyek->invoices->sum(fn ($invoice) => (float) $invoice->grand_total);
                    $totalDibayar = $proyek->invoices->sum(fn ($invoice) => (float) $invoice->pembayarans->sum('nominal_bayar'));
                    $sisaTagihan = max($totalInvoice - $totalDibayar, 0);
                    $nilaiProyek = (float) ($proyek->nominal ?? 0);
                    $selisihProyek = $nilaiProyek - $totalInvoice;
                @endphp

                <tr>
                    <td>
                        <div>
                            <strong>{{ $proyek->permohonan->nama_proyek ?? '-' }}</strong><br>
                            <small class="text-muted">{{ $proyek->no_proyek }}</small><br>
                            <small class="text-muted">{{ $proyek->permohonan->nama_perusahaan ?? '-' }}</small>
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
                                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                            <div>
                                                <strong>{{ $invoice->no_invoice }}</strong><br>
                                                <small class="text-muted">
                                                    {{ $invoice->tanggal_invoice ? $invoice->tanggal_invoice->format('d-m-Y') : '-' }}
                                                </small><br>
                                                <small>Total: Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</small><br>
                                                <small>Dibayar: Rp {{ number_format($dibayar, 0, ',', '.') }}</small><br>
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
                                                            class="text-info me-2" title="Lihat File Signed" target="_blank">
                                                            <i class="lni lni-eye"></i>
                                                        </a>
                                                    @endif

                                                    @can('invoice.upload_signed')
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
                                                    @endcan

                                                    @can('invoice.export_pdf')
                                                        <a href="{{ route('invoice.export-pdf', $invoice->id) }}"
                                                            class="text-warning me-2" title="Export PDF" target="_blank">
                                                            <i class="lni lni-download"></i>
                                                        </a>
                                                    @endcan

                                                    @can('invoice.store')
                                                        @if ($statusPembayaran == 'belum_bayar')
                                                            <a href="{{ route('invoice.edit', $invoice->id) }}"
                                                                class="text-primary me-2" title="Edit Invoice">
                                                                <i class="lni lni-pencil"></i>
                                                            </a>
                                                        @endif
                                                    @endcan

                                                    @can('pembayaran.create')
                                                        @if ($statusPembayaran == 'belum_bayar')
                                                            <a href="{{ route('pembayaran.create', ['invoice_id' => $invoice->id]) }}"
                                                                class="text-success me-2" title="Tambah Pembayaran">
                                                                <i class="lni lni-plus"></i>
                                                            </a>
                                                        @endif
                                                    @endcan
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

<div class="mt-3 invoice-pagination">
    {{ $proyeks->links() }}
</div>
