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
                                <a href="{{ $publicUrl }}" target="_blank" class="btn btn-sm btn-outline-primary mb-1">Detail</a>
                                <a href="{{ $qrUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary">QR Code</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada data aset.</td>
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
                                    <input type="number" name="harga" id="harga" class="form-control" step="0.01" min="0" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Total (Auto)</label>
                                    <input type="number" id="total" class="form-control" readonly>
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
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const jumlahInput = document.getElementById('jumlah');
            const hargaInput = document.getElementById('harga');
            const totalInput = document.getElementById('total');

            function hitungTotal() {
                const jumlah = Number(jumlahInput?.value || 0);
                const harga = Number(hargaInput?.value || 0);
                totalInput.value = (jumlah * harga).toFixed(2);
            }

            jumlahInput?.addEventListener('input', hitungTotal);
            hargaInput?.addEventListener('input', hitungTotal);


            @if ($errors->any()) {
                const modalEl = document.getElementById('createAssetModal');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                }
            }

        });
    </script>
@endpush
