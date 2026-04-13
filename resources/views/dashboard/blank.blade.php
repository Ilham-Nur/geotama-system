@extends('layouts.app')

@section('title', 'Halaman tidak ditemukan')

@section('content')

    <style>
        .text-center {
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2>Halaman tidak ditemukan</h2>
                </div>
            </div>

            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">
                                404 Not Found
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- ========== title-wrapper end ========== -->

    <div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
        <div class="text-center">

            <!-- ICON / ILUSTRASI -->
            <div class="mb-4">
                <i class="lni lni-warning text-danger" style="font-size: 80px;"></i>
            </div>

            <!-- TITLE -->
            <h1 class="fw-bold" style="font-size: 80px;">404</h1>

            <!-- MESSAGE -->
            <h4 class="mb-2">Halaman tidak ditemukan</h4>
            <p class="text-muted mb-4">
                Halaman yang kamu cari tidak tersedia atau sudah dipindahkan.
            </p>

            <!-- ACTION -->
            <a href="{{ route('proyek.index') }}" class="btn btn-primary px-4">
                Kembali ke Proyek
            </a>

        </div>
    </div>



@endsection
