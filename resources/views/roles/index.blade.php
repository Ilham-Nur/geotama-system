@extends('layouts.app')

@section('title', 'Role Management')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="title-wrapper pt-30">
                <div class="title">
                    <h2>Role Management</h2>
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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Daftar Role</h6>

                    @can('roles.create')
                        <a href="{{ route('roles.create') }}" class="btn btn-primary">
                            Tambah Role
                        </a>
                    @endcan
                </div>

                <div class="table-wrapper table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="60">No</th>
                                <th>Nama Role</th>
                                <th>Permissions</th>
                                <th width="180">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $index => $role)
                                <tr>
                                    <td>{{ $roles->firstItem() + $index }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        @forelse($role->permissions as $permission)
                                            <span class="badge bg-info me-1 mb-1">{{ $permission->name }}</span>
                                        @empty
                                            <span class="text-muted">Belum ada permission</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        @can('roles.edit')
                                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-warning">
                                                Edit
                                            </a>
                                        @endcan

                                        @can('roles.delete')
                                            @if ($role->name !== 'super-admin')
                                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                                    class="d-inline" onsubmit="return confirm('Yakin ingin hapus role ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada data role</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $roles->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
