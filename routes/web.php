<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PakController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PermohonanController;
use App\Http\Controllers\ProyekController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    Route::get('/blank', function () {
        return view('dashboard.blank');
    })->name('blank');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::post('/profile/documents', [ProfileController::class, 'storeDocument'])->name('profile.documents.store');
    Route::delete('/profile/documents/{document}', [ProfileController::class, 'destroyDocument'])->name('profile.documents.destroy');

    Route::prefix('pak')->name('pak.')->group(function () {

        Route::get('/', [PakController::class, 'index'])
            ->middleware('permission:pak.view')
            ->name('index');

        Route::get('/create', [PakController::class, 'create'])
            ->middleware('permission:pak.create')
            ->name('create');

        Route::post('/store', [PakController::class, 'store'])
            ->middleware('permission:pak.create')
            ->name('store');

        Route::get('/{id}', [PakController::class, 'show'])
            ->middleware('permission:pak.view')
            ->name('show');

        Route::get('/{id}/edit', [PakController::class, 'edit'])
            ->middleware('permission:pak.edit')
            ->name('edit');

        Route::get('/{id}/export-pdf', [PakController::class, 'exportPdf'])
            ->middleware('permission:pak.view')
            ->name('export-pdf');

        Route::put('/{id}', [PakController::class, 'update'])
            ->middleware('permission:pak.edit')
            ->name('update');

        Route::post('/{id}/convert', [PakController::class, 'convert'])
            ->middleware('permission:pak.convert')
            ->name('convert');
    });


    Route::get('/users', [UserController::class, 'index'])
        ->middleware('permission:users.view')
        ->name('users.index');

    Route::get('/users/create', [UserController::class, 'create'])
        ->middleware('permission:users.create')
        ->name('users.create');

    Route::post('/users', [UserController::class, 'store'])
        ->middleware('permission:users.create')
        ->name('users.store');

    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->middleware('permission:users.edit')
        ->name('users.edit');

    Route::put('/users/{user}', [UserController::class, 'update'])
        ->middleware('permission:users.edit')
        ->name('users.update');

    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->middleware('permission:users.delete')
        ->name('users.destroy');

    Route::get('/roles', [RoleController::class, 'index'])
        ->middleware('permission:roles.view')
        ->name('roles.index');

    Route::get('/roles/create', [RoleController::class, 'create'])
        ->middleware('permission:roles.create')
        ->name('roles.create');

    Route::post('/roles', [RoleController::class, 'store'])
        ->middleware('permission:roles.create')
        ->name('roles.store');

    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
        ->middleware('permission:roles.edit')
        ->name('roles.edit');

    Route::put('/roles/{role}', [RoleController::class, 'update'])
        ->middleware('permission:roles.edit')
        ->name('roles.update');

    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
        ->middleware('permission:roles.delete')
        ->name('roles.destroy');


    Route::get('/employees', [EmployeeController::class, 'index'])
        ->middleware('permission:employees.view')
        ->name('employees.index');

    Route::get('/employees/create', [EmployeeController::class, 'create'])
        ->middleware('permission:employees.create')
        ->name('employees.create');

    Route::post('/employees', [EmployeeController::class, 'store'])
        ->middleware('permission:employees.create')
        ->name('employees.store');

    Route::get('/employees/{employee}', [EmployeeController::class, 'show'])
        ->middleware('permission:employees.view')
        ->name('employees.show');

    Route::post('/employees/{employee}/contracts/generate', [EmployeeController::class, 'generateContract'])
        ->middleware('permission:employees.edit')
        ->name('employees.contracts.generate');

    Route::post('/employees/{employee}/contracts/{contract}/hardcopy', [EmployeeController::class, 'uploadContractHardcopy'])
        ->middleware('permission:employees.edit')
        ->name('employees.contracts.hardcopy.upload');

    Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])
        ->middleware('permission:employees.edit')
        ->name('employees.edit');

    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])
        ->middleware('permission:employees.edit')
        ->name('employees.update');

    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])
        ->middleware('permission:employees.delete')
        ->name('employees.destroy');

    Route::get('/permohonan', [PermohonanController::class, 'index'])
        ->middleware('permission:permohonan.view')
        ->name('permohonan.index');

    Route::get('/permohonan/create', [PermohonanController::class, 'create'])
        ->middleware('permission:permohonan.create')
        ->name('permohonan.create');

    Route::post('/permohonan', [PermohonanController::class, 'store'])
        ->middleware('permission:permohonan.create')
        ->name('permohonan.store');

    Route::get('/permohonan/{permohonan}', [PermohonanController::class, 'show'])
        ->middleware('permission:permohonan.view')
        ->name('permohonan.show');

    Route::get('/permohonan/{permohonan}/edit', [PermohonanController::class, 'edit'])
        ->middleware('permission:permohonan.edit')
        ->name('permohonan.edit');

    Route::put('/permohonan/{permohonan}', [PermohonanController::class, 'update'])
        ->middleware('permission:permohonan.edit')
        ->name('permohonan.update');

    Route::delete('/permohonan/{permohonan}', [PermohonanController::class, 'destroy'])
        ->middleware('permission:permohonan.delete')
        ->name('permohonan.destroy');

    Route::get('permohonan-dokumen/{dokumen}/preview', [PermohonanController::class, 'previewDokumen'])
        ->middleware('permission:permohonan.view')
        ->name('permohonan.dokumen.preview');

    Route::get('permohonan-dokumen/{dokumen}/download', [PermohonanController::class, 'downloadDokumen'])
        ->middleware('permission:permohonan.view')
        ->name('permohonan.dokumen.download');

    Route::get('permohonan/{id}/export-pdf', [PermohonanController::class, 'exportPdf'])
        ->middleware('permission:permohonan.view')
        ->name('permohonan.export-pdf');

    Route::post('permohonan/{id}/jadikan-project', [PermohonanController::class, 'jadikanProject'])
        ->middleware('permission:permohonan.jadikan_project')
        ->name('permohonan.jadikan-project');

    Route::get('/permohonan/blank/pdf', [PermohonanController::class, 'blankPdf'])
        ->name('permohonan.blank-pdf');

    Route::get('/proyek', [ProyekController::class, 'index'])
        ->middleware('permission:proyek.view')
        ->name('proyek.index');

    Route::get('/proyek/{id}', [ProyekController::class, 'show'])
        ->middleware('permission:proyek.show')
        ->name('proyek.show');

    Route::get('/proyek/{proyek}/pekerjaan/{item}/layanan/{layanan}', [ProyekController::class, 'showPekerjaan'])
        ->middleware('permission:proyek.show')
        ->name('proyek.pekerjaan.show');

    Route::get('/invoice', [InvoiceController::class, 'index'])
        ->middleware('permission:invoice.view')
        ->name('invoice.index');

    Route::get('/invoice/create', [InvoiceController::class, 'create'])
        ->middleware('permission:invoice.create')
        ->name('invoice.create');

    Route::post('/invoice', [InvoiceController::class, 'store'])
        ->middleware('permission:invoice.store')
        ->name('invoice.store');

    Route::get('/invoice/{invoice}/pdf', [InvoiceController::class, 'exportPdf'])
        ->middleware('permission:invoice.export_pdf')
        ->name('invoice.export-pdf');

    Route::post('/invoice/{invoice}/upload-signed', [InvoiceController::class, 'uploadSignedFile'])
        ->middleware('permission:invoice.upload_signed')
        ->name('invoice.upload-signed');


    Route::get('/aset', [AssetController::class, 'index'])
        ->middleware('permission:assets.view')
        ->name('assets.index');

    Route::post('/aset', [AssetController::class, 'store'])
        ->middleware('permission:assets.create')
        ->name('assets.store');

    Route::get('/aset/export-excel', [AssetController::class, 'exportExcel'])
        ->middleware('permission:assets.view')
        ->name('assets.export-excel');

    Route::put('/aset/{asset}', [AssetController::class, 'update'])
        ->middleware('permission:assets.edit')
        ->name('assets.update');

    Route::delete('/aset/{asset}', [AssetController::class, 'destroy'])
        ->middleware('permission:assets.delete')
        ->name('assets.destroy');

    Route::get('/pembayaran', [PembayaranController::class, 'index'])
        ->middleware('permission:pembayaran.view')
        ->name('pembayaran.index');

    Route::get('/pembayaran/create', [PembayaranController::class, 'create'])
        ->middleware('permission:pembayaran.create')
        ->name('pembayaran.create');

    Route::post('/pembayaran', [PembayaranController::class, 'store'])
        ->middleware('permission:pembayaran.store')
        ->name('pembayaran.store');
});

Route::get('/scan/aset/{asset}', [AssetController::class, 'publicShow'])->name('assets.public-show');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
