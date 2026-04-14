@extends('layouts.app')

@section('title', 'Aset')

@section('content')
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2>Manajemen Aset</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Aset</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-style mb-30">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="text-medium mb-0">Data Aset</h6>
            @can('assets.create')
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAssetModal">+ Tambah Aset</button>
            @endcan
        </div>

        <div class="table-wrapper table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>No Aset</th>
                        <th>Nama</th>
                        <th>Lokasi</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Total</th>
                        <th>Remark</th>
                        <th>QR</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assets as $asset)
                        <tr>
                            <td>{{ $asset->no_aset }}</td>
                            <td>
                                <strong>{{ $asset->nama }}</strong><br>
                                <small class="text-muted">{{ $asset->merek ?? '-' }} | {{ $asset->no_seri ?? '-' }}</small>
                            </td>
                            <td>{{ $asset->lokasi }}</td>
                            <td>{{ $asset->jumlah }}</td>
                            <td>Rp {{ number_format($asset->harga, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($asset->total, 0, ',', '.') }}</td>
                            <td>{{ $asset->remark ?? '-' }}</td>
                            <td>
                                @php
                                    $publicUrl = route('assets.public-show', $asset->qr_token);
                                    $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' . urlencode($publicUrl);
                                @endphp
                                <a href="{{ $publicUrl }}" target="_blank" class="btn btn-sm btn-outline-primary">Detail</a>
                                <a href="{{ $qrUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary">QR Code</a>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    @can('assets.edit')
                                        <button type="button" class="btn btn-sm btn-warning btn-edit-asset"
                                            data-bs-toggle="modal" data-bs-target="#editAssetModal"
                                            data-update-url="{{ route('assets.update', $asset->qr_token) }}"
                                            data-nama="{{ $asset->nama }}" data-merek="{{ $asset->merek }}"
                                            data-no_seri="{{ $asset->no_seri }}" data-lokasi="{{ $asset->lokasi }}"
                                            data-jumlah="{{ $asset->jumlah }}" data-harga="{{ (int) $asset->harga }}"
                                            data-tahun="{{ $asset->tahun }}" data-remark="{{ $asset->remark }}">
                                            Edit
                                        </button>
                                    @endcan
                                    @can('assets.delete')
                                        <form method="POST" action="{{ route('assets.destroy', $asset->qr_token) }}" class="form-delete-asset">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger btn-delete-asset">Hapus</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Belum ada data aset.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $assets->links() }}
        </div>
    </div>

    @can('assets.create')
        <div class="modal fade" id="createAssetModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <form method="POST" action="{{ route('assets.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Data Aset</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">No Aset (Auto Generate)</label>
                                    <input type="text" class="form-control" value="{{ $generatedNoAset }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nama</label>
                                    <input type="text" name="nama" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Merek</label>
                                    <input type="text" name="merek" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">No Seri</label>
                                    <input type="text" name="no_seri" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Lokasi</label>
                                    <input type="text" name="lokasi" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Jumlah</label>
                                    <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Harga</label>
                                    <input type="text" id="harga_display" class="form-control" placeholder="Rp 0" inputmode="numeric" required>
                                    <input type="hidden" name="harga" id="harga" value="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Total (Auto)</label>
                                    <input type="text" id="total" class="form-control" value="Rp 0" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">File Faktur</label>
                                    <input type="file" name="file_faktur" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tahun</label>
                                    <input type="number" name="tahun" class="form-control" min="1900" max="2100" value="{{ date('Y') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Remark</label>
                                    <select name="remark" class="form-select">
                                        <option value="">- Pilih Remark -</option>
                                        <option value="baik">Baik</option>
                                        <option value="perlu perbaikan">Perlu Perbaikan</option>
                                        <option value="rusak">Rusak</option>
                                        <option value="hilang">Hilang</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gambar</label>
                                    <input type="file" name="gambar" class="form-control" accept="image/*">
                                </div>
                            </div>
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

    @can('assets.edit')
        <div class="modal fade" id="editAssetModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <form method="POST" id="editAssetForm" action="" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Data Aset</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama</label>
                                    <input type="text" name="nama" id="edit_nama" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Merek</label>
                                    <input type="text" name="merek" id="edit_merek" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">No Seri</label>
                                    <input type="text" name="no_seri" id="edit_no_seri" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Lokasi</label>
                                    <input type="text" name="lokasi" id="edit_lokasi" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Jumlah</label>
                                    <input type="number" name="jumlah" id="edit_jumlah" class="form-control" min="1" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Harga</label>
                                    <input type="text" id="edit_harga_display" class="form-control" placeholder="Rp 0" inputmode="numeric" required>
                                    <input type="hidden" name="harga" id="edit_harga" value="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Total (Auto)</label>
                                    <input type="text" id="edit_total" class="form-control" value="Rp 0" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">File Faktur (opsional, ganti baru)</label>
                                    <input type="file" name="file_faktur" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tahun</label>
                                    <input type="number" name="tahun" id="edit_tahun" class="form-control" min="1900" max="2100" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Remark</label>
                                    <select name="remark" id="edit_remark" class="form-select">
                                        <option value="">- Pilih Remark -</option>
                                        <option value="baik">Baik</option>
                                        <option value="perlu perbaikan">Perlu Perbaikan</option>
                                        <option value="rusak">Rusak</option>
                                        <option value="hilang">Hilang</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gambar (opsional, ganti baru)</label>
                                    <input type="file" name="gambar" class="form-control" accept="image/*">
                                </div>
                            </div>
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
            const jumlahInput = document.getElementById('jumlah');
            const hargaInput = document.getElementById('harga');
            const hargaDisplayInput = document.getElementById('harga_display');
            const totalInput = document.getElementById('total');
            const form = document.querySelector('#createAssetModal form');
            const editForm = document.getElementById('editAssetForm');
            const editJumlahInput = document.getElementById('edit_jumlah');
            const editHargaInput = document.getElementById('edit_harga');
            const editHargaDisplayInput = document.getElementById('edit_harga_display');
            const editTotalInput = document.getElementById('edit_total');

            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID').format(angka);
            }

            function parseNumber(value) {
                return Number(String(value).replace(/[^\d]/g, '') || 0);
            }

            function hitungTotal() {
                const jumlah = Number(jumlahInput?.value || 0);
                const harga = Number(hargaInput?.value || 0);
                totalInput.value = `Rp ${formatRupiah(jumlah * harga)}`;
            }

            jumlahInput?.addEventListener('input', hitungTotal);
            hargaDisplayInput?.addEventListener('input', function() {
                const numericValue = parseNumber(this.value);
                hargaInput.value = numericValue;
                this.value = numericValue ? `Rp ${formatRupiah(numericValue)}` : '';
                hitungTotal();
            });

            form?.addEventListener('submit', function() {
                const numericValue = parseNumber(hargaDisplayInput?.value ?? '');
                hargaInput.value = numericValue;
            });

            function hitungTotalEdit() {
                const jumlah = Number(editJumlahInput?.value || 0);
                const harga = Number(editHargaInput?.value || 0);
                editTotalInput.value = `Rp ${formatRupiah(jumlah * harga)}`;
            }

            editJumlahInput?.addEventListener('input', hitungTotalEdit);
            editHargaDisplayInput?.addEventListener('input', function() {
                const numericValue = parseNumber(this.value);
                editHargaInput.value = numericValue;
                this.value = numericValue ? `Rp ${formatRupiah(numericValue)}` : '';
                hitungTotalEdit();
            });

            editForm?.addEventListener('submit', function() {
                const numericValue = parseNumber(editHargaDisplayInput?.value ?? '');
                editHargaInput.value = numericValue;
            });

            document.querySelectorAll('.btn-edit-asset').forEach((button) => {
                button.addEventListener('click', function() {
                    editForm.action = this.dataset.updateUrl;
                    document.getElementById('edit_nama').value = this.dataset.nama || '';
                    document.getElementById('edit_merek').value = this.dataset.merek || '';
                    document.getElementById('edit_no_seri').value = this.dataset.no_seri || '';
                    document.getElementById('edit_lokasi').value = this.dataset.lokasi || '';
                    document.getElementById('edit_jumlah').value = this.dataset.jumlah || 1;
                    document.getElementById('edit_tahun').value = this.dataset.tahun || new Date().getFullYear();
                    document.getElementById('edit_remark').value = this.dataset.remark || '';

                    const harga = Number(this.dataset.harga || 0);
                    editHargaInput.value = harga;
                    editHargaDisplayInput.value = harga ? `Rp ${formatRupiah(harga)}` : '';
                    hitungTotalEdit();
                });
            });

            document.querySelectorAll('.form-delete-asset').forEach((formDelete) => {
                formDelete.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Hapus data aset?',
                        text: 'Data yang dihapus tidak bisa dikembalikan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            formDelete.submit();
                        }
                    });
                });
            });


            @if ($errors->any())
                const modalEl = document.getElementById('createAssetModal');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                }
            @endif

        });
    </script>
@endpush
