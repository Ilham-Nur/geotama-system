@extends('layouts.app')

@section('title', 'Tambah Pembayaran')

@section('content')
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2> Tambah Pembayaran</h2>
                </div>
            </div>
            <!-- end col -->
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('pembayaran.index') }}">Pembayaran</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Tambah Pembayaran
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
        <form action="{{ route('pembayaran.store') }}" method="POST" enctype="multipart/form-data" id="form-pembayaran">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>No Pembayaran</label>
                    <input type="text" class="form-control" value="{{ $generatedNoPembayaran }}" readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Tanggal Bayar</label>
                    <input type="date" name="tanggal_bayar" class="form-control"
                        value="{{ old('tanggal_bayar', now()->format('Y-m-d')) }}">
                    @error('tanggal_bayar')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <label>Invoice</label>
                    <select name="invoice_id" id="invoice_id" class="form-control">
                        <option value="">-- Pilih Invoice --</option>
                        @foreach ($invoices as $invoice)
                            <option value="{{ $invoice->id }}" data-no_invoice="{{ $invoice->no_invoice }}"
                                data-no_proyek="{{ $invoice->proyek->no_proyek ?? '' }}"
                                data-pemohon="{{ $invoice->proyek->permohonan->nama_perusahaan ?? '' }}"
                                data-grand_total="{{ $invoice->grand_total }}"
                                data-total_dibayar="{{ $invoice->total_dibayar }}"
                                data-sisa_tagihan="{{ $invoice->sisa_tagihan }}"
                                {{ $selectedInvoiceId == $invoice->id ? 'selected' : '' }}>
                                {{ $invoice->no_invoice }} -
                                {{ $invoice->proyek->permohonan->nama_perusahaan ?? ($invoice->proyek->no_proyek ?? '-') }}
                            </option>
                        @endforeach
                    </select>
                    @error('invoice_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>No Invoice</label>
                    <input type="text" id="info_no_invoice" class="form-control" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label>No Proyek</label>
                    <input type="text" id="info_no_proyek" class="form-control" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Pemohon</label>
                    <input type="text" id="info_pemohon" class="form-control" readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Grand Total</label>
                    <input type="text" id="info_grand_total" class="form-control" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Total Dibayar</label>
                    <input type="text" id="info_total_dibayar" class="form-control" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Sisa Tagihan</label>
                    <input type="text" id="info_sisa_tagihan" class="form-control" readonly>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Nominal Bayar</label>
                    <input type="number" step="0.01" min="0" name="nominal_bayar" id="nominal_bayar"
                        class="form-control" value="{{ old('nominal_bayar') }}">
                    @error('nominal_bayar')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label>Metode Pembayaran</label>
                    <select name="metode_pembayaran" class="form-control">
                        <option value="transfer" {{ old('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>Transfer
                        </option>
                        <option value="cash" {{ old('metode_pembayaran') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="giro" {{ old('metode_pembayaran') == 'giro' ? 'selected' : '' }}>Giro</option>
                        <option value="cek" {{ old('metode_pembayaran') == 'cek' ? 'selected' : '' }}>Cek</option>
                        <option value="lainnya" {{ old('metode_pembayaran') == 'lainnya' ? 'selected' : '' }}>Lainnya
                        </option>
                    </select>
                    @error('metode_pembayaran')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label>No Referensi</label>
                    <input type="text" name="no_referensi" class="form-control" value="{{ old('no_referensi') }}">
                    @error('no_referensi')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label>Nama Pengirim</label>
                    <input type="text" name="nama_pengirim" class="form-control" value="{{ old('nama_pengirim') }}">
                    @error('nama_pengirim')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label>Bank Pengirim</label>
                    <input type="text" name="bank_pengirim" class="form-control" value="{{ old('bank_pengirim') }}">
                    @error('bank_pengirim')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label>Bukti Pembayaran</label>
                    <input type="file" name="bukti_pembayaran" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                    @error('bukti_pembayaran')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="main-btn primary-btn btn-hover">Simpan</button>
                <a href="{{ route('pembayaran.index') }}" class="main-btn light-btn btn-hover">Kembali</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            function formatRupiah(number) {
                number = Number(number || 0);
                return 'Rp ' + number.toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                });
            }

            function updateInvoiceInfo() {
                let selected = $('#invoice_id option:selected');

                if (!selected.val()) {
                    $('#info_no_invoice').val('');
                    $('#info_no_proyek').val('');
                    $('#info_pemohon').val('');
                    $('#info_grand_total').val('');
                    $('#info_total_dibayar').val('');
                    $('#info_sisa_tagihan').val('');
                    return;
                }

                $('#info_no_invoice').val(selected.data('no_invoice') || '');
                $('#info_no_proyek').val(selected.data('no_proyek') || '');
                $('#info_pemohon').val(selected.data('pemohon') || '');
                $('#info_grand_total').val(formatRupiah(selected.data('grand_total') || 0));
                $('#info_total_dibayar').val(formatRupiah(selected.data('total_dibayar') || 0));
                $('#info_sisa_tagihan').val(formatRupiah(selected.data('sisa_tagihan') || 0));
            }

            $('#invoice_id').on('change', function() {
                updateInvoiceInfo();
            });

            updateInvoiceInfo();
        });
    </script>
@endpush
