@extends('layouts.app')

@section('title', 'Detail Pekerjaan | ' . ($layanan->nama ?? '-'))

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1">Detail Pekerjaan Proyek</h3>
                <p class="text-muted mb-0">Form laporan pekerjaan per item</p>
            </div>
            <a href="{{ route('proyek.show', $proyek->id) }}" class="btn btn-secondary">
                Kembali
            </a>
        </div>

        <div class="row">
            {{-- ===================== SIDEBAR KIRI ===================== --}}
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Pekerjaan</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>No Proyek</strong><br>
                            <span>{{ $proyek->no_proyek ?? '-' }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>Nama Proyek</strong><br>
                            <span>{{ $proyek->permohonan->nama_proyek ?? '-' }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>Client</strong><br>
                            <span>{{ $proyek->permohonan->nama_perusahaan ?? '-' }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>Detail Pekerjaan</strong><br>
                            <span>{{ $item->detail_pekerjaan ?? '-' }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>Layanan</strong><br>
                            <span class="badge bg-info">{{ $layanan->nama ?? '-' }}</span>
                        </div>

                        {{-- Status laporan (tampil jika sudah ada) --}}
                        @if ($laporan)
                            <div class="mb-0">
                                <strong>Status Laporan</strong><br>
                                @if ($laporan->isDraft())
                                    <span class="badge bg-warning text-dark">Draft</span>
                                @else
                                    <span class="badge bg-success">Submitted</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">PIC Proyek</h5>
                    </div>
                    <div class="card-body">
                        @forelse ($proyek->users as $user)
                            <div class="border rounded p-2 mb-2">
                                <strong>{{ $user->name }}</strong><br>
                                <small class="text-muted">{{ $user->email }}</small>
                            </div>
                        @empty
                            <p class="mb-0 text-muted">Belum ada PIC.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ===================== KONTEN KANAN ===================== --}}
            <div class="col-md-8">

                @if ($isNdtLayanan)
                    @include('proyek.partials.ndt-report-form')
                @else
                @if ($laporan)
                    {{-- ============================================
                         MODE: LAPORAN SUDAH ADA
                         Tampil data + tabel file + tombol edit/submit
                    ============================================ --}}

                    {{-- Info tanggal --}}
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Laporan</h5>
                            <button class="btn btn-sm btn-outline-primary" id="btnToggleEdit" onclick="toggleEditMode()">
                                <i class="bi bi-pencil"></i> Edit Laporan
                            </button>
                        </div>
                        <div class="card-body">

                            {{-- VIEW MODE --}}
                            <div id="viewMode">
                                <div class="mb-3">
                                    <strong>Tanggal Pelaksanaan</strong><br>
                                    <span>{{ $laporan->tanggal_pelaksanaan->format('d M Y') }}</span>
                                </div>
                            </div>

                            {{-- EDIT MODE (tersembunyi by default) --}}
                            <div id="editMode" class="d-none">
                                <form id="formUpdate" enctype="multipart/form-data">
                                    @csrf
                                    @method('PATCH')

                                    <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">

                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">
                                            Tanggal Pelaksanaan <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" class="form-control" name="tanggal_pelaksanaan"
                                            value="{{ $laporan->tanggal_pelaksanaan->format('Y-m-d') }}"
                                            style="max-width:300px;" required>
                                    </div>

                                    <hr>

                                    {{-- ===== FILE REPORT EXISTING ===== --}}
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">File Report Saat Ini</label>
                                        @if ($laporan->fileReport->count())
                                            <table class="table table-bordered table-sm align-middle mb-3">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Nama File</th>
                                                        <th>Tipe</th>
                                                        <th>Ukuran</th>
                                                        <th class="text-center">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($laporan->fileReport as $fr)
                                                        <tr id="row-report-{{ $fr->id }}">
                                                            <td>
                                                                <a href="{{ Storage::url($fr->path) }}" target="_blank"
                                                                    class="text-decoration-none">
                                                                    {{ $fr->nama_file }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                @if ($fr->isPdf())
                                                                    <span class="badge bg-danger">PDF</span>
                                                                @else
                                                                    <span class="badge bg-info">Foto</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $fr->size_readable }}</td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                                    onclick="tandaiHapus('report', {{ $fr->id }}, this)">
                                                                    Hapus
                                                                </button>
                                                                <input type="checkbox" name="hapus_file_report[]"
                                                                    value="{{ $fr->id }}"
                                                                    id="hapus-report-{{ $fr->id }}" class="d-none">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <p class="text-muted small mb-3">Belum ada file report.</p>
                                        @endif

                                        {{-- Tambah file report baru --}}
                                        <label class="form-label fw-semibold">Tambah File Report Baru</label>
                                        <p class="text-muted small mb-2">JPG, PNG, JPEG, PDF. Maks 10MB per file.</p>
                                        <div id="dropZoneReport"
                                            class="upload-drop-zone border border-2 border-dashed rounded p-3 text-center mb-2"
                                            onclick="document.getElementById('fileInputReport').click()"
                                            ondragover="handleDragOver(event,'dropZoneReport')"
                                            ondragleave="handleDragLeave(event,'dropZoneReport')"
                                            ondrop="handleDrop(event,'report')">
                                            <p class="mb-0 text-muted small">Klik atau seret file ke sini</p>
                                        </div>
                                        <input type="file" id="fileInputReport" name="file_report[]" multiple
                                            accept=".jpg,.jpeg,.png,.pdf" class="d-none"
                                            onchange="handleFileSelect(this.files,'report')">
                                        <div id="previewReport" class="row g-2 mt-1"></div>
                                        <div id="errorReport" class="text-danger small mt-1 d-none"></div>
                                    </div>

                                    <hr>

                                    {{-- ===== FOTO LAMPIRAN EXISTING ===== --}}
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Foto Lampiran Saat Ini</label>
                                        @if ($laporan->fotoLampiran->count())
                                            <table class="table table-bordered table-sm align-middle mb-3">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Nama File</th>
                                                        <th>Tipe</th>
                                                        <th>Ukuran</th>
                                                        <th class="text-center">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($laporan->fotoLampiran as $fl)
                                                        <tr id="row-lampiran-{{ $fl->id }}">
                                                            <td>
                                                                <a href="{{ Storage::url($fl->path) }}" target="_blank"
                                                                    class="text-decoration-none">
                                                                    {{ $fl->nama_file }}
                                                                </a>
                                                            </td>
                                                            <td><span class="badge bg-info">Foto</span></td>
                                                            <td>{{ $fl->size_readable }}</td>
                                                            <td class="text-center">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    onclick="tandaiHapus('lampiran', {{ $fl->id }}, this)">
                                                                    Hapus
                                                                </button>
                                                                <input type="checkbox" name="hapus_foto_lampiran[]"
                                                                    value="{{ $fl->id }}"
                                                                    id="hapus-lampiran-{{ $fl->id }}"
                                                                    class="d-none">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <p class="text-muted small mb-3">Belum ada foto lampiran.</p>
                                        @endif

                                        {{-- Tambah foto lampiran baru --}}
                                        <label class="form-label fw-semibold">Tambah Foto Lampiran Baru</label>
                                        <p class="text-muted small mb-2">JPG, PNG, JPEG. Maks 5MB per foto.</p>
                                        <div id="dropZoneLampiran"
                                            class="upload-drop-zone border border-2 border-dashed rounded p-3 text-center mb-2"
                                            onclick="document.getElementById('fileInputLampiran').click()"
                                            ondragover="handleDragOver(event,'dropZoneLampiran')"
                                            ondragleave="handleDragLeave(event,'dropZoneLampiran')"
                                            ondrop="handleDrop(event,'lampiran')">
                                            <p class="mb-0 text-muted small">Klik atau seret foto ke sini</p>
                                        </div>
                                        <input type="file" id="fileInputLampiran" name="foto_lampiran[]" multiple
                                            accept=".jpg,.jpeg,.png" class="d-none"
                                            onchange="handleFileSelect(this.files,'lampiran')">
                                        <div id="previewLampiran" class="row g-2 mt-1"></div>
                                        <div id="errorLampiran" class="text-danger small mt-1 d-none"></div>
                                    </div>

                                    <div class="mt-4 d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-secondary"
                                            onclick="toggleEditMode()">
                                            Batal
                                        </button>
                                        <div>
                                            <button type="button" class="btn btn-secondary me-2" id="btnUpdateDraft"
                                                onclick="submitUpdate('draft')">
                                                Simpan Draft
                                            </button>
                                            <button type="button" class="btn btn-primary" id="btnUpdateSubmit"
                                                onclick="submitUpdate('submit')">
                                                Simpan & Submit
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Tabel File Report (view mode) --}}
                    <div class="card mb-4" id="cardViewReport">
                        <div class="card-header">
                            <h5 class="mb-0">File Report</h5>
                        </div>
                        <div class="card-body p-0">
                            @if ($laporan->fileReport->count())
                                <table class="table table-bordered table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Nama File</th>
                                            <th style="width:100px">Tipe</th>
                                            <th style="width:100px">Ukuran</th>
                                            <th style="width:120px" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($laporan->fileReport as $fr)
                                            <tr>
                                                <td class="ps-3">{{ $fr->nama_file }}</td>
                                                <td>
                                                    @if ($fr->isPdf())
                                                        <span class="badge bg-danger">PDF</span>
                                                    @else
                                                        <span class="badge bg-info">Foto</span>
                                                    @endif
                                                </td>
                                                <td>{{ $fr->size_readable }}</td>
                                                <td class="text-center">
                                                    <a href="{{ Storage::url($fr->path) }}" target="_blank"
                                                        class="btn btn-sm btn-outline-primary">
                                                        Lihat
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted m-3">Belum ada file report.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Tabel Foto Lampiran (view mode) --}}
                    <div class="card mb-4" id="cardViewLampiran">
                        <div class="card-header">
                            <h5 class="mb-0">Foto Lampiran</h5>
                        </div>
                        <div class="card-body p-0">
                            @if ($laporan->fotoLampiran->count())
                                <table class="table table-bordered table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Nama File</th>
                                            <th style="width:100px">Tipe</th>
                                            <th style="width:100px">Ukuran</th>
                                            <th style="width:120px" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($laporan->fotoLampiran as $fl)
                                            <tr>
                                                <td class="ps-3">{{ $fl->nama_file }}</td>
                                                <td><span class="badge bg-info">Foto</span></td>
                                                <td>{{ $fl->size_readable }}</td>
                                                <td class="text-center">
                                                    <a href="{{ Storage::url($fl->path) }}" target="_blank"
                                                        class="btn btn-sm btn-outline-primary">
                                                        Lihat
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted m-3">Belum ada foto lampiran.</p>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- ============================================
                         MODE: BELUM ADA LAPORAN — Form input baru
                    ============================================ --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Laporan</h5>
                        </div>
                        <div class="card-body">
                            <form id="formLaporan" enctype="multipart/form-data">
                                @csrf

                                <input type="hidden" name="proyek_id" value="{{ $proyek->id }}">
                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                <input type="hidden" name="layanan_id" value="{{ $layanan->id }}">

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">
                                        Tanggal Pelaksanaan <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" name="tanggal_pelaksanaan"
                                        style="max-width:300px;" required>
                                </div>

                                <hr>

                                {{-- File Report --}}
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">File Report</label>
                                    <p class="text-muted small mb-2">JPG, PNG, JPEG, PDF. Bisa lebih dari satu file.</p>
                                    <div id="dropZoneReport"
                                        class="upload-drop-zone border border-2 border-dashed rounded p-4 text-center mb-3"
                                        onclick="document.getElementById('fileInputReport').click()"
                                        ondragover="handleDragOver(event,'dropZoneReport')"
                                        ondragleave="handleDragLeave(event,'dropZoneReport')"
                                        ondrop="handleDrop(event,'report')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                                            fill="#6c757d" viewBox="0 0 16 16" class="mb-2">
                                            <path
                                                d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z" />
                                        </svg>
                                        <p class="mb-0 text-muted">Klik atau seret file ke sini</p>
                                        <small class="text-muted">JPG, PNG, JPEG, PDF</small>
                                    </div>
                                    <input type="file" id="fileInputReport" name="file_report[]" multiple
                                        accept=".jpg,.jpeg,.png,.pdf" class="d-none"
                                        onchange="handleFileSelect(this.files,'report')">
                                    <div id="previewReport" class="row g-2 mt-1"></div>
                                    <div id="errorReport" class="text-danger small mt-2 d-none"></div>
                                </div>

                                <hr>

                                {{-- Foto Lampiran --}}
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Foto Lampiran</label>
                                    <p class="text-muted small mb-2">JPG, PNG, JPEG. Hanya foto.</p>
                                    <div id="dropZoneLampiran"
                                        class="upload-drop-zone border border-2 border-dashed rounded p-4 text-center mb-3"
                                        onclick="document.getElementById('fileInputLampiran').click()"
                                        ondragover="handleDragOver(event,'dropZoneLampiran')"
                                        ondragleave="handleDragLeave(event,'dropZoneLampiran')"
                                        ondrop="handleDrop(event,'lampiran')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                                            fill="#6c757d" viewBox="0 0 16 16" class="mb-2">
                                            <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                                            <path
                                                d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1z" />
                                        </svg>
                                        <p class="mb-0 text-muted">Klik atau seret foto ke sini</p>
                                        <small class="text-muted">JPG, PNG, JPEG</small>
                                    </div>
                                    <input type="file" id="fileInputLampiran" name="foto_lampiran[]" multiple
                                        accept=".jpg,.jpeg,.png" class="d-none"
                                        onchange="handleFileSelect(this.files,'lampiran')">
                                    <div id="previewLampiran" class="row g-2 mt-1"></div>
                                    <div id="errorLampiran" class="text-danger small mt-2 d-none"></div>
                                </div>

                                <div class="mt-4 text-end">
                                    <button type="button" id="btnDraft" class="btn btn-secondary me-2"
                                        onclick="submitForm('draft')">Simpan Draft</button>
                                    <button type="button" id="btnSubmit" class="btn btn-primary"
                                        onclick="submitForm('submit')">Simpan Laporan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
                @endif

            </div>{{-- end col-md-8 --}}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .upload-drop-zone {
            border-color: #adb5bd !important;
            cursor: pointer;
            transition: background .2s, border-color .2s;
        }

        .upload-drop-zone.drag-over {
            background-color: #e9f5ff !important;
            border-color: #0d6efd !important;
        }

        .preview-item {
            position: relative;
        }

        .preview-item .remove-btn {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: rgba(220, 53, 69, .85);
            color: #fff;
            border: none;
            font-size: 12px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
            z-index: 1;
        }

        .preview-img {
            width: 100%;
            height: 90px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }

        .preview-pdf {
            width: 100%;
            height: 90px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }

        .preview-filename {
            font-size: 11px;
            color: #6c757d;
            margin-top: 4px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 100%;
        }

        tr.akan-dihapus td {
            opacity: .45;
            text-decoration: line-through;
        }

        tr.akan-dihapus .btn-outline-danger {
            display: none;
        }

        tr.akan-dihapus::after {
            content: '';
        }
    </style>

    <script>
        // =============================================
        //  KONSTANTA ROUTE
        // =============================================
        const SUBMIT_URL =
            "{{ route('proyek.pekerjaan.tambah-report', [
                'proyek' => $proyek->id,
                'item' => $item->id,
                'layanan' => $layanan->id,
            ]) }}";

        @if ($laporan)
            const UPDATE_URL =
                "{{ route('proyek.pekerjaan.update-report', [
                    'proyek' => $proyek->id,
                    'item' => $item->id,
                    'layanan' => $layanan->id,
                ]) }}";
        @endif

        // =============================================
        //  TOGGLE EDIT MODE
        // =============================================
        function toggleEditMode() {
            const viewMode = document.getElementById('viewMode');
            const editMode = document.getElementById('editMode');
            const cardViewReport = document.getElementById('cardViewReport');
            const cardViewLampiran = document.getElementById('cardViewLampiran');
            const btn = document.getElementById('btnToggleEdit');

            const isEditing = !editMode.classList.contains('d-none');

            if (isEditing) {
                // Kembali ke view mode
                editMode.classList.add('d-none');
                viewMode.classList.remove('d-none');
                cardViewReport.classList.remove('d-none');
                cardViewLampiran.classList.remove('d-none');
                btn.textContent = '✏️ Edit Laporan';
                // Reset file baru yang sudah dipilih
                state.report = [];
                state.lampiran = [];
                renderPreviews('report');
                renderPreviews('lampiran');
            } else {
                // Masuk edit mode
                editMode.classList.remove('d-none');
                viewMode.classList.add('d-none');
                cardViewReport.classList.add('d-none');
                cardViewLampiran.classList.add('d-none');
                btn.textContent = '✕ Batal Edit';
            }
        }

        // =============================================
        //  TANDAI HAPUS FILE EXISTING
        // =============================================
        function tandaiHapus(type, id, btn) {
            const row = document.getElementById(`row-${type}-${id}`);
            const checkbox = document.getElementById(`hapus-${type}-${id}`);
            row.classList.add('akan-dihapus');
            checkbox.checked = true;
        }

        // =============================================
        //  SUBMIT FORM BARU (belum ada laporan)
        // =============================================
        function submitForm(action) {
            const tanggal = document.querySelector('#formLaporan [name="tanggal_pelaksanaan"]').value;
            if (!tanggal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Tanggal pelaksanaan wajib diisi.',
                    confirmButtonColor: '#0d6efd'
                });
                return;
            }

            const isSubmit = action === 'submit';
            Swal.fire({
                icon: 'question',
                title: isSubmit ? 'Simpan Laporan?' : 'Simpan sebagai Draft?',
                text: isSubmit ? 'Laporan akan disimpan dan dikirimkan. Lanjutkan?' :
                    'Data akan disimpan sebagai draft. Lanjutkan?',
                showCancelButton: true,
                confirmButtonText: isSubmit ? 'Ya, Simpan' : 'Ya, Draft',
                cancelButtonText: 'Batal',
                confirmButtonColor: isSubmit ? '#0d6efd' : '#6c757d',
                cancelButtonColor: '#dc3545',
            }).then(result => {
                if (!result.isConfirmed) return;

                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => Swal.showLoading()
                });
                setBtnLoading(true, 'btnDraft', 'btnSubmit');

                const formData = new FormData();
                formData.append('_token', document.querySelector('#formLaporan [name="_token"]').value);
                formData.append('proyek_id', '{{ $proyek->id }}');
                formData.append('item_id', '{{ $item->id }}');
                formData.append('layanan_id', '{{ $layanan->id }}');
                formData.append('tanggal_pelaksanaan', tanggal);
                formData.append('action', action);
                state.report.forEach(f => formData.append('file_report[]', f));
                state.lampiran.forEach(f => formData.append('foto_lampiran[]', f));

                kirimAjax(SUBMIT_URL, 'POST', formData, () => setBtnLoading(false, 'btnDraft', 'btnSubmit'));
            });
        }

        // =============================================
        //  SUBMIT UPDATE (sudah ada laporan)
        // =============================================
        function submitUpdate(action) {
            const tanggal = document.querySelector('#formUpdate [name="tanggal_pelaksanaan"]').value;
            if (!tanggal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Tanggal pelaksanaan wajib diisi.',
                    confirmButtonColor: '#0d6efd'
                });
                return;
            }

            const isSubmit = action === 'submit';
            Swal.fire({
                icon: 'question',
                title: isSubmit ? 'Perbarui & Submit?' : 'Perbarui Draft?',
                text: 'Perubahan pada laporan akan disimpan. Lanjutkan?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal',
                confirmButtonColor: isSubmit ? '#0d6efd' : '#6c757d',
                cancelButtonColor: '#dc3545',
            }).then(result => {
                if (!result.isConfirmed) return;

                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => Swal.showLoading()
                });
                setBtnLoading(true, 'btnUpdateDraft', 'btnUpdateSubmit');

                const formData = new FormData(document.getElementById('formUpdate'));
                formData.append('action', action);
                // file baru
                state.report.forEach(f => formData.append('file_report[]', f));
                state.lampiran.forEach(f => formData.append('foto_lampiran[]', f));
                // Laravel PATCH via POST + _method
                formData.append('_method', 'PATCH');

                kirimAjax(UPDATE_URL, 'POST', formData, () => setBtnLoading(false, 'btnUpdateDraft',
                    'btnUpdateSubmit'));
            });
        }

        // =============================================
        //  KIRIM AJAX (shared)
        // =============================================
        function kirimAjax(url, method, formData, onDone) {
            fetch(url, {
                    method,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(res => res.json().then(data => ({
                    status: res.status,
                    body: data
                })))
                .then(({
                    status,
                    body
                }) => {
                    onDone();
                    if (status === 200 || status === 201) {
                        Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: body.message ?? 'Laporan berhasil disimpan.',
                                confirmButtonColor: '#0d6efd'
                            })
                            .then(() => {
                                if (body.redirect) window.location.href = body.redirect;
                            });
                    } else if (status === 422) {
                        const messages = Object.values(body.errors ?? {}).flat().join('<br>');
                        Swal.fire({
                            icon: 'warning',
                            title: 'Data tidak valid',
                            html: messages || 'Periksa kembali isian form.',
                            confirmButtonColor: '#0d6efd'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: body.message ?? 'Terjadi kesalahan.',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                })
                .catch(() => {
                    onDone();
                    Swal.fire({
                        icon: 'error',
                        title: 'Koneksi Bermasalah',
                        text: 'Tidak dapat terhubung ke server.',
                        confirmButtonColor: '#dc3545'
                    });
                });
        }

        // =============================================
        //  BUTTON LOADING STATE
        // =============================================
        function setBtnLoading(isLoading, ...btnIds) {
            btnIds.forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                el.disabled = isLoading;
                if (isLoading) {
                    el.dataset.original = el.innerHTML;
                    el.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';
                } else {
                    el.innerHTML = el.dataset.original ?? el.innerHTML;
                }
            });
        }

        // =============================================
        //  FILE UPLOAD
        // =============================================
        const state = {
            report: [],
            lampiran: []
        };

        const uploadConfig = {
            report: {
                allowedTypes: ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'],
                inputId: 'fileInputReport',
                previewId: 'previewReport',
                errorId: 'errorReport',
                errorMsg: 'Hanya JPG, PNG, JPEG, PDF yang diizinkan.'
            },
            lampiran: {
                allowedTypes: ['image/jpeg', 'image/png', 'image/jpg'],
                inputId: 'fileInputLampiran',
                previewId: 'previewLampiran',
                errorId: 'errorLampiran',
                errorMsg: 'Hanya JPG, PNG, JPEG yang diizinkan.'
            },
        };

        function handleFileSelect(files, type) {
            const cfg = uploadConfig[type];
            const errorEl = document.getElementById(cfg.errorId);
            if (!errorEl) return;
            let invalid = 0;
            Array.from(files).forEach(file => {
                if (!cfg.allowedTypes.includes(file.type)) {
                    invalid++;
                    return;
                }
                if (!state[type].some(f => f.name === file.name && f.size === file.size)) state[type].push(file);
            });
            errorEl.textContent = invalid ? `${invalid} file ditolak. ${cfg.errorMsg}` : '';
            errorEl.classList.toggle('d-none', !invalid);
            renderPreviews(type);
        }

        function renderPreviews(type) {
            const cfg = uploadConfig[type];
            const container = document.getElementById(cfg.previewId);
            if (!container) return;
            container.innerHTML = '';
            state[type].forEach((file, index) => {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-3 preview-item';
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        col.innerHTML =
                            `<button type="button" class="remove-btn" onclick="removeFile('${type}',${index})">&#x2715;</button><img src="${e.target.result}" class="preview-img"><div class="preview-filename">${file.name}</div>`;
                    };
                    reader.readAsDataURL(file);
                } else {
                    col.innerHTML =
                        `<button type="button" class="remove-btn" onclick="removeFile('${type}',${index})">&#x2715;</button><div class="preview-pdf"><small class="text-danger fw-semibold">PDF</small></div><div class="preview-filename">${file.name}</div>`;
                }
                container.appendChild(col);
            });
        }

        function removeFile(type, index) {
            state[type].splice(index, 1);
            renderPreviews(type);
        }

        function handleDragOver(event, zoneId) {
            event.preventDefault();
            document.getElementById(zoneId)?.classList.add('drag-over');
        }

        function handleDragLeave(event, zoneId) {
            document.getElementById(zoneId)?.classList.remove('drag-over');
        }

        function handleDrop(event, type) {
            event.preventDefault();
            const zoneId = type === 'report' ? 'dropZoneReport' : 'dropZoneLampiran';
            document.getElementById(zoneId)?.classList.remove('drag-over');
            if (event.dataTransfer.files.length) handleFileSelect(event.dataTransfer.files, type);
        }
    </script>
@endsection
