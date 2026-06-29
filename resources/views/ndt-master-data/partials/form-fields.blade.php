@if (in_array($type, ['procedures', 'criteria', 'standards'], true))
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Code</label>
            <input type="text" name="code" class="form-control" value="{{ old('code', $record->code ?? '') }}" required>
        </div>
        <div class="col-md-8">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $record->name ?? '') }}">
        </div>
        <div class="col-12">
            <label class="form-label">Keterangan</label>
            <textarea name="description" class="form-control" rows="2">{{ old('description', $record->description ?? '') }}</textarea>
        </div>
        <div class="col-12">
            <div class="form-check">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                    {{ old('is_active', $record->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label">Aktif</label>
            </div>
        </div>
    </div>
@elseif ($type === 'descriptions')
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Description Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $record->name ?? '') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Sketch Image</label>
            <input type="file" name="sketch" class="form-control" accept="image/*">
        </div>
        <div class="col-12">
            <label class="form-label">Keterangan</label>
            <textarea name="description" class="form-control" rows="2">{{ old('description', $record->description ?? '') }}</textarea>
        </div>
        <div class="col-12">
            <div class="form-check">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                    {{ old('is_active', $record->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label">Aktif</label>
            </div>
        </div>
    </div>
@elseif ($type === 'certificates')
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Judul Sertifikat</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $record->title ?? '') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Nomor Sertifikat</label>
            <input type="text" name="certificate_no" class="form-control" value="{{ old('certificate_no', $record->certificate_no ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Type</label>
            <input type="text" name="type" class="form-control" value="{{ old('type', $record->type ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Issued At</label>
            <input type="date" name="issued_at" class="form-control"
                value="{{ old('issued_at', optional($record->issued_at ?? null)->format('Y-m-d')) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Expired At</label>
            <input type="date" name="expired_at" class="form-control"
                value="{{ old('expired_at', optional($record->expired_at ?? null)->format('Y-m-d')) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">File Sertifikat</label>
            <input type="file" name="certificate_file" class="form-control" accept=".pdf,image/*">
            <small class="text-muted">PDF akan otomatis dibuat preview gambar dari halaman pertama untuk report.</small>
        </div>
        <div class="col-md-6 d-flex align-items-end">
            <div class="form-check">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                    {{ old('is_active', $record->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label">Aktif</label>
            </div>
        </div>
    </div>
@else
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Role Approval</label>
            <select name="role" class="form-select" required>
                <option value="">- Pilih Role -</option>
                @foreach ($approvalRoles as $value => $label)
                    <option value="{{ $value }}" @selected(old('role', $record->role ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $record->name ?? '') }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Jabatan</label>
            <input type="text" name="position" class="form-control" value="{{ old('position', $record->position ?? '') }}">
        </div>
        <div class="col-12">
            <div class="form-check">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                    {{ old('is_active', $record->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label">Aktif</label>
            </div>
        </div>
    </div>
@endif
