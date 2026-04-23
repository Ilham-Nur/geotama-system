@extends('layouts.app')

@section('title', 'Tambah Quotation')

@section('content')
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2>Tambah Quotation</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('quotation.index') }}">Quotation</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah Quotation</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card-style mb-30">
                <form action="{{ route('quotation.store') }}" method="POST">
                    @csrf
                    @include('quotation._form')
                </form>
            </div>
        </div>
    </div>
@endsection
