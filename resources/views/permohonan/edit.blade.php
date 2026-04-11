@extends('layouts.app')

@section('title', 'Edit Permohonan')

@section('content')
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2> Tambah Permohonan</h2>
                </div>
            </div>
            <!-- end col -->
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('permohonan.index') }}">Permohonan</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Tambah Permohonan
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

    <div class="card-style mb-30">
        <form id="permohonan-form" action="{{ route('permohonan.update', $permohonan->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('permohonan._form')


            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="main-btn primary-btn btn-hover" id="btn-submit-form">
                    <span class="btn-text">Simpan</span>
                    <span class="btn-loading d-none">
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Menyimpan...
                    </span>
                </button>

                <a href="{{ route('permohonan.index') }}" class="main-btn light-btn btn-hover">
                    Kembali
                </a>
            </div>
        </form>
    </div>
@endsection
