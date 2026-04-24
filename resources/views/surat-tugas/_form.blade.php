@php($suratTugas = $suratTugas ?? null)
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">No Surat</label>
        <input type="text" name="no_surat" class="form-control @error('no_surat') is-invalid @enderror"
            value="{{ old('no_surat', $suratTugas->no_surat ?? '') }}" required>
        @error('no_surat')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Proyek</label>
        <select name="proyek_id" class="form-control @error('proyek_id') is-invalid @enderror" required>
            <option value="">-- Pilih Proyek --</option>
            @foreach ($proyeks as $proyek)
                <option value="{{ $proyek->id }}"
                    @selected(old('proyek_id', $suratTugas->proyek_id ?? '') == $proyek->id)>
                    {{ $proyek->no_proyek }}
                </option>
            @endforeach
        </select>
        @error('proyek_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Tanggal Berangkat</label>
        <input type="date" name="tanggal_berangkat" class="form-control @error('tanggal_berangkat') is-invalid @enderror"
            value="{{ old('tanggal_berangkat', optional($suratTugas->tanggal_berangkat ?? null)->format('Y-m-d')) }}" required>
        @error('tanggal_berangkat')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Tanggal Kembali</label>
        <input type="date" name="tanggal_kembali" class="form-control @error('tanggal_kembali') is-invalid @enderror"
            value="{{ old('tanggal_kembali', optional($suratTugas->tanggal_kembali ?? null)->format('Y-m-d')) }}" required>
        @error('tanggal_kembali')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Transportasi</label>
        <input type="text" name="transportasi" class="form-control @error('transportasi') is-invalid @enderror"
            value="{{ old('transportasi', $suratTugas->transportasi ?? '') }}">
        @error('transportasi')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Keterangan</label>
        <input type="text" name="keterangan" class="form-control @error('keterangan') is-invalid @enderror"
            value="{{ old('keterangan', $suratTugas->keterangan ?? '') }}">
        @error('keterangan')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<hr class="my-4">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">Item Biaya</h6>
    <button type="button" class="btn btn-sm btn-primary" id="btn-add-item">+ Tambah Item</button>
</div>

<div class="table-responsive">
    <table class="table table-bordered" id="tableBiayaItem">
        <thead>
            <tr>
                <th>Deskripsi</th>
                <th style="width: 140px;">Qty</th>
                <th style="width: 220px;">Total</th>
                <th style="width: 80px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $defaultItems = [['deskripsi' => '', 'qty' => 1, 'total' => 0]];
                $existingItems = $suratTugas && $suratTugas->relationLoaded('biayaItems')
                    ? $suratTugas->biayaItems->toArray()
                    : [];
                $items = old('items', !empty($existingItems) ? $existingItems : $defaultItems);
            @endphp

            @foreach ($items as $index => $item)
                <tr>
                    <td><input type="text" name="items[{{ $index }}][deskripsi]" class="form-control"
                            value="{{ $item['deskripsi'] ?? '' }}" required></td>
                    <td><input type="number" step="0.01" min="0" name="items[{{ $index }}][qty]" class="form-control"
                            value="{{ $item['qty'] ?? 1 }}" required></td>
                    <td><input type="number" step="0.01" min="0" name="items[{{ $index }}][total]"
                            class="form-control input-total" value="{{ $item['total'] ?? 0 }}" required></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger btn-remove-item">X</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-end">Grand Total</th>
                <th id="grandTotalText">Rp 0</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>

<div class="mt-4 d-flex justify-content-end gap-2">
    <a href="{{ route('surat-tugas.index') }}" class="btn btn-outline-secondary">Batal</a>
    <button type="submit" class="btn btn-primary">Simpan</button>
</div>

@push('scripts')
    <script>
        $(function() {
            const tbody = $('#tableBiayaItem tbody');

            function formatRupiah(value) {
                return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
            }

            function updateGrandTotal() {
                let total = 0;
                $('.input-total').each(function() {
                    total += parseFloat($(this).val() || 0);
                });
                $('#grandTotalText').text(formatRupiah(total));
            }

            function reindexItems() {
                tbody.find('tr').each(function(index) {
                    $(this).find('input').each(function() {
                        const oldName = $(this).attr('name');
                        if (!oldName) return;
                        const newName = oldName.replace(/items\[\d+\]/, `items[${index}]`);
                        $(this).attr('name', newName);
                    });
                });
            }

            $('#btn-add-item').on('click', function() {
                const index = tbody.find('tr').length;
                tbody.append(`
                    <tr>
                        <td><input type="text" name="items[${index}][deskripsi]" class="form-control" required></td>
                        <td><input type="number" step="0.01" min="0" name="items[${index}][qty]" class="form-control" value="1" required></td>
                        <td><input type="number" step="0.01" min="0" name="items[${index}][total]" class="form-control input-total" value="0" required></td>
                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger btn-remove-item">X</button></td>
                    </tr>
                `);
                updateGrandTotal();
            });

            $(document).on('click', '.btn-remove-item', function() {
                if (tbody.find('tr').length <= 1) {
                    return;
                }
                $(this).closest('tr').remove();
                reindexItems();
                updateGrandTotal();
            });

            $(document).on('input', '.input-total', updateGrandTotal);

            updateGrandTotal();
        });
    </script>
@endpush
