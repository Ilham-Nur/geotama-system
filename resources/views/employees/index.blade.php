@extends('layouts.app')

@section('title', 'Karyawan')

@section('content')
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2>Halaman Karyawan</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Karyawan</li>
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
        <div class="col-12">
            <div class="card-style mb-30">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h6 class="mb-0">Daftar Karyawan</h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <form action="{{ route('employees.index') }}" method="GET" class="d-flex gap-2">
                            <input type="text" id="search-input" name="search" class="form-control"
                                placeholder="Cari nama / NIK / posisi" value="{{ $search }}">
                            <button type="submit" class="btn btn-outline-primary">Cari</button>
                        </form>
                        @can('employees.create')
                            <a href="{{ route('employees.create') }}" class="btn btn-primary">
                                <i class="lni lni-plus"></i> Tambah Karyawan
                            </a>
                        @endcan
                    </div>
                </div>

                @include('employees.partials.table', ['employees' => $employees])
            </div>
        </div>
    </div>

    <div class="modal fade" id="employeeDetailModal" tabindex="-1" aria-labelledby="employeeDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="employeeDetailModalLabel">Detail Karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="employee-detail-photo-wrapper" class="mb-3"></div>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered mb-0">
                            <tbody id="employee-detail-table-body"></tbody>
                        </table>
                    </div>
                    <h6 class="mb-2">Dokumen</h6>
                    <div id="employee-detail-documents"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            const detailModalElement = document.getElementById('employeeDetailModal');
            const detailModal = new bootstrap.Modal(detailModalElement);

            function fallbackValue(value) {
                if (value === null || value === undefined || value === '') {
                    return '-';
                }

                return value;
            }

            function renderField(label, value) {
                return `
                    <tr>
                        <th class="text-start pe-3" style="width: 220px;">${label}</th>
                        <td class="text-start">${fallbackValue(value)}</td>
                    </tr>
                `;
            }

            $('.btn-employee-detail').on('click', function() {
                const employee = $(this).data('employee') || {};
                const documents = $(this).data('documents') || [];
                const tableBody = $('#employee-detail-table-body');
                const documentsContainer = $('#employee-detail-documents');
                const photoWrapper = $('#employee-detail-photo-wrapper');
                const modalTitle = $('#employeeDetailModalLabel');

                const fields = [{
                        label: 'Kode Karyawan / NIK',
                        value: employee.employee_code
                    },
                    {
                        label: 'Nama Lengkap',
                        value: employee.full_name
                    },
                    {
                        label: 'Posisi',
                        value: employee.position
                    },
                    {
                        label: 'No Telepon',
                        value: employee.phone
                    },
                    {
                        label: 'Tanggal Bergabung',
                        value: employee.hire_date
                    },
                    {
                        label: 'Status Kepegawaian',
                        value: employee.employment_status
                    },
                    {
                        label: 'Jenis Kelamin',
                        value: employee.gender
                    },
                    {
                        label: 'Tempat Lahir',
                        value: employee.birth_place
                    },
                    {
                        label: 'Tanggal Lahir',
                        value: employee.birth_date
                    },
                    {
                        label: 'No. KTP / Identitas',
                        value: employee.identity_number
                    },
                    {
                        label: 'Status Pernikahan',
                        value: employee.marital_status
                    },
                    {
                        label: 'Kewarganegaraan',
                        value: employee.nationality
                    },
                    {
                        label: 'Agama',
                        value: employee.religion
                    },
                    {
                        label: 'Alamat Lengkap',
                        value: employee.full_address
                    },
                ];

                const documentsHtml = documents.length ?
                    `<ul class="list-group text-start">${documents.map((document) => `
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <div class="fw-semibold">${fallbackValue(document.document_label)}</div>
                                <small class="text-muted">${fallbackValue(document.file_name)}</small>
                            </div>
                            <a href="${document.file_url}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat</a>
                        </li>`).join('')}
                    </ul>` :
                    '<p class="text-muted mb-0 text-start">Tidak ada dokumen.</p>';

                const photoHtml = employee.photo_url ?
                    `<img src="${employee.photo_url}" alt="Foto Karyawan" class="img-thumbnail mb-3" style="max-height: 180px;">` :
                    '<p class="text-muted mb-3 text-start">Foto belum tersedia.</p>';

                modalTitle.text(`Detail Karyawan: ${fallbackValue(employee.full_name)}`);
                tableBody.html(fields.map((field) => renderField(field.label, field.value)).join(''));
                documentsContainer.html(documentsHtml);
                photoWrapper.html(photoHtml);
                detailModal.show();
            });

            $('.btn-delete-employee').on('click', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');

                Swal.fire({
                    title: 'Hapus karyawan ini?',
                    text: 'Data yang dihapus tidak bisa dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.trigger('submit');
                    }
                });
            });
        });
    </script>
@endpush
