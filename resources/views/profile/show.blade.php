@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2>Profil Karyawan</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Profil Saya</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card-style mb-30">
                <h6 class="mb-25">Data Profil</h6>
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror"
                                value="{{ old('full_name', $employee->full_name) }}" required>
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">No Telepon</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                value="{{ old('phone', $employee->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="laki-laki" {{ old('gender', $employee->gender) === 'laki-laki' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="perempuan" {{ old('gender', $employee->gender) === 'perempuan' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="birth_place" class="form-control @error('birth_place') is-invalid @enderror"
                                value="{{ old('birth_place', $employee->birth_place) }}">
                            @error('birth_place')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror"
                                value="{{ old('birth_date', optional($employee->birth_date)->format('Y-m-d')) }}">
                            @error('birth_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nomor Identitas / KTP</label>
                            <input type="text" name="identity_number"
                                class="form-control @error('identity_number') is-invalid @enderror"
                                value="{{ old('identity_number', $employee->identity_number) }}">
                            @error('identity_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status Perkawinan</label>
                            <select name="marital_status" class="form-select @error('marital_status') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="belum_kawin"
                                    {{ old('marital_status', $employee->marital_status) === 'belum_kawin' ? 'selected' : '' }}>
                                    Belum Kawin</option>
                                <option value="kawin" {{ old('marital_status', $employee->marital_status) === 'kawin' ? 'selected' : '' }}>
                                    Kawin</option>
                                <option value="cerai_hidup"
                                    {{ old('marital_status', $employee->marital_status) === 'cerai_hidup' ? 'selected' : '' }}>
                                    Cerai Hidup</option>
                                <option value="cerai_mati"
                                    {{ old('marital_status', $employee->marital_status) === 'cerai_mati' ? 'selected' : '' }}>
                                    Cerai Mati</option>
                            </select>
                            @error('marital_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kewarganegaraan</label>
                            <input type="text" name="nationality"
                                class="form-control @error('nationality') is-invalid @enderror"
                                value="{{ old('nationality', $employee->nationality) }}">
                            @error('nationality')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Agama</label>
                            <input type="text" name="religion" class="form-control @error('religion') is-invalid @enderror"
                                value="{{ old('religion', $employee->religion) }}">
                            @error('religion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="full_address" rows="3" class="form-control @error('full_address') is-invalid @enderror">{{ old('full_address', $employee->full_address) }}</textarea>
                            @error('full_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Foto Profil</label>
                            <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror"
                                accept=".jpg,.jpeg,.png">
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Ukuran maksimal 2MB (JPG/PNG).</small>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-primary" type="submit">Simpan Profil</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-style mb-30 text-center">
                <h6 class="mb-3">Foto Profil</h6>
                <img src="{{ $employee->photo_path ? asset('storage/' . $employee->photo_path) : asset('template/assets/images/profile/profile-image.png') }}"
                    class="img-fluid rounded-circle border" style="width:140px; height:140px; object-fit:cover;">
                <p class="mt-3 mb-0 fw-bold">{{ $employee->full_name }}</p>
                <small class="text-muted">{{ auth()->user()->username }}</small>
            </div>

            <div class="card-style mb-30">
                <h6 class="mb-3">Upload Dokumen / Sertifikat</h6>
                <form action="{{ route('profile.documents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Label Dokumen</label>
                        <input type="text" name="document_label"
                            class="form-control @error('document_label') is-invalid @enderror"
                            placeholder="Contoh: KTP, Sertifikat Welding" required>
                        @error('document_label')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File Dokumen</label>
                        <input type="file" name="document_file"
                            class="form-control @error('document_file') is-invalid @enderror" required>
                        @error('document_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: PDF/JPG/PNG/DOC/XLS (maks 5MB).</small>
                    </div>
                    <button class="btn btn-outline-primary w-100" type="submit">Upload Dokumen</button>
                </form>
            </div>
        </div>
    </div>

    <div class="card-style mb-30">
        <h6 class="mb-3">Daftar Dokumen</h6>
        <div class="table-wrapper table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Label Dokumen</th>
                        <th>Nama File</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employee->documents as $document)
                        <tr>
                            <td>{{ $document->document_label }}</td>
                            <td>{{ $document->file_name }}</td>
                            <td>
                                <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank"
                                    class="btn btn-sm btn-info">Lihat</a>
                                <form action="{{ route('profile.documents.destroy', $document->id) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Hapus dokumen ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Belum ada dokumen.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
