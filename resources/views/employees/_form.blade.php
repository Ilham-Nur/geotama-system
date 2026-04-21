@php
    $hasUser = old('create_system_account', isset($employee) && $employee->user_id ? '1' : '0') === '1';
    $accountRole = old('role', isset($employee) && $employee->user ? optional($employee->user->roles->first())->name : null);
    $oldWorkExperiences = old('work_experiences', isset($employee) ? $employee->workExperiences->map(fn($item) => [
        'id' => $item->id,
        'company_name' => $item->company_name,
        'position' => $item->position,
        'start_year' => $item->start_year,
        'end_year' => $item->end_year,
        'certificate_file_name' => $item->certificate_file_name,
    ])->toArray() : []);
    $oldCertificates = old('certificates', isset($employee) ? $employee->certificates->map(fn($item) => [
        'id' => $item->id,
        'certificate_type' => $item->certificate_type,
        'certificate_name' => $item->certificate_name,
        'issuer' => $item->issuer,
        'issued_at' => optional($item->issued_at)->format('Y-m-d'),
        'expired_at' => optional($item->expired_at)->format('Y-m-d'),
        'file_name' => $item->file_name,
    ])->toArray() : []);
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Gagal menyimpan data:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<h6 class="mb-3">1. Data Diri Karyawan</h6>
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Kode Karyawan / NIK</label>
        <input type="text" name="employee_code" value="{{ old('employee_code', $employee->employee_code ?? '') }}"
            class="form-control @error('employee_code') is-invalid @enderror" required>
        @error('employee_code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="full_name" value="{{ old('full_name', $employee->full_name ?? '') }}"
            class="form-control @error('full_name') is-invalid @enderror" required>
        @error('full_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Posisi</label>
        <input type="text" name="position" value="{{ old('position', $employee->position ?? '') }}"
            class="form-control @error('position') is-invalid @enderror">
    </div>

    <div class="col-md-6">
        <label class="form-label">No Telepon</label>
        <input type="text" name="phone" value="{{ old('phone', $employee->phone ?? '') }}"
            class="form-control @error('phone') is-invalid @enderror">
    </div>

    <div class="col-md-6">
        <label class="form-label">Tempat Lahir</label>
        <input type="text" name="birth_place" value="{{ old('birth_place', $employee->birth_place ?? '') }}"
            class="form-control @error('birth_place') is-invalid @enderror">
    </div>

    <div class="col-md-6">
        <label class="form-label">Tanggal Lahir</label>
        <input type="date" name="birth_date"
            value="{{ old('birth_date', isset($employee->birth_date) ? $employee->birth_date->format('Y-m-d') : '') }}"
            class="form-control @error('birth_date') is-invalid @enderror">
    </div>

    <div class="col-md-6">
        <label class="form-label">Kontak Darurat (Nama)</label>
        <input type="text" name="emergency_contact_name"
            value="{{ old('emergency_contact_name', $employee->emergency_contact_name ?? '') }}"
            class="form-control @error('emergency_contact_name') is-invalid @enderror">
    </div>

    <div class="col-md-6">
        <label class="form-label">Kontak Darurat (No Telp)</label>
        <input type="text" name="emergency_contact_phone"
            value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone ?? '') }}"
            class="form-control @error('emergency_contact_phone') is-invalid @enderror">
    </div>

    <div class="col-md-6">
        <label class="form-label">No. KTP</label>
        <input type="text" name="identity_number" value="{{ old('identity_number', $employee->identity_number ?? '') }}"
            class="form-control @error('identity_number') is-invalid @enderror">
    </div>

    <div class="col-md-6">
        <label class="form-label">No. BPJS Ketenagakerjaan</label>
        <input type="text" name="bpjs_ketenagakerjaan_number"
            value="{{ old('bpjs_ketenagakerjaan_number', $employee->bpjs_ketenagakerjaan_number ?? '') }}"
            class="form-control @error('bpjs_ketenagakerjaan_number') is-invalid @enderror">
    </div>

    <div class="col-md-6">
        <label class="form-label">No. BPJS Kesehatan</label>
        <input type="text" name="bpjs_kesehatan_number"
            value="{{ old('bpjs_kesehatan_number', $employee->bpjs_kesehatan_number ?? '') }}"
            class="form-control @error('bpjs_kesehatan_number') is-invalid @enderror">
    </div>

    <div class="col-md-6">
        <label class="form-label">Tanggal Bergabung</label>
        <input type="date" name="hire_date"
            value="{{ old('hire_date', isset($employee->hire_date) ? $employee->hire_date->format('Y-m-d') : '') }}"
            class="form-control @error('hire_date') is-invalid @enderror">
    </div>

    <div class="col-md-6">
        <label class="form-label">Status Kepegawaian</label>
        <select name="employment_status" class="form-select @error('employment_status') is-invalid @enderror" required>
            @foreach (['tetap' => 'Tetap', 'kontrak' => 'Kontrak', 'magang' => 'Magang'] as $value => $label)
                <option value="{{ $value }}"
                    {{ old('employment_status', $employee->employment_status ?? 'kontrak') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Jenis Kelamin</label>
        <select name="gender" class="form-select @error('gender') is-invalid @enderror">
            <option value="">-- Pilih Jenis Kelamin --</option>
            <option value="laki-laki" {{ old('gender', $employee->gender ?? '') === 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
            <option value="perempuan" {{ old('gender', $employee->gender ?? '') === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Status Pernikahan</label>
        <select name="marital_status" class="form-select @error('marital_status') is-invalid @enderror">
            <option value="">-- Pilih Status Pernikahan --</option>
            @foreach (['belum_kawin' => 'Belum Kawin','kawin' => 'Kawin','cerai_hidup' => 'Cerai Hidup','cerai_mati' => 'Cerai Mati'] as $value => $label)
                <option value="{{ $value }}" {{ old('marital_status', $employee->marital_status ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Kewarganegaraan</label>
        <input type="text" name="nationality" value="{{ old('nationality', $employee->nationality ?? '') }}"
            class="form-control @error('nationality') is-invalid @enderror">
    </div>

    <div class="col-md-6">
        <label class="form-label">Agama</label>
        <input type="text" name="religion" value="{{ old('religion', $employee->religion ?? '') }}"
            class="form-control @error('religion') is-invalid @enderror">
    </div>

    <div class="col-12">
        <label class="form-label">Alamat Lengkap</label>
        <textarea name="full_address" rows="3" class="form-control @error('full_address') is-invalid @enderror">{{ old('full_address', $employee->full_address ?? '') }}</textarea>
    </div>

    <div class="col-12">
        <label class="form-label">Informasi Penting Lainnya</label>
        <textarea name="important_information" rows="3" class="form-control @error('important_information') is-invalid @enderror">{{ old('important_information', $employee->important_information ?? '') }}</textarea>
    </div>
</div>

<hr class="my-4">

<h6 class="mb-3">2. Data Pendidikan</h6>
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Ijazah Terakhir</label>
        <input type="text" name="last_education" value="{{ old('last_education', $employee->last_education ?? '') }}"
            class="form-control @error('last_education') is-invalid @enderror" placeholder="Contoh: S1 Teknik Industri">
    </div>
    <div class="col-md-6">
        <label class="form-label">Upload Berkas Ijazah</label>
        <input type="file" name="last_education_file" class="form-control @error('last_education_file') is-invalid @enderror"
            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
        @if (!empty($employee?->last_education_file_path))
            <small class="text-muted d-block mt-1">File saat ini: <a href="{{ asset('storage/' . $employee->last_education_file_path) }}" target="_blank">{{ $employee->last_education_file_name }}</a></small>
        @endif
    </div>
</div>

<hr class="my-4">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">3. Pengalaman Kerja</h6>
    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-work-experience">+ Tambah</button>
</div>
<div id="work-experience-wrapper">
    @forelse($oldWorkExperiences as $index => $work)
        <div class="border rounded p-3 mb-3 work-item">
            <div class="row g-3">
                @if (!empty($work['id']))
                    <input type="hidden" name="work_experiences[{{ $index }}][id]" value="{{ $work['id'] }}">
                @endif
                <div class="col-md-4"><input type="text" name="work_experiences[{{ $index }}][company_name]" class="form-control" placeholder="Nama PT" value="{{ $work['company_name'] ?? '' }}"></div>
                <div class="col-md-4"><input type="text" name="work_experiences[{{ $index }}][position]" class="form-control" placeholder="Posisi" value="{{ $work['position'] ?? '' }}"></div>
                <div class="col-md-2"><input type="number" name="work_experiences[{{ $index }}][start_year]" class="form-control" placeholder="Mulai" value="{{ $work['start_year'] ?? '' }}"></div>
                <div class="col-md-2"><input type="number" name="work_experiences[{{ $index }}][end_year]" class="form-control" placeholder="Selesai" value="{{ $work['end_year'] ?? '' }}"></div>
                <div class="col-md-10"><input type="file" name="work_experience_files[{{ $index }}]" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></div>
                <div class="col-md-2 text-end"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row">Hapus</button></div>
            </div>
            @if (!empty($work['certificate_file_name']))
                <small class="text-muted">File saat ini: {{ $work['certificate_file_name'] }}</small>
            @endif
        </div>
    @empty
    @endforelse
</div>

<hr class="my-4">

<div class="d-flex justify-content-between align-items-center mb-2">
    <h6 class="mb-0">4. Sertifikat yang Dimiliki</h6>
    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-certificate">+ Tambah</button>
</div>
<p class="text-muted small mb-3">Notice: sertifikat yang memiliki tanggal expired dan akan kedaluwarsa dalam 3 bulan akan ditandai di halaman detail.</p>
<div id="certificate-wrapper">
    @forelse($oldCertificates as $index => $certificate)
        <div class="border rounded p-3 mb-3 certificate-item bg-light">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge {{ ($certificate['certificate_type'] ?? 'internal') === 'external' ? 'bg-warning text-dark' : 'bg-secondary' }}">
                    {{ strtoupper($certificate['certificate_type'] ?? 'internal') }}
                </span>
                <small class="text-muted">Sertifikat #{{ $index + 1 }}</small>
            </div>
            <div class="row g-3">
                @if (!empty($certificate['id']))
                    <input type="hidden" name="certificates[{{ $index }}][id]" value="{{ $certificate['id'] }}">
                @endif
                <div class="col-md-3">
                    <label class="form-label">Jenis</label>
                    <select name="certificates[{{ $index }}][certificate_type]" class="form-select">
                        <option value="internal" {{ ($certificate['certificate_type'] ?? '') === 'internal' ? 'selected' : '' }}>Internal</option>
                        <option value="external" {{ ($certificate['certificate_type'] ?? '') === 'external' ? 'selected' : '' }}>External</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Nama Sertifikat</label>
                    <input type="text" name="certificates[{{ $index }}][certificate_name]" class="form-control" placeholder="Contoh: Pelatihan APAR" value="{{ $certificate['certificate_name'] ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Penerbit</label>
                    <input type="text" name="certificates[{{ $index }}][issuer]" class="form-control" placeholder="Contoh: MT PT / Internal Perusahaan" value="{{ $certificate['issuer'] ?? '' }}">
                </div>
                <div class="col-md-3"><label class="form-label">Tgl Terbit</label><input type="date" name="certificates[{{ $index }}][issued_at]" class="form-control" value="{{ $certificate['issued_at'] ?? '' }}"></div>
                <div class="col-md-3"><label class="form-label">Tgl Expired</label><input type="date" name="certificates[{{ $index }}][expired_at]" class="form-control" value="{{ $certificate['expired_at'] ?? '' }}"></div>
                <div class="col-md-4"><label class="form-label">Berkas</label><input type="file" name="certificate_files[{{ $index }}]" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></div>
                <div class="col-md-2 text-end d-flex align-items-end justify-content-end"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row">Hapus</button></div>
            </div>
            @if (!empty($certificate['file_name']))
                <small class="text-muted">File saat ini: {{ $certificate['file_name'] }}</small>
            @endif
        </div>
    @empty
    @endforelse
</div>

<hr class="my-4">

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
        <h6 class="mb-1">Akun Sistem</h6>
        <small class="text-muted">Aktifkan jika karyawan ini perlu login ke sistem.</small>
    </div>
    <div class="form-check form-switch m-0">
        <input class="form-check-input" type="checkbox" id="create_system_account" name="create_system_account" value="1" {{ $hasUser ? 'checked' : '' }}>
        <label class="form-check-label" for="create_system_account">Buat / Kelola Akun</label>
    </div>
</div>

<div id="system-account-section" class="border rounded p-3 {{ $hasUser ? '' : 'd-none' }}">
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Username</label><input type="text" name="username" value="{{ old('username', isset($employee) && $employee->user ? $employee->user->username : '') }}" class="form-control @error('username') is-invalid @enderror"></div>
        <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email', isset($employee) && $employee->user ? $employee->user->email : '') }}" class="form-control @error('email') is-invalid @enderror"></div>
        <div class="col-md-6"><label class="form-label">Password {{ isset($employee) ? '(Kosongkan jika tidak diubah)' : '' }}</label><input type="password" name="password" class="form-control @error('password') is-invalid @enderror"></div>
        <div class="col-md-6"><label class="form-label">Konfirmasi Password</label><input type="password" name="password_confirmation" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Role</label><select name="role" class="form-select @error('role') is-invalid @enderror"><option value="">-- Pilih Role --</option>@foreach ($roles as $role)<option value="{{ $role->name }}" {{ $accountRole === $role->name ? 'selected' : '' }}>{{ $role->name }}</option>@endforeach</select></div>
    </div>
</div>

@push('scripts')
    <script>
        $(function() {
            const toggle = $('#create_system_account');
            const section = $('#system-account-section');
            const workWrapper = $('#work-experience-wrapper');
            const certificateWrapper = $('#certificate-wrapper');
            let workIndex = {{ count($oldWorkExperiences) }};
            let certificateIndex = {{ count($oldCertificates) }};

            function syncAccountSection() { section.toggleClass('d-none', !toggle.is(':checked')); }

            $('#btn-add-work-experience').on('click', function() {
                workWrapper.append(`<div class="border rounded p-3 mb-3 work-item"><div class="row g-3"><div class="col-md-4"><input type="text" name="work_experiences[${workIndex}][company_name]" class="form-control" placeholder="Nama PT"></div><div class="col-md-4"><input type="text" name="work_experiences[${workIndex}][position]" class="form-control" placeholder="Posisi"></div><div class="col-md-2"><input type="number" name="work_experiences[${workIndex}][start_year]" class="form-control" placeholder="Mulai"></div><div class="col-md-2"><input type="number" name="work_experiences[${workIndex}][end_year]" class="form-control" placeholder="Selesai"></div><div class="col-md-10"><input type="file" name="work_experience_files[${workIndex}]" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></div><div class="col-md-2 text-end"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row">Hapus</button></div></div></div>`);
                workIndex++;
            });

            $('#btn-add-certificate').on('click', function() {
                certificateWrapper.append(`<div class="border rounded p-3 mb-3 certificate-item bg-light"><div class="d-flex justify-content-between align-items-center mb-3"><span class="badge bg-secondary">INTERNAL</span><small class="text-muted">Sertifikat #${certificateIndex + 1}</small></div><div class="row g-3"><div class="col-md-3"><label class="form-label">Jenis</label><select name="certificates[${certificateIndex}][certificate_type]" class="form-select"><option value="internal">Internal</option><option value="external">External</option></select></div><div class="col-md-5"><label class="form-label">Nama Sertifikat</label><input type="text" name="certificates[${certificateIndex}][certificate_name]" class="form-control" placeholder="Contoh: Pelatihan APAR"></div><div class="col-md-4"><label class="form-label">Penerbit</label><input type="text" name="certificates[${certificateIndex}][issuer]" class="form-control" placeholder="Contoh: MT PT / Internal Perusahaan"></div><div class="col-md-3"><label class="form-label">Tgl Terbit</label><input type="date" name="certificates[${certificateIndex}][issued_at]" class="form-control"></div><div class="col-md-3"><label class="form-label">Tgl Expired</label><input type="date" name="certificates[${certificateIndex}][expired_at]" class="form-control"></div><div class="col-md-4"><label class="form-label">Berkas</label><input type="file" name="certificate_files[${certificateIndex}]" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></div><div class="col-md-2 text-end d-flex align-items-end justify-content-end"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row">Hapus</button></div></div></div>`);
                certificateIndex++;
            });

            $(document).on('change', 'select[name^="certificates"][name$="[certificate_type]"]', function() {
                const wrapper = $(this).closest('.certificate-item');
                const badge = wrapper.find('.badge').first();
                if ($(this).val() === 'external') {
                    badge.removeClass('bg-secondary').addClass('bg-warning text-dark').text('EXTERNAL');
                } else {
                    badge.removeClass('bg-warning text-dark').addClass('bg-secondary').text('INTERNAL');
                }
            });

            $(document).on('click', '.btn-remove-row', function() { $(this).closest('.work-item, .certificate-item').remove(); });

            toggle.on('change', syncAccountSection);
            syncAccountSection();
        });
    </script>
@endpush
