<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PakController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PermohonanController;
use App\Http\Controllers\ProyekController;
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

    Route::prefix('pak')->name('pak.')->group(function () {

        Route::get('/', [PakController::class, 'index'])->name('index');

        Route::get('/create', [PakController::class, 'create'])->name('create');

        Route::post('/store', [PakController::class, 'store'])->name('store');

        Route::get('/{id}', [PakController::class, 'show'])->name('show');

        Route::get('/{id}/edit', [PakController::class, 'edit'])->name('edit');

        Route::put('/{id}', [PakController::class, 'update'])->name('update');

        Route::post('/{id}/convert', [PakController::class, 'convert'])->name('convert');
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

    Route::resource('roles', RoleController::class)->middleware('permission:roles.view');


    Route::get('/employees', [EmployeeController::class, 'index'])
        ->middleware('permission:employees.view')
        ->name('employees.index');

    Route::get('/employees/create', [EmployeeController::class, 'create'])
        ->middleware('permission:employees.create')
        ->name('employees.create');

    Route::post('/employees', [EmployeeController::class, 'store'])
        ->middleware('permission:employees.create')
        ->name('employees.store');

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
        ->middleware('permission:permohonan.edit')
        ->name('permohonan.jadikan-project');

    Route::get('/proyek', [ProyekController::class, 'index'])->name('proyek.index');
    Route::get('/proyek/{id}', [ProyekController::class, 'show'])
        ->name('proyek.show');
    Route::get('/proyek/{proyek}/pekerjaan/{item}/layanan/{layanan}', [ProyekController::class, 'showPekerjaan'])
        ->name('proyek.pekerjaan.show');

    Route::resource('invoice', InvoiceController::class)->only(['index', 'create', 'store']);
    Route::get('/invoice/{invoice}/pdf', [InvoiceController::class, 'exportPdf'])
        ->name('invoice.export-pdf');

    Route::post('/invoice/{invoice}/upload-signed', [InvoiceController::class, 'uploadSignedFile'])
        ->name('invoice.upload-signed');

    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/create', [PembayaranController::class, 'create'])->name('pembayaran.create');
    Route::post('/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
