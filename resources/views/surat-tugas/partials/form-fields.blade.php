@php
    $selectedProyek = old('proyek_id', $suratTugas->proyek_id ?? '');
    $tanggalBerangkat = old('tanggal_berangkat', isset($suratTugas) ? optional($suratTugas->tanggal_berangkat)->format('Y-m-d') : '');
    $tanggalKembali = old('tanggal_kembali', isset($suratTugas) ? optional($suratTugas->tanggal_kembali)->format('Y-m-d') : '');
    $transportasi = old('transportasi', $suratTugas->transportasi ?? '');
    $keterangan = old('keterangan', $suratTugas->keterangan ?? '');

    $defaultItems = isset($suratTugas)
        ? $suratTugas->biayaItems->map(fn($item) => [
            'deskripsi' => $item->deskripsi,
            'qty' => $item->qty,
            'total' => (float) $item->total,
        ])->toArray()
        : [['deskripsi' => '', 'qty' => 1, 'total' => 0]];

    $items = old('items', $defaultItems);
    if (empty($items)) {
        $items = [['deskripsi' => '', 'qty' => 1, 'total' => 0]];
    }
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Proyek</label>
        <select name="proyek_id" class="form-select" required>
            <option value="">- Pilih Proyek -</option>
            @foreach ($proyekList as $proyek)
                <option value="{{ $proyek->id }}" {{ (string) $selectedProyek === (string) $proyek->id ? 'selected' : '' }}>
                    {{ $proyek->no_proyek }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Tanggal Berangkat</label>
        <input type="date" name="tanggal_berangkat" class="form-control" value="{{ $tanggalBerangkat }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Tanggal Kembali</label>
        <input type="date" name="tanggal_kembali" class="form-control" value="{{ $tanggalKembali }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Transportasi</label>
        <input type="text" name="transportasi" class="form-control" value="{{ $transportasi }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Grand Total (Auto)</label>
        <input type="text" class="form-control grand-total-display" value="Rp 0" readonly>
    </div>

    <div class="col-md-12">
        <label class="form-label">Keterangan</label>
        <textarea name="keterangan" rows="2" class="form-control">{{ $keterangan }}</textarea>
    </div>

    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label mb-0">Biaya Item</label>
            <button type="button" class="btn btn-sm btn-info btn-add-row">+ Tambah Item</button>
        </div>
        <div class="biaya-wrapper">
            @foreach ($items as $idx => $item)
                <div class="row g-2 biaya-row mb-2">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="items[{{ $idx }}][deskripsi]" placeholder="Deskripsi"
                            value="{{ $item['deskripsi'] ?? '' }}" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" name="items[{{ $idx }}][qty]" min="1" placeholder="Qty"
                            value="{{ $item['qty'] ?? 1 }}" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control item-total-display"
                            value="{{ !empty($item['total']) ? 'Rp ' . number_format((float) $item['total'], 0, ',', '.') : '' }}"
                            placeholder="Total" required>
                        <input type="hidden" class="item-total-hidden" name="items[{{ $idx }}][total]"
                            value="{{ $item['total'] ?? 0 }}">
                    </div>
                    <div class="col-md-1 d-grid">
                        <button type="button" class="btn btn-danger btn-remove-row">-</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
