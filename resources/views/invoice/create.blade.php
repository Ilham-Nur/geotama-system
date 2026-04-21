@extends('layouts.app')

@section('title', 'Tambah Invoice')

@section('content')
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2> Tambah Invoice</h2>
                </div>
            </div>
            <!-- end col -->
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('invoice.index') }}">Invoice</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Tambah Invoice
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
        <form action="{{ route('invoice.store') }}" method="POST" id="invoice-form">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>No Invoice</label>
                    <input type="text" class="form-control" value="{{ $generatedInvoiceNo }}" readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Tanggal Invoice</label>
                    <input type="date" name="tanggal_invoice" class="form-control"
                        value="{{ old('tanggal_invoice', now()->format('Y-m-d')) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Proyek</label>
                    <select name="proyek_id" id="proyek_id" class="form-control">
                        <option value="">-- Pilih Proyek --</option>
                        @foreach ($proyeks as $proyek)
                            <option value="{{ $proyek->id }}" data-no="{{ $proyek->no_proyek }}"
                                data-nominal="{{ $proyek->nominal ?? 0 }}"
                                data-total-invoice="{{ $proyek->total_invoice ?? 0 }}"
                                data-total-invoice-net="{{ $proyek->total_invoice_net ?? 0 }}"
                                data-total-tax="{{ $proyek->total_tax ?? 0 }}"
                                data-total-discount="{{ $proyek->total_discount ?? 0 }}"
                                data-sisa="{{ is_null($proyek->sisa_tagihan) ? '' : $proyek->sisa_tagihan }}"
                                data-has-invoice="{{ $proyek->has_invoice ? 1 : 0 }}"
                                data-is-nominal-empty="{{ $proyek->is_nominal_empty ? 1 : 0 }}"
                                data-nama-perusahaan="{{ $proyek->notes_template['nama_perusahaan'] ?? '' }}"
                                data-no-permohonan="{{ $proyek->notes_template['nomor_permohonan'] ?? '' }}"
                                data-created-at-permohonan="{{ $proyek->notes_template['tanggal_permohonan'] ?? '' }}"
                                data-lokasi-permohonan="{{ $proyek->notes_template['lokasi_permohonan'] ?? '' }}"
                                data-tanggal-pelaksanaan-awal="{{ $proyek->notes_template['tanggal_pelaksanaan_awal'] ?? '' }}"
                                data-tanggal-pelaksanaan-akhir="{{ $proyek->notes_template['tanggal_pelaksanaan_akhir'] ?? '' }}"
                                {{ $selectedProyekId == $proyek->id ? 'selected' : '' }}>
                                {{ $proyek->no_proyek }} - {{ $proyek->permohonan->nama_proyek ?? 'Tanpa deskripsi' }}
                            </option>
                        @endforeach
                    </select>
                    @error('proyek_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label>Jenis Invoice</label>
                    <select name="jenis_invoice" class="form-control">
                        <option value="">-- Pilih Jenis Invoice --</option>
                        <option value="dp" {{ old('jenis_invoice') == 'dp' ? 'selected' : '' }}>DP</option>
                        <option value="termin" {{ old('jenis_invoice') == 'termin' ? 'selected' : '' }}>Termin</option>
                        <option value="pelunasan" {{ old('jenis_invoice') == 'pelunasan' ? 'selected' : '' }}>Pelunasan
                        </option>
                    </select>
                    @error('jenis_invoice')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label>No Proyek</label>
                    <input type="text" id="proyek_no_view" class="form-control" readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Nominal Proyek</label>
                    <input type="text" id="nominal_proyek_display" class="form-control"
                        value="{{ old('nominal_proyek') }}">
                    <input type="hidden" name="nominal_proyek" id="nominal_proyek" value="{{ old('nominal_proyek') }}">
                    <small id="nominal_help" class="text-muted"></small>
                    @error('nominal_proyek')
                        <small class="text-danger d-block">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label>Sisa Tagihan</label>
                    <input type="text" id="sisa_tagihan_view" class="form-control" readonly>
                </div>

                {{-- <div class="col-md-6 mb-3">
                    <label>Total Invoice Sebelumnya (Tanpa Tax)</label>
                    <input type="text" id="total_invoice_view" class="form-control" readonly>
                </div> --}}

                <div class="col-md-12 mb-3">
                    <label>Catatan</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Item Invoice</h5>
                <button type="button" class="btn btn-sm btn-primary" id="btn-add-item">+ Tambah Item</button>
            </div>

            <div class="table-wrapper table-responsive">
                <table class="table table-bordered" id="invoice-items-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th width="120">Unit</th>
                            <th width="120">Qty</th>
                            <th width="160">Amount</th>
                            <th width="160">Total</th>
                            <th width="80">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $oldItems = old('items', [
                                [
                                    'description' => '',
                                    'unit' => '',
                                    'qty' => '',
                                    'amount' => '',
                                ],
                            ]);
                        @endphp

                        @foreach ($oldItems as $i => $item)
                            <tr>
                                <td>
                                    <input type="text" name="items[{{ $i }}][description]"
                                        class="form-control" value="{{ $item['description'] ?? '' }}">
                                    @error("items.$i.description")
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text" name="items[{{ $i }}][unit]" class="form-control"
                                        value="{{ $item['unit'] ?? '' }}">
                                    @error("items.$i.unit")
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0"
                                        name="items[{{ $i }}][qty]" class="form-control item-qty"
                                        value="{{ $item['qty'] ?? '' }}">
                                    @error("items.$i.qty")
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0"
                                        name="items[{{ $i }}][amount]" class="form-control item-amount"
                                        value="{{ $item['amount'] ?? '' }}">
                                    @error("items.$i.amount")
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text" class="form-control item-total-view" readonly>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger btn-remove-item">Hapus</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @error('items')
                <small class="text-danger d-block mt-2">{{ $message }}</small>
            @enderror

            <hr>

            <div class="row justify-content-end">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label>Sub Total</label>
                        <input type="text" id="sub_total_view" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Discount</label>
                        <input type="number" step="0.01" min="0" name="discount" id="discount"
                            class="form-control" value="{{ old('discount', 0) }}">
                    </div>

                    <div class="mb-3">
                        <label>Tax</label>
                        <input type="number" step="0.01" min="0" name="tax" id="tax"
                            class="form-control" value="{{ old('tax', 0) }}">
                    </div>

                    <div class="mb-3">
                        <label>Grand Total</label>
                        <input type="text" id="grand_total_view" class="form-control" readonly>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="main-btn primary-btn btn-hover">
                    Simpan
                </button>
                <a href="{{ route('invoice.index') }}" class="main-btn light-btn btn-hover">
                    Kembali
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const proyekSelect = document.getElementById('proyek_id');
            const proyekNoView = document.getElementById('proyek_no_view');
            const nominalInput = document.getElementById('nominal_proyek');
            const nominalInputDisplay = document.getElementById('nominal_proyek_display');
            const nominalHelp = document.getElementById('nominal_help');
            const sisaTagihanView = document.getElementById('sisa_tagihan_view');
            const notesInput = document.getElementById('notes');
            // const totalInvoiceView = document.getElementById('total_invoice_view');

            function formatRupiah(number) {
                number = Number(number || 0);
                return 'Rp ' + number.toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                });
            }

            function formatNominalDisplay(number) {
                const parsed = Number(number || 0);
                if (!Number.isFinite(parsed) || parsed <= 0) return '';
                return parsed.toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
            }

            function parseNominalDisplay(value) {
                if (!value) return '';
                const normalized = value.toString().replace(/[^\d]/g, '');
                return normalized ? Number(normalized) : '';
            }

            function formatTanggalId(value) {
                if (!value) return '';
                if (value.includes('-') && value.length === 10) {
                    const [year, month, day] = value.split('-');
                    if (year.length === 4) {
                        return `${day}-${month}-${year}`;
                    }
                }
                return value;
            }

            function buildAutoNotes() {
                const selected = proyekSelect.options[proyekSelect.selectedIndex];

                if (!selected || !selected.value) {
                    return '';
                }

                const namaPerusahaan = selected.dataset.namaPerusahaan || '-';
                const noPermohonan = selected.dataset.noPermohonan || '-';
                const createdAtPermohonan = selected.dataset.createdAtPermohonan || '-';
                const lokasiPermohonan = selected.dataset.lokasiPermohonan || '-';
                const tanggalAwal = formatTanggalId(selected.dataset.tanggalPelaksanaanAwal || '');
                const tanggalAkhir = formatTanggalId(selected.dataset.tanggalPelaksanaanAkhir || '');

                return `Atas permintaan dari ${namaPerusahaan} dengan no permohonan ${noPermohonan} tanggal ${createdAtPermohonan} telah dilaksanakan pengujian di ${lokasiPermohonan} pada tanggal ${tanggalAwal || '-'} s/d ${tanggalAkhir || '-'}.`;
            }

            function updateProjectInfo() {
                const selected = proyekSelect.options[proyekSelect.selectedIndex];

                if (!selected || !selected.value) {
                    proyekNoView.value = '';
                    nominalInput.value = '';
                    nominalInputDisplay.value = '';
                    nominalInputDisplay.readOnly = false;
                    nominalHelp.innerText = '';
                    sisaTagihanView.value = '';
                    notesInput.value = '';
                    // totalInvoiceView.value = '';
                    return;
                }

                const noProyek = selected.dataset.no || '';
                const nominal = parseFloat(selected.dataset.nominal || 0);
                const totalInvoice = parseFloat(selected.dataset.totalInvoice || 0);
                const sisa = parseFloat(selected.dataset.sisa || 0);
                const hasInvoice = selected.dataset.hasInvoice === '1';

                proyekNoView.value = noProyek;
                // totalInvoiceView.value = formatRupiah(totalInvoice);
                sisaTagihanView.value = formatRupiah(sisa);

                if (hasInvoice) {
                    nominalInput.value = nominal;
                    nominalInputDisplay.value = formatNominalDisplay(nominal);
                    nominalInputDisplay.readOnly = true;
                    nominalHelp.innerText = 'Nominal proyek tidak bisa diubah karena invoice sudah pernah dibuat.';
                } else {
                    nominalInputDisplay.readOnly = false;
                    nominalInput.value = nominal > 0 ? nominal : '';
                    nominalInputDisplay.value = formatNominalDisplay(nominalInput.value);
                    nominalHelp.innerText = 'Isi nominal proyek karena ini invoice pertama.';
                }

                notesInput.value = buildAutoNotes();
            }

            function calculateTotals() {
                let subTotal = 0;

                document.querySelectorAll('#invoice-items-table tbody tr').forEach(function(row) {
                    const qty = parseFloat(row.querySelector('.item-qty')?.value || 0);
                    const amount = parseFloat(row.querySelector('.item-amount')?.value || 0);
                    const total = qty * amount;

                    const totalView = row.querySelector('.item-total-view');
                    if (totalView) {
                        totalView.value = formatRupiah(total);
                    }

                    subTotal += total;
                });

                const discount = parseFloat(document.getElementById('discount').value || 0);
                const tax = parseFloat(document.getElementById('tax').value || 0);
                const grandTotal = subTotal - discount + tax;

                document.getElementById('sub_total_view').value = formatRupiah(subTotal);
                document.getElementById('grand_total_view').value = formatRupiah(grandTotal);
            }

            updateProjectInfo();
            calculateTotals();

            proyekSelect.addEventListener('change', updateProjectInfo);

            nominalInputDisplay.addEventListener('input', function() {
                const raw = parseNominalDisplay(nominalInputDisplay.value);
                nominalInput.value = raw;
                nominalInputDisplay.value = formatNominalDisplay(raw);
            });

            document.addEventListener('input', function(e) {
                if (
                    e.target.classList.contains('item-qty') ||
                    e.target.classList.contains('item-amount') ||
                    e.target.id === 'discount' ||
                    e.target.id === 'tax'
                ) {
                    calculateTotals();
                }
            });

            let itemIndex =
                {{ count(old('items', [['description' => '', 'unit' => '', 'qty' => '', 'amount' => '']])) }};

            document.getElementById('btn-add-item').addEventListener('click', function() {
                const tbody = document.querySelector('#invoice-items-table tbody');

                const row = `
            <tr>
                <td><input type="text" name="items[${itemIndex}][description]" class="form-control"></td>
                <td><input type="text" name="items[${itemIndex}][unit]" class="form-control"></td>
                <td><input type="number" step="0.01" min="0" name="items[${itemIndex}][qty]" class="form-control item-qty"></td>
                <td><input type="number" step="0.01" min="0" name="items[${itemIndex}][amount]" class="form-control item-amount"></td>
                <td><input type="text" class="form-control item-total-view" readonly></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger btn-remove-item">Hapus</button>
                </td>
            </tr>
        `;

                tbody.insertAdjacentHTML('beforeend', row);
                itemIndex++;
                calculateTotals();
            });

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-remove-item')) {
                    const rows = document.querySelectorAll('#invoice-items-table tbody tr');

                    if (rows.length > 1) {
                        e.target.closest('tr').remove();
                        calculateTotals();
                    } else {
                        alert('Minimal harus ada 1 item.');
                    }
                }
            });
        });
    </script>
@endpush
