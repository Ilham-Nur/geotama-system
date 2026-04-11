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

    <div class="row">
        <div class="col-12">
            <div class="card-style mb-30">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h6 class="mb-0">Daftar Karyawan</h6>
                    <div class="d-flex gap-2">
                        <input type="text" id="search-input" class="form-control" placeholder="Cari nama / NIK / posisi"
                            value="{{ $search }}">
                        @can('employees.create')
                            <button type="button" class="btn btn-primary" id="btn-add-employee">Tambah Karyawan</button>
                        @endcan
                    </div>
                </div>

                <div id="employees-table-wrapper">
                    @include('employees.partials.table', ['employees' => $employees])
                </div>
            </div>
        </div>
    </div>

    @canany(['employees.create', 'employees.edit'])
        <div class="modal fade" id="employeeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form id="employee-form">
                        <div class="modal-header">
                            <h5 class="modal-title" id="employeeModalTitle">Tambah Karyawan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Kode Karyawan / NIK</label>
                                    <input type="text" name="employee_code" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="full_name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Posisi</label>
                                    <input type="text" name="position" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">No Telepon</label>
                                    <input type="text" name="phone" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Bergabung</label>
                                    <input type="date" name="hire_date" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status Kepegawaian</label>
                                    <select name="employment_status" class="form-select" required>
                                        <option value="tetap">Tetap</option>
                                        <option value="kontrak" selected>Kontrak</option>
                                        <option value="magang">Magang</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="main-btn danger-btn-outline btn-hover" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="main-btn primary-btn btn-hover" id="btn-submit-employee">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcanany
@endsection

@push('scripts')
    <script>
        $(function() {
            const canCreate = @json(auth()->user()->can('employees.create'));
            const canEdit = @json(auth()->user()->can('employees.edit'));
            const canDelete = @json(auth()->user()->can('employees.delete'));
            const tableWrapper = $('#employees-table-wrapper');
            const searchInput = $('#search-input');
            const employeeModalElement = document.getElementById('employeeModal');
            const employeeModal = employeeModalElement ? new bootstrap.Modal(employeeModalElement) : null;

            let editId = null;

            function refreshTable(pageUrl = null) {
                const url = pageUrl || "{{ route('employees.index') }}";
                $.get(url, {
                    search: searchInput.val()
                }, function(html) {
                    tableWrapper.html(html);
                });
            }

            function resetForm() {
                editId = null;
                $('#employeeModalTitle').text('Tambah Karyawan');
                $('#employee-form')[0].reset();
            }

            function showValidationErrors(errors) {
                const messages = Object.values(errors).flat().join('\n');
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: messages
                });
            }

            let searchTimer;
            searchInput.on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => refreshTable(), 400);
            });

            $(document).on('click', '#btn-add-employee', function() {
                if (!canCreate || !employeeModal) {
                    return;
                }

                resetForm();
                employeeModal.show();
            });

            $(document).on('click', '.btn-edit-employee', function() {
                if (!canEdit || !employeeModal) {
                    return;
                }

                editId = $(this).data('id');

                $.get(`{{ url('employees') }}/${editId}`, function(data) {
                    $('#employeeModalTitle').text('Edit Karyawan');
                    $('#employee-form [name="employee_code"]').val(data.employee_code);
                    $('#employee-form [name="full_name"]').val(data.full_name);
                    $('#employee-form [name="position"]').val(data.position);
                    $('#employee-form [name="phone"]').val(data.phone);
                    $('#employee-form [name="hire_date"]').val(data.hire_date);
                    $('#employee-form [name="employment_status"]').val(data.employment_status);
                    employeeModal.show();
                });
            });

            $('#employee-form').on('submit', function(e) {
                e.preventDefault();

                const isEdit = !!editId;
                const url = isEdit ? `{{ url('employees') }}/${editId}` : "{{ route('employees.store') }}";
                const method = isEdit ? 'PUT' : 'POST';
                const formData = $(this).serialize();

                $.ajax({
                    url,
                    type: method,
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false,
                        });

                        employeeModal.hide();
                        resetForm();
                        refreshTable();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            showValidationErrors(xhr.responseJSON.errors || {});
                            return;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Aksi tidak dapat diproses saat ini.'
                        });
                    }
                });
            });

            $(document).on('click', '.btn-delete-employee', function() {
                if (!canDelete) {
                    return;
                }

                const id = $(this).data('id');

                Swal.fire({
                    title: 'Hapus karyawan ini?',
                    text: 'Data yang dihapus tidak bisa dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: `{{ url('employees') }}/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false,
                            });
                            refreshTable();
                        },
                    });
                });
            });

            $(document).on('click', '#employees-table-wrapper .pagination a', function(e) {
                e.preventDefault();
                refreshTable($(this).attr('href'));
            });
        });
    </script>
@endpush
