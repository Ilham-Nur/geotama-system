@extends('layouts.app')

@section('title', 'Surat Tugas')

@section('content')
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2>Surat Tugas</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Surat Tugas</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card-style mb-30">
                <div class="title d-flex flex-wrap align-items-center justify-content-between">
                    <h6 class="text-medium mb-30">List Surat Tugas</h6>

                    @can('surat_tugas.create')
                        <button class="btn btn-primary mb-30" data-bs-toggle="modal" data-bs-target="#createSuratTugasModal">
                            + Tambah Surat Tugas
                        </button>
                    @endcan
                </div>

                <div class="table-wrapper table-responsive">
                    <table id="tableSuratTugas" class="table">
                        <thead>
                            <tr>
                                <th>Proyek</th>
                                <th>Tgl Berangkat</th>
                                <th>Tgl Kembali</th>
                                <th>Transportasi</th>
                                <th>Keterangan</th>
                                <th>Grand Total</th>
                                <th>Item Biaya</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($suratTugas as $surat)
                                <tr>
                                    <td>{{ $surat->proyek?->no_proyek ?? '-' }}</td>
                                    <td>{{ $surat->tanggal_berangkat?->format('d-m-Y') }}</td>
                                    <td>{{ $surat->tanggal_kembali?->format('d-m-Y') }}</td>
                                    <td>{{ $surat->transportasi }}</td>
                                    <td>{{ $surat->keterangan ?? '-' }}</td>
                                    <td>Rp {{ number_format($surat->grand_total, 0, ',', '.') }}</td>
                                    <td>{{ $surat->biayaItems->count() }} item</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            @can('surat_tugas.edit')
                                                <button class="btn btn-sm btn-warning btn-edit-surat" data-bs-toggle="modal"
                                                    data-bs-target="#editSuratTugasModal"
                                                    data-id="{{ $surat->id }}"
                                                    data-update-url="{{ route('surat-tugas.update', $surat) }}"
                                                    data-proyek-id="{{ $surat->proyek_id }}"
                                                    data-tanggal-berangkat="{{ $surat->tanggal_berangkat?->format('Y-m-d') }}"
                                                    data-tanggal-kembali="{{ $surat->tanggal_kembali?->format('Y-m-d') }}"
                                                    data-transportasi="{{ $surat->transportasi }}"
                                                    data-keterangan="{{ $surat->keterangan }}"
                                                    data-items='@json($surat->biayaItems->map(fn($item) => ["deskripsi" => $item->deskripsi, "qty" => $item->qty, "total" => (float) $item->total])->values())'>
                                                    Edit
                                                </button>
                                            @endcan
                                            @can('surat_tugas.delete')
                                                <form method="POST" action="{{ route('surat-tugas.destroy', $surat) }}" class="form-delete-surat">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Belum ada data surat tugas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $suratTugas->links() }}
                </div>
            </div>
        </div>
    </div>

    @can('surat_tugas.create')
        <div class="modal fade" id="createSuratTugasModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <form method="POST" action="{{ route('surat-tugas.store') }}" id="createSuratForm" class="form-confirm-submit"
                        data-confirm-title="Simpan surat tugas baru?" data-confirm-text="Pastikan data sudah benar.">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Surat Tugas</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            @include('surat-tugas.partials.form-fields', ['mode' => 'create'])
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('surat_tugas.edit')
        <div class="modal fade" id="editSuratTugasModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <form method="POST" action="" id="editSuratForm" class="form-confirm-submit"
                        data-confirm-title="Update surat tugas?" data-confirm-text="Perubahan akan disimpan.">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Surat Tugas</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            @include('surat-tugas.partials.form-fields', ['mode' => 'edit'])
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formatRupiah = (value) => new Intl.NumberFormat('id-ID').format(Number(value || 0));
            const parseNumber = (value) => Number(String(value || '').replace(/[^\d]/g, '') || 0);

            function recalculateGrandTotal(formId) {
                const rows = document.querySelectorAll(`#${formId} .biaya-row`);
                let total = 0;
                rows.forEach(row => {
                    total += parseNumber(row.querySelector('.item-total-display').value);
                });
                document.querySelector(`#${formId} .grand-total-display`).value = `Rp ${formatRupiah(total)}`;
            }

            function bindRowEvents(formId, row) {
                const totalDisplay = row.querySelector('.item-total-display');
                const totalHidden = row.querySelector('.item-total-hidden');

                totalDisplay.addEventListener('input', function() {
                    const numeric = parseNumber(this.value);
                    this.value = numeric ? `Rp ${formatRupiah(numeric)}` : '';
                    totalHidden.value = numeric;
                    recalculateGrandTotal(formId);
                });

                row.querySelector('.btn-remove-row').addEventListener('click', function() {
                    const wrapper = document.querySelector(`#${formId} .biaya-wrapper`);
                    if (wrapper.querySelectorAll('.biaya-row').length <= 1) {
                        Swal.fire('Info', 'Minimal harus ada 1 item biaya.', 'info');
                        return;
                    }
                    row.remove();
                    recalculateGrandTotal(formId);
                });
            }

            function createItemRow(formId, item = null) {
                const wrapper = document.querySelector(`#${formId} .biaya-wrapper`);
                const idx = wrapper.querySelectorAll('.biaya-row').length;
                const html = `
                    <div class="row g-2 biaya-row mb-2">
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="items[${idx}][deskripsi]" placeholder="Deskripsi" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" name="items[${idx}][qty]" min="1" placeholder="Qty" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control item-total-display" placeholder="Total" required>
                            <input type="hidden" class="item-total-hidden" name="items[${idx}][total]" value="0">
                        </div>
                        <div class="col-md-1 d-grid">
                            <button type="button" class="btn btn-danger btn-remove-row">-</button>
                        </div>
                    </div>
                `;

                wrapper.insertAdjacentHTML('beforeend', html);
                const newRow = wrapper.querySelectorAll('.biaya-row')[idx];
                bindRowEvents(formId, newRow);

                if (item) {
                    newRow.querySelector(`input[name="items[${idx}][deskripsi]"]`).value = item.deskripsi || '';
                    newRow.querySelector(`input[name="items[${idx}][qty]"]`).value = item.qty || 1;
                    const totalValue = Number(item.total || 0);
                    newRow.querySelector('.item-total-display').value = totalValue ? `Rp ${formatRupiah(totalValue)}` : '';
                    newRow.querySelector('.item-total-hidden').value = totalValue;
                }

                recalculateGrandTotal(formId);
            }

            ['createSuratForm', 'editSuratForm'].forEach(formId => {
                const form = document.getElementById(formId);
                if (!form) return;

                form.querySelector('.btn-add-row').addEventListener('click', function() {
                    createItemRow(formId);
                });

                form.querySelectorAll('.biaya-row').forEach(row => bindRowEvents(formId, row));
                recalculateGrandTotal(formId);
            });

            document.querySelectorAll('.btn-edit-surat').forEach(button => {
                button.addEventListener('click', function() {
                    const form = document.getElementById('editSuratForm');
                    form.action = this.dataset.updateUrl;
                    form.querySelector('[name="proyek_id"]').value = this.dataset.proyekId || '';
                    form.querySelector('[name="tanggal_berangkat"]').value = this.dataset.tanggalBerangkat || '';
                    form.querySelector('[name="tanggal_kembali"]').value = this.dataset.tanggalKembali || '';
                    form.querySelector('[name="transportasi"]').value = this.dataset.transportasi || '';
                    form.querySelector('[name="keterangan"]').value = this.dataset.keterangan || '';

                    const wrapper = form.querySelector('.biaya-wrapper');
                    wrapper.innerHTML = '';

                    let items = [];
                    try {
                        items = JSON.parse(this.dataset.items || '[]');
                    } catch (error) {
                        items = [];
                    }

                    if (!items.length) {
                        createItemRow('editSuratForm');
                    } else {
                        items.forEach(item => createItemRow('editSuratForm', item));
                    }
                });
            });

            document.querySelectorAll('.form-confirm-submit').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: form.dataset.confirmTitle || 'Yakin?',
                        text: form.dataset.confirmText || 'Data akan diproses.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, lanjutkan',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });

            document.querySelectorAll('.form-delete-surat').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Hapus surat tugas?',
                        text: 'Data yang dihapus tidak bisa dikembalikan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });

            @if (session('success'))
                Swal.fire('Berhasil', @json(session('success')), 'success');
            @endif

            @if ($errors->any())
                Swal.fire('Validasi gagal', 'Silakan cek kembali data input.', 'error');
            @endif
        });
    </script>
@endpush
