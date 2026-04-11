@extends('layouts.app')

@section('title', 'Detail Permohonan')

@section('content')

    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2> Detail Permohonan</h2>
                </div>
            </div>
            <!-- end col -->
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('permohonan.index') }}">Permohonan</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Detail Permohonan
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    </div>
    <!-- ========== title-wrapper end ========== -->

    <div class="card-style mb-30">
        <div class="row mb-3">
            <div class="col-md-3"><strong>Nomor</strong></div>
            <div class="col-md-9">{{ $permohonan->nomor }}</div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3"><strong>Status</strong></div>
            <div class="col-md-9">
                @if ($permohonan->status == 'OPEN')
                    <span class="status-btn warning-btn">OPEN</span>
                @else
                    <span class="status-btn success-btn">CLOSE</span>
                @endif
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3"><strong>Nama Perusahaan</strong></div>
            <div class="col-md-9">{{ $permohonan->nama_perusahaan }}</div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3"><strong>Alamat</strong></div>
            <div class="col-md-9">{{ $permohonan->alamat }}</div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3"><strong>Nama PIC</strong></div>
            <div class="col-md-9">{{ $permohonan->nama_pic }}</div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3"><strong>No Telp</strong></div>
            <div class="col-md-9">{{ $permohonan->no_telp }}</div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3"><strong>Email</strong></div>
            <div class="col-md-9">{{ $permohonan->email ?: '-' }}</div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3"><strong>Test Uji</strong></div>
            <div class="col-md-9">
                {{ $permohonan->testuji == 'quality_internal' ? 'Quality Internal' : 'Quality External' }}
                @if ($permohonan->testuji == 'quality_external' && $permohonan->testuji_external_keterangan)
                    <br>
                    <small>Keterangan: {{ $permohonan->testuji_external_keterangan }}</small>
                @endif
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3"><strong>Lokasi</strong></div>
            <div class="col-md-9">{{ $permohonan->lokasi }}</div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3"><strong>Nama Proyek</strong></div>
            <div class="col-md-9">{{ $permohonan->nama_proyek }}</div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3"><strong>Permintaan Khusus</strong></div>
            <div class="col-md-9">{{ $permohonan->permintaan_khusus ?: '-' }}</div>
        </div>

        <hr>

        <h5 class="mb-3">Dokumen Pendukung</h5>
        <div class="table-wrapper table-responsive mb-4">
            <table class="table">
                <thead>
                    <tr>
                        <th>
                            <h6>Jenis Dokumen</h6>
                        </th>
                        <th>
                            <h6>Nama File</h6>
                        </th>
                        <th>
                            <h6>Aksi</h6>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permohonan->dokumens as $dok)
                        <tr>
                            <td>{{ $dok->label }}</td>
                            <td>{{ $dok->file_name }}</td>
                            <td>
                                <a href="{{ route('permohonan.dokumen.preview', $dok->id) }}" target="_blank"
                                    class="btn btn-sm btn-info">
                                    Preview
                                </a>
                                <a href="{{ route('permohonan.dokumen.download', $dok->id) }}"
                                    class="btn btn-sm btn-secondary">
                                    Download
                                </a>
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

        <h5 class="mb-3">Item Permohonan</h5>
        <div class="table-wrapper table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>
                            <h6>No</h6>
                        </th>
                        <th>
                            <h6>Detail Pekerjaan</h6>
                        </th>
                        <th>
                            <h6>Jenis Layanan</h6>
                        </th>
                        <th>
                            <h6>Tanggal Permintaan</h6>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permohonan->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->detail_pekerjaan }}</td>
                            <td>{{ $item->layanans->pluck('nama')->implode(', ') ?: '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_permintaan)->format('d-m-Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada item.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="projectModal" tabindex="-1" aria-labelledby="projectModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="form-jadikan-project" action="{{ route('permohonan.jadikan-project', $permohonan->id) }}"
                        method="POST">
                        @csrf

                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="projectModalLabel">Jadikan Proyek</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div id="project-form-alert" class="alert d-none"></div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="project_no" class="form-label">No Project</label>
                                    <input type="text" class="form-control" id="project_no" name="project_no"
                                        value="{{ $generatedProjectNo }}" readonly>
                                    <small class="text-danger field-error" data-field="project_no"></small>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="pic_ids" class="form-label">PIC</label>
                                    <select name="pic_ids[]" id="pic_ids" class="form-control" multiple>
                                        @foreach ($pics as $pic)
                                            <option value="{{ $pic->id }}">
                                                {{ $pic->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger field-error" data-field="pic_ids"></small>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="description" class="form-label">Deskripsi</label>
                                    <textarea name="description" id="description" rows="4" class="form-control"
                                        placeholder="Masukkan deskripsi proyek..."></textarea>
                                    <small class="text-danger field-error" data-field="description"></small>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Batal
                            </button>

                            <button type="submit" class="btn btn-primary" id="btn-submit-project">
                                <span class="btn-text">Simpan & Jadikan Proyek</span>
                                <span class="btn-loading d-none">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"
                                        aria-hidden="true"></span>
                                    Memproses...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-12 text-end mt-4">
            @if ($permohonan->proyek)
                @can('proyek.show')
                    <a href="{{ route('proyek.show', $permohonan->proyek->id) }}" class="main-btn info-btn">
                        Lihat Proyek
                    </a>
                @endcan
            @else
                @can('permohonan.jadikan_project')
                    <button type="button" class="main-btn success-btn" data-bs-toggle="modal"
                        data-bs-target="#projectModal">
                        Jadikan proyek
                    </button>
                @endcan
            @endif

            @can('permohonan.edit')
                <a href="{{ route('permohonan.edit', $permohonan->id) }}" class="main-btn primary-btn btn-hover">
                    Edit
                </a>
            @endcan
            <a href="{{ route('permohonan.index') }}" class="main-btn light-btn btn-hover">
                Kembali
            </a>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#pic_ids').select2({
                dropdownParent: $('#projectModal'),
                width: '100%',
                placeholder: '-- Pilih PIC --'
            });

            const form = $('#form-jadikan-project');
            const submitBtn = $('#btn-submit-project');

            function clearErrors() {
                $('.field-error').text('');
                $('#project-form-alert')
                    .addClass('d-none')
                    .removeClass('alert-danger alert-success')
                    .html('');
            }

            function showErrors(errors) {
                clearErrors();

                $('#project-form-alert')
                    .removeClass('d-none')
                    .addClass('alert alert-danger')
                    .html('Ada data yang belum sesuai. Silakan cek form.');

                Object.keys(errors).forEach(function(field) {
                    const message = errors[field][0];
                    $(`.field-error[data-field="${field}"]`).text(message);
                });
            }

            function setLoading(isLoading) {
                if (isLoading) {
                    submitBtn.prop('disabled', true);
                    submitBtn.find('.btn-text').addClass('d-none');
                    submitBtn.find('.btn-loading').removeClass('d-none');
                } else {
                    submitBtn.prop('disabled', false);
                    submitBtn.find('.btn-text').removeClass('d-none');
                    submitBtn.find('.btn-loading').addClass('d-none');
                }
            }

            form.on('submit', function(e) {
                e.preventDefault();
                clearErrors();
                setLoading(true);

                const formData = new FormData(this);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        $('#project-form-alert')
                            .removeClass('d-none alert-danger')
                            .addClass('alert alert-success')
                            .html(response.message);

                        if (response.redirect) {
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 800);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {

                            // kalau ada validation error
                            if (xhr.responseJSON.errors) {
                                showErrors(xhr.responseJSON.errors);
                            }

                            // kalau hanya message biasa
                            if (xhr.responseJSON.message) {
                                $('#project-form-alert')
                                    .removeClass('d-none alert-success')
                                    .addClass('alert alert-danger')
                                    .html(xhr.responseJSON.message);
                            }

                        } else {
                            $('#project-form-alert')
                                .removeClass('d-none alert-success')
                                .addClass('alert alert-danger')
                                .html(xhr.responseJSON?.message ||
                                    'Terjadi kesalahan pada server.');
                        }
                    },
                    complete: function() {
                        setLoading(false);
                    }
                });
            });
        });
    </script>
@endpush
