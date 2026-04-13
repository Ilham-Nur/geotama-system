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
                    <h6 class="mb-2">Arsip Kontrak (Admin)</h6>
                    <div id="employee-detail-contracts" class="mb-4"></div>
                    <h6 class="mb-2">Dokumen Lain</h6>
                    <div id="employee-detail-documents"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="generateContractModal" tabindex="-1" aria-labelledby="generateContractModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="generate-contract-form">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="generateContractModalLabel">Generate Kontrak</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">Karyawan: <strong id="contract-employee-name">-</strong></p>
                        <p class="mb-3">Status: <span class="badge bg-secondary text-uppercase" id="contract-employee-status">-</span>
                        </p>

                        <div class="mb-3">
                            <label class="form-label">Nomor Kontrak (opsional)</label>
                            <input type="text" name="contract_number" class="form-control"
                                placeholder="Kosongkan untuk nomor otomatis">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Penandatanganan</label>
                            <input type="date" name="signing_date" class="form-control" required>
                        </div>

                        <div class="row g-3" id="period-fields">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="contract_start_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Berakhir</label>
                                <input type="date" name="contract_end_date" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3 d-none" id="effective-date-field">
                            <label class="form-label">Tanggal Efektif Karyawan Tetap</label>
                            <input type="date" name="effective_date" class="form-control">
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Gaji Pokok per Bulan (opsional)</label>
                            <input type="number" min="0" step="1000" name="salary" class="form-control"
                                placeholder="Contoh: 5000000">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Generate PDF</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadHardcopyModal" tabindex="-1" aria-labelledby="uploadHardcopyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="upload-hardcopy-form" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadHardcopyModalLabel">Upload Hardcopy Kontrak</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">Kontrak: <strong id="hardcopy-contract-title">-</strong></p>
                        <div class="mb-0">
                            <label class="form-label">File Hardcopy (PDF/JPG/PNG, maks 5MB)</label>
                            <input type="file" name="hardcopy_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            const detailModalElement = document.getElementById('employeeDetailModal');
            const detailModal = new bootstrap.Modal(detailModalElement);
            const generateContractModal = new bootstrap.Modal(document.getElementById('generateContractModal'));
            const uploadHardcopyModal = new bootstrap.Modal(document.getElementById('uploadHardcopyModal'));
            const employeeShowUrlTemplate = @json(route('employees.show', ['employee' => '__ID__']));
            const contractGenerateUrlTemplate = @json(route('employees.contracts.generate', ['employee' => '__ID__']));
            const contractHardcopyUploadTemplate = @json(route('employees.contracts.hardcopy.upload', ['employee' => '__EMP__', 'contract' => '__CTR__']));

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
                const employeeId = $(this).data('id');
                const tableBody = $('#employee-detail-table-body');
                const documentsContainer = $('#employee-detail-documents');
                const contractsContainer = $('#employee-detail-contracts');
                const photoWrapper = $('#employee-detail-photo-wrapper');
                const modalTitle = $('#employeeDetailModalLabel');
                const requestUrl = employeeShowUrlTemplate.replace('__ID__', employeeId);

                modalTitle.text('Detail Karyawan');
                tableBody.html('<tr><td class="text-center text-muted py-3" colspan="2">Memuat data...</td></tr>');
                contractsContainer.html('<p class="text-muted mb-0">Memuat kontrak...</p>');
                documentsContainer.html('<p class="text-muted mb-0">Memuat dokumen...</p>');
                photoWrapper.html('');
                detailModal.show();

                $.get(requestUrl)
                    .done(function(employee) {
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

                        const normalizedDocuments = Array.isArray(employee.documents) ? employee.documents : [];
                        const normalizedContracts = Array.isArray(employee.contracts) ? employee.contracts : [];
                        const documentsHtml = normalizedDocuments.length ?
                            `<ul class="list-group text-start">${normalizedDocuments.map((document) => `
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <div class="fw-semibold">${fallbackValue(document.document_label)}</div>
                                        <small class="text-muted">${fallbackValue(document.file_name)}</small>
                                    </div>
                                    <a href="${document.file_url}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat</a>
                                </li>`).join('')}
                            </ul>` :
                            '<p class="text-muted mb-0 text-start">Tidak ada dokumen.</p>';

                        const contractsHtml = normalizedContracts.length ?
                            `<ul class="list-group text-start">${normalizedContracts.map((contract) => `
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <div class="fw-semibold">${fallbackValue(contract.contract_number)}</div>
                                        <small class="text-muted d-block">${fallbackValue(contract.contract_type)} • ${fallbackValue(contract.signing_date)}</small>
                                        <small class="text-muted d-block">File Generate: ${fallbackValue(contract.generated_file_name)}</small>
                                        <small class="text-muted d-block">Hardcopy: ${fallbackValue(contract.hardcopy_file_name)}</small>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="${contract.generated_file_url}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat PDF</a>
                                        ${contract.hardcopy_file_url ? `<a href="${contract.hardcopy_file_url}" target="_blank" class="btn btn-sm btn-outline-success">Lihat Hardcopy</a>` : ''}
                                        <button type="button" class="btn btn-sm btn-outline-dark btn-upload-hardcopy"
                                            data-employee-id="${employee.id}"
                                            data-contract-id="${contract.id}"
                                            data-contract-number="${fallbackValue(contract.contract_number)}">
                                            Upload Hardcopy
                                        </button>
                                    </div>
                                </li>`).join('')}
                            </ul>` :
                            '<p class="text-muted mb-0 text-start">Belum ada kontrak.</p>';

                        const photoHtml = employee.photo_url ?
                            `<img src="${employee.photo_url}" alt="Foto Karyawan" class="img-thumbnail mb-3" style="max-height: 180px;">` :
                            '<p class="text-muted mb-3 text-start">Foto belum tersedia.</p>';

                        modalTitle.text(`Detail Karyawan: ${fallbackValue(employee.full_name)}`);
                        tableBody.html(fields.map((field) => renderField(field.label, field.value)).join(''));
                        contractsContainer.html(contractsHtml);
                        documentsContainer.html(documentsHtml);
                        photoWrapper.html(photoHtml);
                    })
                    .fail(function() {
                        modalTitle.text('Detail Karyawan');
                        tableBody.html('<tr><td class="text-center text-danger py-3" colspan="2">Gagal memuat data karyawan.</td></tr>');
                        contractsContainer.html('<p class="text-danger mb-0">Arsip kontrak gagal dimuat.</p>');
                        documentsContainer.html('<p class="text-danger mb-0">Dokumen gagal dimuat.</p>');
                        photoWrapper.html('');
                    });
            });

            $(document).on('click', '.btn-upload-hardcopy', function() {
                const employeeId = $(this).data('employee-id');
                const contractId = $(this).data('contract-id');
                const contractNumber = $(this).data('contract-number');
                const actionUrl = contractHardcopyUploadTemplate
                    .replace('__EMP__', employeeId)
                    .replace('__CTR__', contractId);

                $('#upload-hardcopy-form').attr('action', actionUrl);
                $('#upload-hardcopy-form')[0].reset();
                $('#hardcopy-contract-title').text(contractNumber || '-');
                uploadHardcopyModal.show();
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

            function syncContractFields(status) {
                const isPermanent = status === 'tetap';
                $('#period-fields').toggleClass('d-none', isPermanent);
                $('#effective-date-field').toggleClass('d-none', !isPermanent);
                $('input[name="contract_start_date"], input[name="contract_end_date"]').prop('required', !isPermanent);
                $('input[name="effective_date"]').prop('required', isPermanent);
            }

            $('.btn-generate-contract').on('click', function() {
                const employeeId = $(this).data('id');
                const employeeName = $(this).data('name');
                const employeeStatus = $(this).data('status');

                $('#contract-employee-name').text(employeeName || '-');
                $('#contract-employee-status').text(employeeStatus || '-');
                $('#generate-contract-form').attr('action', contractGenerateUrlTemplate.replace('__ID__', employeeId));
                $('#generate-contract-form')[0].reset();
                $('input[name="signing_date"]').val('{{ now()->format('Y-m-d') }}');
                syncContractFields(employeeStatus);

                generateContractModal.show();
            });
        });
    </script>
@endpush
