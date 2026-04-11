@extends('layouts.app')

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
            {{-- INFORMASI PEKERJAAN --}}
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

            {{-- FORM LAPORAN --}}
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Laporan</h5>
                    </div>
                    <div class="card-body">
                        <form action="#" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="dimensi" class="form-label">Dimensi</label>
                                    <input type="text" class="form-control" id="dimensi" name="dimensi"
                                        placeholder="Contoh: 200 mm x 300 mm">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_pelaksanaan" class="form-label">Tanggal Pelaksanaan</label>
                                    <input type="date" class="form-control" id="tanggal_pelaksanaan"
                                        name="tanggal_pelaksanaan">
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="foto" class="form-label">Foto</label>
                                    <input type="file" class="form-control" id="foto" name="foto">
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Tabel Result</h5>
                                <button type="button" class="btn btn-sm btn-success" onclick="addResultRow()">
                                    + Tambah Baris
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle" id="resultTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;">No</th>
                                            <th>Parameter</th>
                                            <th>Standar</th>
                                            <th>Hasil</th>
                                            <th>Keterangan</th>
                                            <th style="width: 100px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center">1</td>
                                            <td>
                                                <input type="text" name="results[0][parameter]" class="form-control"
                                                    placeholder="Masukkan parameter">
                                            </td>
                                            <td>
                                                <input type="text" name="results[0][standar]" class="form-control"
                                                    placeholder="Masukkan standar">
                                            </td>
                                            <td>
                                                <input type="text" name="results[0][hasil]" class="form-control"
                                                    placeholder="Masukkan hasil">
                                            </td>
                                            <td>
                                                <input type="text" name="results[0][keterangan]" class="form-control"
                                                    placeholder="Masukkan keterangan">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="removeRow(this)">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 text-end">
                                <button type="button" class="btn btn-secondary">Simpan Draft</button>
                                <button type="submit" class="btn btn-primary">Simpan Laporan</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- PREVIEW FOTO / CATATAN --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Preview Tambahan</h5>
                    </div>
                    <div class="card-body">
                        <div class="border rounded p-4 text-center text-muted">
                            Preview foto / lampiran nanti tampil di sini
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function addResultRow() {
            const table = document.querySelector('#resultTable tbody');
            const rowCount = table.rows.length;
            const newRow = document.createElement('tr');

            newRow.innerHTML = `
                <td class="text-center">${rowCount + 1}</td>
                <td>
                    <input type="text" name="results[${rowCount}][parameter]" class="form-control" placeholder="Masukkan parameter">
                </td>
                <td>
                    <input type="text" name="results[${rowCount}][standar]" class="form-control" placeholder="Masukkan standar">
                </td>
                <td>
                    <input type="text" name="results[${rowCount}][hasil]" class="form-control" placeholder="Masukkan hasil">
                </td>
                <td>
                    <input type="text" name="results[${rowCount}][keterangan]" class="form-control" placeholder="Masukkan keterangan">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">
                        Hapus
                    </button>
                </td>
            `;

            table.appendChild(newRow);
            resetRowNumber();
        }

        function removeRow(button) {
            const row = button.closest('tr');
            row.remove();
            resetRowNumber();
        }

        function resetRowNumber() {
            const rows = document.querySelectorAll('#resultTable tbody tr');
            rows.forEach((row, index) => {
                row.querySelector('td:first-child').innerText = index + 1;
            });
        }
    </script>
@endsection
