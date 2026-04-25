@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="auth-card">
        <div class="auth-logo">
            <h3 class="text-white">GEOTAMA-SYSTEM</h3>
            <p class="text-white">Silakan login untuk masuk ke sistem</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.process') }}">
            @csrf

            <div class="mb-3">
                <label for="login" class="form-label">Email atau Username</label>
                <input type="text" name="login" id="login"
                    class="form-control @error('login') is-invalid @enderror" value="{{ old('login') }}"
                    placeholder="Masukkan email atau username" required autofocus>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password"
                    class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan password" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-login">
                    Login
                </button>
            </div>
        </form>
    </div>
@endsection
