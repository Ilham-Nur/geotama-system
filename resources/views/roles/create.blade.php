@extends('layouts.app')

@section('title', 'Tambah Role')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="title-wrapper pt-30">
            <div class="title">
                <h2>Tambah Role</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card-style mb-30">
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nama Role</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" placeholder="Contoh: manager" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Permissions</label>
                    <div class="row">
                        @foreach($permissions as $permission)
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input"
                                        type="checkbox"
                                        name="permissions[]"
                                        value="{{ $permission->name }}"
                                        id="permission_{{ $loop->index }}"
                                        {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permission_{{ $loop->index }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('permissions')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection