@php
    $hasUser = old('create_system_account', isset($employee) && $employee->user_id ? '1' : '0') === '1';
    $accountRole = old('role', isset($employee) && $employee->user ? optional($employee->user->roles->first())->name : null);
@endphp

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
        @error('position')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">No Telepon</label>
        <input type="text" name="phone" value="{{ old('phone', $employee->phone ?? '') }}"
            class="form-control @error('phone') is-invalid @enderror">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Tanggal Bergabung</label>
        <input type="date" name="hire_date" value="{{ old('hire_date', isset($employee->hire_date) ? $employee->hire_date->format('Y-m-d') : '') }}"
            class="form-control @error('hire_date') is-invalid @enderror">
        @error('hire_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
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
        @error('employment_status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Jenis Kelamin</label>
        <select name="gender" class="form-select @error('gender') is-invalid @enderror">
            <option value="">-- Pilih Jenis Kelamin --</option>
            <option value="laki-laki" {{ old('gender', $employee->gender ?? '') === 'laki-laki' ? 'selected' : '' }}>
                Laki-laki
            </option>
            <option value="perempuan" {{ old('gender', $employee->gender ?? '') === 'perempuan' ? 'selected' : '' }}>
                Perempuan
            </option>
        </select>
        @error('gender')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Tempat Lahir</label>
        <input type="text" name="birth_place" value="{{ old('birth_place', $employee->birth_place ?? '') }}"
            class="form-control @error('birth_place') is-invalid @enderror">
        @error('birth_place')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Tanggal Lahir</label>
        <input type="date" name="birth_date"
            value="{{ old('birth_date', isset($employee->birth_date) ? $employee->birth_date->format('Y-m-d') : '') }}"
            class="form-control @error('birth_date') is-invalid @enderror">
        @error('birth_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">No. KTP / Identitas</label>
        <input type="text" name="identity_number" value="{{ old('identity_number', $employee->identity_number ?? '') }}"
            class="form-control @error('identity_number') is-invalid @enderror">
        @error('identity_number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Status Pernikahan</label>
        <select name="marital_status" class="form-select @error('marital_status') is-invalid @enderror">
            <option value="">-- Pilih Status Pernikahan --</option>
            @foreach ([
                'belum_kawin' => 'Belum Kawin',
                'kawin' => 'Kawin',
                'cerai_hidup' => 'Cerai Hidup',
                'cerai_mati' => 'Cerai Mati',
            ] as $value => $label)
                <option value="{{ $value }}"
                    {{ old('marital_status', $employee->marital_status ?? '') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('marital_status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Kewarganegaraan</label>
        <input type="text" name="nationality" value="{{ old('nationality', $employee->nationality ?? '') }}"
            class="form-control @error('nationality') is-invalid @enderror">
        @error('nationality')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Agama</label>
        <input type="text" name="religion" value="{{ old('religion', $employee->religion ?? '') }}"
            class="form-control @error('religion') is-invalid @enderror">
        @error('religion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Alamat Lengkap</label>
        <textarea name="full_address" rows="3" class="form-control @error('full_address') is-invalid @enderror">{{ old('full_address', $employee->full_address ?? '') }}</textarea>
        @error('full_address')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<hr class="my-4">

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
        <h6 class="mb-1">Akun Sistem</h6>
        <small class="text-muted">Aktifkan jika karyawan ini perlu login ke sistem.</small>
    </div>
    <div class="form-check form-switch m-0">
        <input class="form-check-input" type="checkbox" id="create_system_account" name="create_system_account" value="1"
            {{ $hasUser ? 'checked' : '' }}>
        <label class="form-check-label" for="create_system_account">Buat / Kelola Akun</label>
    </div>
</div>

<div id="system-account-section" class="border rounded p-3 {{ $hasUser ? '' : 'd-none' }}">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Username</label>
            <input type="text" name="username"
                value="{{ old('username', isset($employee) && $employee->user ? $employee->user->username : '') }}"
                class="form-control @error('username') is-invalid @enderror">
            @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email"
                value="{{ old('email', isset($employee) && $employee->user ? $employee->user->email : '') }}"
                class="form-control @error('email') is-invalid @enderror">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Password {{ isset($employee) ? '(Kosongkan jika tidak diubah)' : '' }}</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>

        <div class="col-md-6">
            <label class="form-label">Role</label>
            <select name="role" class="form-select @error('role') is-invalid @enderror">
                <option value="">-- Pilih Role --</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}" {{ $accountRole === $role->name ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(function() {
            const toggle = $('#create_system_account');
            const section = $('#system-account-section');

            function syncAccountSection() {
                section.toggleClass('d-none', !toggle.is(':checked'));
            }

            toggle.on('change', syncAccountSection);
            syncAccountSection();
        });
    </script>
@endpush
