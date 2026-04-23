@extends('layouts.app')

@section('title', 'Surat Tugas')

@section('content')
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2>Surat Tugas</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">
                                Surat Tugas
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card-style mb-30">
                <div class="title d-flex flex-wrap align-items-center justify-content-between">
                    <div class="left">
                        <h6 class="text-medium mb-30">List Surat Tugas</h6>
                    </div>

                    <div class="col-md-6 mb-30 text-end">
                       
                    </div>
                </div>

                <div class="table-wrapper table-responsive">
                    <table id="tableSuratTugas" class="table">
                        
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

