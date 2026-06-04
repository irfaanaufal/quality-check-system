<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BerasController;
use App\Http\Controllers\PembagianBerasController;
use App\Http\Controllers\PenerimaanBerasController;
use App\Http\Controllers\TimbanganController;
use App\Models\Beras;
use App\Models\KriteriaBb;
use App\Models\ReportTimbangBeras;
use App\Models\TerimaBb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/beras', [BerasController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('beras.index');

Route::get('/penerimaan-beras', [PenerimaanBerasController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('penerimaan_beras.index');

Route::post('/penerimaan-beras', [PenerimaanBerasController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('penerimaan_beras.store');

Route::put('/penerimaan-beras/{id}', [PenerimaanBerasController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('penerimaan_beras.update');

Route::delete('/penerimaan-beras/{id}', [PenerimaanBerasController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('penerimaan_beras.destroy');

Route::get('/penerimaan-beras/create', function () {
    return redirect()->route('penerimaan_beras.index');
})->middleware(['auth', 'verified'])->name('penerimaan_beras.create');

Route::get('/pembagian-beras/create', [PembagianBerasController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('pembagian_beras.create');

Route::post('/pembagian-beras', [PembagianBerasController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('pembagian_beras.store');

Route::get('/pembagian-beras/{terimaBbId}/rincian', [PembagianBerasController::class, 'getRincian'])
    ->middleware(['auth', 'verified'])
    ->name('pembagian_beras.rincian');

Route::get('/pembagian-beras/{terimaBbId}/detail/{sortingNumber}', [PembagianBerasController::class, 'getPembagianDetail'])
    ->middleware(['auth', 'verified'])
    ->name('pembagian_beras.detail');

Route::get('/timbangan/create', [TimbanganController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('timbangan.create');

Route::post('/timbangan', [TimbanganController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('timbangan.store');

Route::get('/timbangan/{id}/edit', [TimbanganController::class, 'edit'])
    ->middleware(['auth', 'verified'])
    ->name('timbangan.edit');

Route::put('/timbangan/{id}', [TimbanganController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('timbangan.update');

Route::delete('/timbangan/{id}', [TimbanganController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('timbangan.destroy');

Route::post('/timbangan/{id}/selesai-timbang', [TimbanganController::class, 'selesaiTimbang'])
    ->middleware(['auth', 'verified'])
    ->name('timbangan.selesai_timbang');

Route::post('/timbangan/{id}/validate', [TimbanganController::class, 'validateApproval'])
    ->middleware(['auth', 'verified'])
    ->name('timbangan.validate');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
