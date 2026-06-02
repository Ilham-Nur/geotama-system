@extends('layouts.app')

@section('title', 'Edit Invoice')

@section('content')
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2>Edit Invoice</h2>
                </div>
            </div>

            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('invoice.index') }}">Invoice</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Edit Invoice
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- ========== title-wrapper end ========== -->

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @error('invoice')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    <div class="card-style mb-30">
        <form action="{{ route('invoice.update', $invoice->id) }}" method="POST" id="invoice-form">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>No Invoice</label>
                    <input type="text" class="form-control" value="{{ $invoice->no_invoice }}" readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Tanggal Invoice</label>
                    <input type="date" name="tanggal_invoice" class="form-control"
                        value="{{ old('tanggal_invoice', optional($invoice->tanggal_invoice)->format('Y-m-d')) }}">
                    @error('tanggal_invoice')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label>Proyek</label>
                    <input type="text" class="form-control"
                        value="{{ $proyek->no_proyek }} - {{ $proyek->permohonan->nama_proyek ?? 'Tanpa deskripsi' }}"
                        readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Jenis Invoice</label>
                    <select name="jenis_invoice" class="form-control">
                        <option value="">-- Pilih Jenis Invoice --</option>
                        <option value="dp" {{ old('jenis_invoice', $invoice->jenis_invoice) == 'dp' ? 'selected' : '' }}>
                            DP
                        </option>
                        <option value="termin"
                            {{ old('jenis_invoice', $invoice->jenis_invoice) == 'termin' ? 'selected' : '' }}>
                            Termin
                        </option>
                        <option value="pelunasan"
                            {{ old('jenis_invoice', $invoice->jenis_invoice) == 'pelunasan' ? 'selected' : '' }}>
                            Pelunasan
                        </option>
                    </select>
                    @error('jenis_invoice')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label>No Proyek</label>
                    <input type="text" class="form-control" value="{{ $proyek->no_proyek }}" readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Nominal Proyek</label>
                    <input type="text" id="nominal_proyek_display" class="form-control"
                        value="{{ old('nominal_proyek', $proyek->nominal) }}"
                        {{ $canEditNominalProyek ? '' : 'readonly' }}>
                    <input type="hidden" name="nominal_proyek" id="nominal_proyek"
                        value="{{ old('nominal_proyek', $proyek->nominal) }}">
                    <small id="nominal_help" class="text-muted">
                        @if ($canEditNominalProyek)
                            Nominal proyek bisa diubah karena ini invoice pertama dan belum ada pembayaran.
                        @else
                            Nominal proyek hanya bisa diubah dari invoice pertama.
                        @endif
                    </small>
                    @error('nominal_proyek')
                        <small class="text-danger d-block">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label>Sisa Nominal Proyek</label>
                    <input type="text" id="sisa_tagihan_view" class="form-control" readonly>
                    <small class="text-muted">
                        Total invoice lain: Rp {{ number_format($totalInvoiceLain, 0, ',', '.') }}
                    </small>
                </div>

                <div class="col-md-12 mb-3">
                    <label>Catatan</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $invoice->notes) }}</textarea>
                    @error('notes')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
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
                            $defaultItems = $invoice->items
                                ->map(function ($item) {
                                    return [
                                        'description' => $item->description,
                                        'unit' => $item->unit,
                                        'qty' => $item->qty,
                                        'amount' => $item->amount,
                                    ];
                                })
                                ->values()
                                ->toArray();

                            $oldItems = old('items', $defaultItems);
                            if (count($oldItems) === 0) {
                                $oldItems = [
                                    [
                                        'description' => '',
                                        'unit' => '',
                                        'qty' => '',
                                        'amount' => '',
                                    ],
                                ];
                            }
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
                            class="form-control" value="{{ old('discount', $invoice->discount) }}">
                        @error('discount')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label>Tax</label>
                        <input type="number" step="0.01" min="0" name="tax" id="tax"
                            class="form-control" value="{{ old('tax', $invoice->tax) }}">
                        @error('tax')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label>Grand Total</label>
                        <input type="text" id="grand_total_view" class="form-control" readonly>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="main-btn primary-btn btn-hover">
                    Update
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
            const nominalInput = document.getElementById('nominal_proyek');
            const nominalInputDisplay = document.getElementById('nominal_proyek_display');
            const sisaTagihanView = document.getElementById('sisa_tagihan_view');
            const totalInvoiceLain = Number({{ $totalInvoiceLain }});

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
                const nominalProyek = parseFloat(nominalInput.value || 0);
                const sisaNominal = nominalProyek - totalInvoiceLain - grandTotal;

                document.getElementById('sub_total_view').value = formatRupiah(subTotal);
                document.getElementById('grand_total_view').value = formatRupiah(grandTotal);
                sisaTagihanView.value = formatRupiah(sisaNominal);
            }

            nominalInputDisplay.value = formatNominalDisplay(nominalInput.value);
            calculateTotals();

            nominalInputDisplay.addEventListener('input', function() {
                const raw = parseNominalDisplay(nominalInputDisplay.value);
                nominalInput.value = raw;
                nominalInputDisplay.value = formatNominalDisplay(raw);
                calculateTotals();
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

            let itemIndex = {{ count($oldItems) }};

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
