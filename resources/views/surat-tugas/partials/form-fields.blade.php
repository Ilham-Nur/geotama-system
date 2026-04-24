<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Proyek</label>
        <select name="proyek_id" class="form-select" required>
            <option value="">- Pilih Proyek -</option>
            @foreach ($proyekList as $proyek)
                <option value="{{ $proyek->id }}">{{ $proyek->no_proyek }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Tanggal Berangkat</label>
        <input type="date" name="tanggal_berangkat" class="form-control" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Tanggal Kembali</label>
        <input type="date" name="tanggal_kembali" class="form-control" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Transportasi</label>
        <input type="text" name="transportasi" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Grand Total (Auto)</label>
        <input type="text" class="form-control grand-total-display" value="Rp 0" readonly>
    </div>

    <div class="col-md-12">
        <label class="form-label">Keterangan</label>
        <textarea name="keterangan" rows="2" class="form-control"></textarea>
    </div>

    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label mb-0">Biaya Item</label>
            <button type="button" class="btn btn-sm btn-info btn-add-row">+ Tambah Item</button>
        </div>
        <div class="biaya-wrapper">
            <div class="row g-2 biaya-row mb-2">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="items[0][deskripsi]" placeholder="Deskripsi" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="items[0][qty]" min="1" placeholder="Qty" required>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control item-total-display" placeholder="Total" required>
                    <input type="hidden" class="item-total-hidden" name="items[0][total]" value="0">
                </div>
                <div class="col-md-1 d-grid">
                    <button type="button" class="btn btn-danger btn-remove-row">-</button>
                </div>
            </div>
        </div>
    </div>
</div>
