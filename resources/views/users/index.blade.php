@extends('layouts.app')

@section('title', 'User Management')

@section('content')

    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2>User Management</h2>
                </div>
            </div>
            <!-- end col -->
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            {{-- <li class="breadcrumb-item">
                                <a href="#0">Dashboard</a>
                            </li> --}}
                            <li class="breadcrumb-item active" aria-current="page">
                                User Management
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
                    <h6 class="mb-0">Daftar User</h6>

                    <div class="d-flex gap-2 flex-wrap">
                        <form action="{{ route('users.index') }}" method="GET" class="d-flex gap-2">
                            <input type="text" name="search" value="{{ $search }}" class="form-control"
                                placeholder="Cari nama / username / email">
                            <button type="submit" class="btn btn-outline-primary">Cari</button>
                        </form>

                        @can('users.create')
                            <a href="{{ route('users.create') }}" class="btn btn-primary">Tambah User</a>
                        @endcan
                    </div>
                </div>

                <div class="table-wrapper table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="60">No</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th width="180">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $user)
                                <tr>
                                    <td>{{ $users->firstItem() + $index }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @forelse($user->roles as $role)
                                            <span class="badge bg-info">{{ $role->name }}</span>
                                        @empty
                                            <span class="text-muted">Tidak ada role</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        @can('users.edit')
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning">
                                                Edit
                                            </a>
                                        @endcan

                                        @can('users.delete')
                                            @if (auth()->id() !== $user->id)
                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                    class="d-inline" onsubmit="return confirm('Yakin ingin hapus user ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada data user</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
