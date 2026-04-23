@php
    $isEdit = isset($quotation);

    $oldItems = old('items');
    if ($oldItems) {
        $itemsData = $oldItems;
    } elseif ($isEdit && $quotation->items->isNotEmpty()) {
        $itemsData = $quotation->items
            ->map(function ($item) {
                return [
                    'description' => $item->description,
                    'satuan' => $item->satuan,
                    'qty' => $item->qty,
                    'total' => $item->total,
                ];
            })
            ->toArray();
    } else {
        $itemsData = [['description' => '', 'satuan' => '', 'qty' => '', 'total' => '']];
    }

    $oldTerms = old('terms');
    if ($oldTerms) {
        $termsData = $oldTerms;
    } elseif ($isEdit && $quotation->terms->isNotEmpty()) {
        $termsData = $quotation->terms->map(fn($term) => ['name' => $term->name])->toArray();
    } else {
        $termsData = [['name' => '']];
    }
@endphp

<div class="row">
    @if (!$isEdit)
        <div class="col-md-12 mb-3">
            <label class="form-label">Gunakan Quotation Sebelumnya (by No Quotation)</label>
            <select id="previous_quotation_id" class="form-select">
                <option value="">-- Pilih No Quotation --</option>
                @foreach ($previousQuotations ?? [] as $previousQuotation)
                    <option value="{{ $previousQuotation->id }}"
                        data-quotation='@json([
                            'client_id' => $previousQuotation->client_id,
                            'items' => $previousQuotation->items
                                ->map(function ($item) {
                                    return [
                                        'description' => $item->description,
                                        'satuan' => $item->satuan,
                                        'qty' => $item->qty,
                                        'total' => $item->total,
                                    ];
                                })
                                ->values()
                                ->all(),
                            'terms' => $previousQuotation->terms
                                ->map(function ($term) {
                                    return [
                                        'name' => $term->name,
                                    ];
                                })
                                ->values()
                                ->all(),
                        ])'>
                        {{ $previousQuotation->no_quo }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted">Pilih quotation sebelumnya untuk auto-isi client, item, dan terms.</small>
        </div>
    @endif

    <div class="col-md-6 mb-3">
        <label class="form-label">No Quotation</label>
        <input type="text" name="no_quo" class="form-control @error('no_quo') is-invalid @enderror"
            value="{{ old('no_quo', $quotation->no_quo ?? $generatedNoQuotation ?? '') }}" required>
        @error('no_quo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Tanggal</label>
        <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror"
            value="{{ old('tanggal', isset($quotation) ? optional($quotation->tanggal)->format('Y-m-d') : now()->toDateString()) }}"
            required>
        @error('tanggal')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Client</label>
        <select name="client_id" id="client_id" class="form-select @error('client_id') is-invalid @enderror">
            <option value="">-- Pilih Client --</option>
            @foreach ($clients as $client)
                <option value="{{ $client->id }}"
                    {{ (string) old('client_id', $quotation->client_id ?? '') === (string) $client->id ? 'selected' : '' }}>
                    {{ $client->nama_perusahaan }} - {{ $client->nama_pic }}
                </option>
            @endforeach
        </select>
        @error('client_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<hr>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h6>Item Quotation</h6>
    <button type="button" class="btn btn-sm btn-primary" id="btn-add-item">+ Tambah Item</button>
</div>

<div class="table-wrapper table-responsive mb-3">
    <table class="table" id="quotation-items-table">
        <thead>
            <tr>
                <th><h6>Description</h6></th>
                <th><h6>Satuan</h6></th>
                <th><h6>Qty</h6></th>
                <th><h6>Total</h6></th>
                <th><h6>Aksi</h6></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($itemsData as $i => $item)
                <tr>
                    <td>
                        <textarea name="items[{{ $i }}][description]" class="form-control" rows="2" required>{{ $item['description'] ?? '' }}</textarea>
                    </td>
                    <td>
                        <input type="text" name="items[{{ $i }}][satuan]" class="form-control"
                            value="{{ $item['satuan'] ?? '' }}">
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0.01" name="items[{{ $i }}][qty]"
                            class="form-control" value="{{ $item['qty'] ?? '' }}" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0" name="items[{{ $i }}][total]"
                            class="form-control item-total" value="{{ $item['total'] ?? '' }}" required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger btn-remove-item">Hapus</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@error('items')
    <div class="text-danger small mb-3">{{ $message }}</div>
@enderror

<div class="text-end mb-4">
    <h6>Grand Total: <span id="grand-total-label">Rp 0</span></h6>
</div>

<hr>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h6>Quotation Terms</h6>
    <button type="button" class="btn btn-sm btn-primary" id="btn-add-term">+ Tambah Terms</button>
</div>

<div id="terms-wrapper" class="mb-4">
    @foreach ($termsData as $i => $term)
        <div class="input-group mb-2 term-row">
            <input type="text" name="terms[{{ $i }}][name]" class="form-control"
                value="{{ $term['name'] ?? '' }}" placeholder="Contoh: Harga belum termasuk PPN">
            <button type="button" class="btn btn-outline-danger btn-remove-term">Hapus</button>
        </div>
    @endforeach
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Simpan' }}</button>
    <a href="{{ route('quotation.index') }}" class="btn btn-secondary">Kembali</a>
</div>

@push('scripts')
    <script>
        $(function() {
            const itemTableBody = $('#quotation-items-table tbody');
            const termsWrapper = $('#terms-wrapper');

            function formatRupiah(number) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(number || 0);
            }

            function refreshGrandTotal() {
                let total = 0;
                $('.item-total').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#grand-total-label').text(formatRupiah(total));
            }

            function reindexRows() {
                itemTableBody.find('tr').each(function(index) {
                    $(this).find('textarea, input').each(function() {
                        const name = $(this).attr('name');
                        if (!name) return;
                        $(this).attr('name', name.replace(/items\[\d+\]/, `items[${index}]`));
                    });
                });
            }

            function reindexTerms() {
                termsWrapper.find('.term-row').each(function(index) {
                    $(this).find('input').attr('name', `terms[${index}][name]`);
                });
            }

            function getItemRow(index, item = {}) {
                return `
                    <tr>
                        <td><textarea name="items[${index}][description]" class="form-control" rows="2" required>${item.description ?? ''}</textarea></td>
                        <td><input type="text" name="items[${index}][satuan]" class="form-control" value="${item.satuan ?? ''}"></td>
                        <td><input type="number" step="0.01" min="0.01" name="items[${index}][qty]" class="form-control" value="${item.qty ?? ''}" required></td>
                        <td><input type="number" step="0.01" min="0" name="items[${index}][total]" class="form-control item-total" value="${item.total ?? ''}" required></td>
                        <td><button type="button" class="btn btn-sm btn-danger btn-remove-item">Hapus</button></td>
                    </tr>
                `;
            }

            function getTermRow(index, term = {}) {
                return `
                    <div class="input-group mb-2 term-row">
                        <input type="text" name="terms[${index}][name]" class="form-control"
                            value="${term.name ?? ''}" placeholder="Contoh: Harga belum termasuk PPN">
                        <button type="button" class="btn btn-outline-danger btn-remove-term">Hapus</button>
                    </div>
                `;
            }

            $('#btn-add-item').on('click', function() {
                const index = itemTableBody.find('tr').length;
                itemTableBody.append(getItemRow(index));
            });

            itemTableBody.on('click', '.btn-remove-item', function() {
                if (itemTableBody.find('tr').length === 1) {
                    return;
                }

                $(this).closest('tr').remove();
                reindexRows();
                refreshGrandTotal();
            });

            itemTableBody.on('input', '.item-total', refreshGrandTotal);

            $('#btn-add-term').on('click', function() {
                const index = termsWrapper.find('.term-row').length;
                termsWrapper.append(getTermRow(index));
            });

            termsWrapper.on('click', '.btn-remove-term', function() {
                if (termsWrapper.find('.term-row').length === 1) {
                    $(this).closest('.term-row').find('input').val('');
                    return;
                }

                $(this).closest('.term-row').remove();
                reindexTerms();
            });

            $('#previous_quotation_id').on('change', function() {
                const selected = $(this).find(':selected');
                const payload = selected.data('quotation');

                if (!payload) {
                    return;
                }

                $('#client_id').val(payload.client_id ?? '').trigger('change');

                const items = Array.isArray(payload.items) && payload.items.length ? payload.items : [{
                    description: '',
                    satuan: '',
                    qty: '',
                    total: ''
                }];

                itemTableBody.empty();
                items.forEach((item, index) => {
                    itemTableBody.append(getItemRow(index, item));
                });

                const terms = Array.isArray(payload.terms) && payload.terms.length ? payload.terms : [{
                    name: ''
                }];

                termsWrapper.empty();
                terms.forEach((term, index) => {
                    termsWrapper.append(getTermRow(index, term));
                });

                refreshGrandTotal();

                Swal.fire({
                    icon: 'success',
                    title: 'Template quotation dimuat',
                    text: 'Data client, item, dan terms berhasil diisi otomatis.',
                    timer: 1600,
                    showConfirmButton: false,
                });
            });

            refreshGrandTotal();
        });
    </script>
@endpush
