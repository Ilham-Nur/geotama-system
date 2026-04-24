@extends('layouts.app')

@section('title', 'Edit Surat Tugas')

@section('content')
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2>Edit Surat Tugas</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('surat-tugas.index') }}">Surat Tugas</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="card-style mb-30">
        <form action="{{ route('surat-tugas.update', $suratTugas->id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('surat-tugas._form')
        </form>
    </div>
@endsection
