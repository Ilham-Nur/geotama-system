@extends('layouts.app')

@section('title', 'NDT Master Data')

@section('content')
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2>NDT Master Data</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">NDT Master Data</li>
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
        <ul class="nav nav-tabs mb-4" role="tablist">
            @foreach ($types as $type => $config)
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $activeType === $type ? 'active' : '' }}"
                        href="{{ route('ndt-master-data.index', ['type' => $type]) }}">
                        {{ $config['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content">
            @foreach ($types as $type => $config)
                <div class="tab-pane fade {{ $activeType === $type ? 'show active' : '' }}">
                    @can('ndt_master.create')
                        <form method="POST" action="{{ route('ndt-master-data.store', $type) }}" enctype="multipart/form-data"
                            class="border rounded p-3 mb-4">
                            @csrf
                            <h6 class="mb-3">Tambah {{ $config['label'] }}</h6>
                            @include('ndt-master-data.partials.form-fields', [
                                'type' => $type,
                                'record' => null,
                                'approvalRoles' => $approvalRoles,
                            ])
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    @endcan

                    <div class="table-wrapper table-responsive">
                        <table class="table align-middle ndt-master-table">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th width="180">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data[$type] as $record)
                                    <tr>
                                        <td>
                                            @if (in_array($type, ['procedures', 'criteria', 'standards'], true))
                                                <strong>{{ $record->code }}</strong>
                                                <small class="d-block text-muted">{{ $record->name ?? '-' }}</small>
                                            @elseif ($type === 'descriptions')
                                                <strong>{{ $record->name }}</strong>
                                                @if ($record->sketch_url)
                                                    <a href="{{ $record->sketch_url }}" target="_blank" class="d-block small">Lihat sketch</a>
                                                @endif
                                            @elseif ($type === 'certificates')
                                                <strong>{{ $record->title }}</strong>
                                                <small class="d-block text-muted">{{ $record->certificate_no ?? '-' }}</small>
                                                @if ($record->url)
                                                    <a href="{{ $record->url }}" target="_blank" class="d-block small">Lihat file</a>
                                                @endif
                                                @if ($record->preview_url)
                                                    <a href="{{ $record->preview_url }}" target="_blank" class="d-block small text-success">Lihat preview report</a>
                                                @endif
                                            @else
                                                <strong>{{ $record->name }}</strong>
                                                <small class="d-block text-muted">{{ $approvalRoles[$record->role] ?? $record->role }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($type === 'certificates')
                                                {{ $record->type ?? '-' }}
                                                <small class="d-block text-muted">
                                                    Exp: {{ optional($record->expired_at)->format('d-m-Y') ?? '-' }}
                                                </small>
                                            @elseif ($type === 'approvals')
                                                {{ $record->position ?? '-' }}
                                            @else
                                                {{ $record->description ?? '-' }}
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $record->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $record->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                @can('ndt_master.edit')
                                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                        data-bs-target="#edit-{{ $type }}-{{ $record->id }}">
                                                        Edit
                                                    </button>
                                                @endcan
                                                @can('ndt_master.delete')
                                                    <form method="POST"
                                                        action="{{ route('ndt-master-data.destroy', [$type, $record->id]) }}"
                                                        onsubmit="return confirm('Yakin hapus data ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @can('ndt_master.edit')
                        @foreach ($data[$type] as $record)
                            <div class="modal fade" id="edit-{{ $type }}-{{ $record->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('ndt-master-data.update', [$type, $record->id]) }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit {{ $config['label'] }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                @include('ndt-master-data.partials.form-fields', [
                                                    'type' => $type,
                                                    'record' => $record,
                                                    'approvalRoles' => $approvalRoles,
                                                ])
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endcan
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.ndt-master-table').DataTable({
                language: {
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                    infoEmpty: 'Belum ada data untuk ditampilkan',
                    emptyTable: 'Belum ada data master.',
                    zeroRecords: 'Data tidak ditemukan.',
                    paginate: {
                        next: 'Berikutnya',
                        previous: 'Sebelumnya'
                    }
                }
            });
        });
    </script>
@endpush
