@extends('layouts.app')

@section('title', 'Profil Saya')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
    <style>
        .avatar-edit-trigger {
            cursor: pointer;
            position: relative;
            display: inline-block;
        }

        .avatar-edit-trigger .overlay {
            position: absolute;
            inset: 0;
            border-radius: 999px;
            background: rgba(0, 0, 0, 0.35);
            color: #fff;
            opacity: 0;
            transition: .2s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
        }

        .avatar-edit-trigger:hover .overlay {
            opacity: 1;
        }

        .cropper-view-box,
        .cropper-face {
            border-radius: 50%;
        }
    </style>
@endpush

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
                    </div>


                    <hr class="my-4">
                    <h6 class="mb-3">Data Diri Tambahan</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Kontak Darurat (Nama)</label>
                            <input type="text" name="emergency_contact_name" class="form-control"
                                value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kontak Darurat (No Telp)</label>
                            <input type="text" name="emergency_contact_phone" class="form-control"
                                value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. BPJS Ketenagakerjaan</label>
                            <input type="text" name="bpjs_ketenagakerjaan_number" class="form-control"
                                value="{{ old('bpjs_ketenagakerjaan_number', $employee->bpjs_ketenagakerjaan_number) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. BPJS Kesehatan</label>
                            <input type="text" name="bpjs_kesehatan_number" class="form-control"
                                value="{{ old('bpjs_kesehatan_number', $employee->bpjs_kesehatan_number) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Informasi Penting Lainnya</label>
                            <textarea name="important_information" rows="3" class="form-control">{{ old('important_information', $employee->important_information) }}</textarea>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3">Data Pendidikan</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Ijazah Terakhir</label>
                            <input type="text" name="last_education" class="form-control" value="{{ old('last_education', $employee->last_education) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload Berkas Ijazah</label>
                            <input type="file" name="last_education_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            @if ($employee->last_education_file_path)
                                <small class="text-muted">File saat ini: <a href="{{ asset('storage/' . $employee->last_education_file_path) }}" target="_blank">{{ $employee->last_education_file_name }}</a></small>
                            @endif
                        </div>
                    </div>

                    <hr class="my-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Pengalaman Kerja</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-work-profile">+ Tambah</button>
                    </div>
                    <div id="work-profile-wrapper">
                        @foreach (old('work_experiences', $employee->workExperiences->map(fn($item) => ['company_name' => $item->company_name, 'position' => $item->position, 'start_year' => $item->start_year, 'end_year' => $item->end_year, 'certificate_file_name' => $item->certificate_file_name])->toArray()) as $i => $work)
                            <div class="border rounded p-3 mb-3 work-item-profile">
                                <div class="row g-3">
                                    <div class="col-md-4"><input type="text" name="work_experiences[{{ $i }}][company_name]" class="form-control" placeholder="Nama PT" value="{{ $work['company_name'] ?? '' }}"></div>
                                    <div class="col-md-4"><input type="text" name="work_experiences[{{ $i }}][position]" class="form-control" placeholder="Posisi" value="{{ $work['position'] ?? '' }}"></div>
                                    <div class="col-md-2"><input type="number" name="work_experiences[{{ $i }}][start_year]" class="form-control" placeholder="Mulai" value="{{ $work['start_year'] ?? '' }}"></div>
                                    <div class="col-md-2"><input type="number" name="work_experiences[{{ $i }}][end_year]" class="form-control" placeholder="Selesai" value="{{ $work['end_year'] ?? '' }}"></div>
                                    <div class="col-md-10"><input type="file" name="work_experience_files[{{ $i }}]" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></div>
                                    <div class="col-md-2 text-end"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-profile-row">Hapus</button></div>
                                </div>
                                @if (!empty($work['certificate_file_name']))<small class="text-muted">File saat ini: {{ $work['certificate_file_name'] }}</small>@endif
                            </div>
                        @endforeach
                    </div>

                    <hr class="my-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Sertifikat</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-certificate-profile">+ Tambah</button>
                    </div>
                    <p class="text-muted small mb-3">Sertifikat external yang expired dalam 3 bulan akan ditandai di detail.</p>
                    <div id="certificate-profile-wrapper">
                        @foreach (old('certificates', $employee->certificates->map(fn($item) => ['certificate_type' => $item->certificate_type, 'certificate_name' => $item->certificate_name, 'issuer' => $item->issuer, 'issued_at' => optional($item->issued_at)->format('Y-m-d'), 'expired_at' => optional($item->expired_at)->format('Y-m-d'), 'file_name' => $item->file_name])->toArray()) as $i => $certificate)
                            <div class="border rounded p-3 mb-3 certificate-item-profile bg-light">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge {{ ($certificate['certificate_type'] ?? 'internal') === 'external' ? 'bg-warning text-dark' : 'bg-secondary' }}">
                                        {{ strtoupper($certificate['certificate_type'] ?? 'internal') }}
                                    </span>
                                    <small class="text-muted">Sertifikat #{{ $i + 1 }}</small>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-3"><label class="form-label">Jenis</label><select name="certificates[{{ $i }}][certificate_type]" class="form-select"><option value="internal" {{ ($certificate['certificate_type'] ?? '') === 'internal' ? 'selected' : '' }}>Internal</option><option value="external" {{ ($certificate['certificate_type'] ?? '') === 'external' ? 'selected' : '' }}>External</option></select></div>
                                    <div class="col-md-5"><label class="form-label">Nama Sertifikat</label><input type="text" name="certificates[{{ $i }}][certificate_name]" class="form-control" placeholder="Contoh: Pelatihan APAR" value="{{ $certificate['certificate_name'] ?? '' }}"></div>
                                    <div class="col-md-4"><label class="form-label">Penerbit</label><input type="text" name="certificates[{{ $i }}][issuer]" class="form-control" placeholder="Contoh: MT PT / Internal Perusahaan" value="{{ $certificate['issuer'] ?? '' }}"></div>
                                    <div class="col-md-3"><label class="form-label">Tgl Terbit</label><input type="date" name="certificates[{{ $i }}][issued_at]" class="form-control" value="{{ $certificate['issued_at'] ?? '' }}"></div>
                                    <div class="col-md-3"><label class="form-label">Tgl Expired</label><input type="date" name="certificates[{{ $i }}][expired_at]" class="form-control" value="{{ $certificate['expired_at'] ?? '' }}"></div>
                                    <div class="col-md-4"><label class="form-label">Berkas</label><input type="file" name="certificate_files[{{ $i }}]" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></div>
                                    <div class="col-md-2 text-end d-flex align-items-end justify-content-end"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-profile-row">Hapus</button></div>
                                </div>
                                @if (!empty($certificate['file_name']))<small class="text-muted">File saat ini: {{ $certificate['file_name'] }}</small>@endif
                            </div>
                        @endforeach
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

                <label class="avatar-edit-trigger" for="profile-photo-input">
                    <img id="profile-preview"
                        src="{{ $employee->photo_path ? asset('storage/' . $employee->photo_path) : asset('template/assets/images/profile/profile-image.png') }}"
                        class="img-fluid rounded-circle border" style="width:140px; height:140px; object-fit:cover;" />
                    <span class="overlay">Ubah Foto</span>
                </label>

                <input type="file" id="profile-photo-input" accept="image/*" class="d-none">

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

    <div class="modal fade" id="photoCropModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Atur Foto Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2 text-muted small">Geser dan zoom foto agar pas seperti lingkaran profil.</div>
                    <div class="w-100" style="max-height: 60vh; overflow: hidden;">
                        <img id="cropper-image" src="" alt="cropper" style="max-width:100%; display:none;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btn-save-cropped-photo">Simpan Foto</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
    <script>
        $(function() {
            const input = document.getElementById('profile-photo-input');
            const workWrapper = $('#work-profile-wrapper');
            const certificateWrapper = $('#certificate-profile-wrapper');
            let workIndex = workWrapper.find('.work-item-profile').length;
            let certificateIndex = certificateWrapper.find('.certificate-item-profile').length;
            const cropperImage = document.getElementById('cropper-image');
            const preview = document.getElementById('profile-preview');
            const modalEl = document.getElementById('photoCropModal');
            const modal = new bootstrap.Modal(modalEl);
            const saveBtn = document.getElementById('btn-save-cropped-photo');


            $('#btn-add-work-profile').on('click', function() {
                workWrapper.append(`<div class="border rounded p-3 mb-3 work-item-profile"><div class="row g-3"><div class="col-md-4"><input type="text" name="work_experiences[${workIndex}][company_name]" class="form-control" placeholder="Nama PT"></div><div class="col-md-4"><input type="text" name="work_experiences[${workIndex}][position]" class="form-control" placeholder="Posisi"></div><div class="col-md-2"><input type="number" name="work_experiences[${workIndex}][start_year]" class="form-control" placeholder="Mulai"></div><div class="col-md-2"><input type="number" name="work_experiences[${workIndex}][end_year]" class="form-control" placeholder="Selesai"></div><div class="col-md-10"><input type="file" name="work_experience_files[${workIndex}]" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></div><div class="col-md-2 text-end"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-profile-row">Hapus</button></div></div></div>`);
                workIndex++;
            });

            $('#btn-add-certificate-profile').on('click', function() {
                certificateWrapper.append(`<div class="border rounded p-3 mb-3 certificate-item-profile bg-light"><div class="d-flex justify-content-between align-items-center mb-3"><span class="badge bg-secondary">INTERNAL</span><small class="text-muted">Sertifikat #${certificateIndex + 1}</small></div><div class="row g-3"><div class="col-md-3"><label class="form-label">Jenis</label><select name="certificates[${certificateIndex}][certificate_type]" class="form-select"><option value="internal">Internal</option><option value="external">External</option></select></div><div class="col-md-5"><label class="form-label">Nama Sertifikat</label><input type="text" name="certificates[${certificateIndex}][certificate_name]" class="form-control" placeholder="Contoh: Pelatihan APAR"></div><div class="col-md-4"><label class="form-label">Penerbit</label><input type="text" name="certificates[${certificateIndex}][issuer]" class="form-control" placeholder="Contoh: MT PT / Internal Perusahaan"></div><div class="col-md-3"><label class="form-label">Tgl Terbit</label><input type="date" name="certificates[${certificateIndex}][issued_at]" class="form-control"></div><div class="col-md-3"><label class="form-label">Tgl Expired</label><input type="date" name="certificates[${certificateIndex}][expired_at]" class="form-control"></div><div class="col-md-4"><label class="form-label">Berkas</label><input type="file" name="certificate_files[${certificateIndex}]" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></div><div class="col-md-2 text-end d-flex align-items-end justify-content-end"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-profile-row">Hapus</button></div></div></div>`);
                certificateIndex++;
            });

            $(document).on('click', '.btn-remove-profile-row', function() {
                $(this).closest('.work-item-profile, .certificate-item-profile').remove();
            });

            $(document).on('change', 'select[name^="certificates"][name$="[certificate_type]"]', function() {
                const wrapper = $(this).closest('.certificate-item-profile');
                const badge = wrapper.find('.badge').first();
                if ($(this).val() === 'external') {
                    badge.removeClass('bg-secondary').addClass('bg-warning text-dark').text('EXTERNAL');
                } else {
                    badge.removeClass('bg-warning text-dark').addClass('bg-secondary').text('INTERNAL');
                }
            });


            let cropper = null;

            input.addEventListener('change', (event) => {
                const file = event.target.files?.[0];
                if (!file) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    cropperImage.src = e.target.result;
                    cropperImage.style.display = 'block';
                    modal.show();
                };
                reader.readAsDataURL(file);
            });

            modalEl.addEventListener('shown.bs.modal', () => {
                if (cropper) {
                    cropper.destroy();
                }

                cropper = new Cropper(cropperImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 1,
                    background: false,
                    guides: false,
                    center: false,
                    highlight: false,
                    cropBoxMovable: false,
                    cropBoxResizable: false,
                    minContainerWidth: 300,
                    minContainerHeight: 300,
                });
            });

            modalEl.addEventListener('hidden.bs.modal', () => {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                input.value = '';
            });

            saveBtn.addEventListener('click', () => {
                if (!cropper) {
                    return;
                }

                cropper.getCroppedCanvas({
                    width: 600,
                    height: 600,
                    imageSmoothingQuality: 'high'
                }).toBlob((blob) => {
                    const formData = new FormData();
                    formData.append('photo', blob, 'profile.jpg');
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch('{{ route('profile.photo.update') }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json'
                            },
                            body: formData,
                        })
                        .then(async (res) => {
                            const payload = await res.json();
                            if (!res.ok || !payload.success) {
                                throw new Error(payload.message || 'Gagal upload foto');
                            }
                            return payload;
                        })
                        .then((payload) => {
                            preview.src = payload.photo_url;
                            modal.hide();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Foto profil berhasil diperbarui.',
                                timer: 1400,
                                showConfirmButton: false,
                            });
                        })
                        .catch((err) => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: err.message,
                            });
                        });
                }, 'image/jpeg', 0.92);
            });
        });
    </script>
@endpush
