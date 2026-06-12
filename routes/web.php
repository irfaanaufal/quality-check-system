<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BerasController;
use App\Http\Controllers\GabahController;
use App\Http\Controllers\PembagianBerasController;
use App\Http\Controllers\PembagianGabahController;
use App\Http\Controllers\PenerimaanBerasController;
use App\Http\Controllers\PenerimaanGabahController;
use App\Http\Controllers\TimbanganBerasController;
use App\Http\Controllers\TimbanganGabahController;
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

Route::get('/bahan-baku-beras', [BerasController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('bahan_baku_beras.index');

Route::put('/bahan-baku-beras/{id}/update-harga', [BerasController::class, 'updateHarga'])
    ->middleware(['auth', 'verified'])
    ->name('bahan_baku_beras.update_harga');

Route::get('/bahan-baku-beras/{id}/print', [BerasController::class, 'print'])
    ->middleware(['auth', 'verified'])
    ->name('bahan_baku_beras.print');

Route::get('/bahan-baku-gabah', [GabahController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('bahan_baku_gabah.index');

Route::put('/bahan-baku-gabah/{id}/update-harga', [GabahController::class, 'updateHarga'])
    ->middleware(['auth', 'verified'])
    ->name('bahan_baku_gabah.update_harga');

Route::get('/bahan-baku-gabah/{id}/print', [GabahController::class, 'print'])
    ->middleware(['auth', 'verified'])
    ->name('bahan_baku_gabah.print');

Route::get('/penerimaan-beras', [PenerimaanBerasController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('penerimaan_beras.index');

Route::get('/penerimaan-gabah', [PenerimaanGabahController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('penerimaan_gabah.index');

Route::post('/penerimaan-gabah', [PenerimaanGabahController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('penerimaan_gabah.store');

Route::put('/penerimaan-gabah/{id}', [PenerimaanGabahController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('penerimaan_gabah.update');

Route::delete('/penerimaan-gabah/{id}', [PenerimaanGabahController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('penerimaan_gabah.destroy');

Route::post('/penerimaan-gabah/{id}/approve', [PenerimaanGabahController::class, 'approve'])
    ->middleware(['auth', 'verified'])
    ->name('penerimaan_gabah.approve');

Route::post('/penerimaan-gabah/{id}/unapprove', [PenerimaanGabahController::class, 'unapprove'])
    ->middleware(['auth', 'verified'])
    ->name('penerimaan_gabah.unapprove');

Route::post('/penerimaan-gabah/{id}/uncheck', [PenerimaanGabahController::class, 'uncheck'])
    ->middleware(['auth', 'verified'])
    ->name('penerimaan_gabah.uncheck');

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

Route::post('/pembagian-beras/{id}/check', [PembagianBerasController::class, 'check'])
    ->middleware(['auth', 'verified'])
    ->name('pembagian_beras.check');

Route::get('/pembagian-beras/{terimaBbId}/rincian', [PembagianBerasController::class, 'getRincian'])
    ->middleware(['auth', 'verified'])
    ->name('pembagian_beras.rincian');

Route::get('/pembagian-beras/{terimaBbId}/detail/{sortingNumber}', [PembagianBerasController::class, 'getPembagianDetail'])
    ->middleware(['auth', 'verified'])
    ->name('pembagian_beras.detail');

// Pembagian Gabah Routes
Route::get('/pembagian-gabah/create', [PembagianGabahController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('pembagian_gabah.create');

Route::post('/pembagian-gabah', [PembagianGabahController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('pembagian_gabah.store');

Route::post('/pembagian-gabah/{id}/check', [PembagianGabahController::class, 'check'])
    ->middleware(['auth', 'verified'])
    ->name('pembagian_gabah.check');

Route::get('/pembagian-gabah/{terimaBbId}/rincian', [PembagianGabahController::class, 'getRincian'])
    ->middleware(['auth', 'verified'])
    ->name('pembagian_gabah.rincian');

Route::get('/pembagian-gabah/{terimaBbId}/detail/{sortingNumber}', [PembagianGabahController::class, 'getPembagianDetail'])
    ->middleware(['auth', 'verified'])
    ->name('pembagian_gabah.detail');

// Timbangan Beras Routes
Route::get('/timbangan-beras/create', [TimbanganBerasController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('timbangan-beras.create');

Route::post('/timbangan-beras', [TimbanganBerasController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('timbangan-beras.store');

Route::put('/timbangan-beras/{id}', [TimbanganBerasController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('timbangan-beras.update');

Route::delete('/timbangan-beras/{id}', [TimbanganBerasController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('timbangan-beras.destroy');

Route::post('/timbangan-beras/{id}/selesai-timbang', [TimbanganBerasController::class, 'selesaiTimbang'])
    ->middleware(['auth', 'verified'])
    ->name('timbangan-beras.selesai_timbang');

Route::post('/timbangan-beras/{id}/validate', [TimbanganBerasController::class, 'validateApproval'])
    ->middleware(['auth', 'verified'])
    ->name('timbangan-beras.validate');

// Timbangan Gabah Routes
Route::get('/timbangan-gabah/create', [TimbanganGabahController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('timbangan-gabah.create');

Route::post('/timbangan-gabah', [TimbanganGabahController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('timbangan-gabah.store');

Route::put('/timbangan-gabah/{id}', [TimbanganGabahController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('timbangan-gabah.update');

Route::delete('/timbangan-gabah/{id}', [TimbanganGabahController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('timbangan-gabah.destroy');

Route::post('/timbangan-gabah/{id}/selesai-timbang', [TimbanganGabahController::class, 'selesaiTimbang'])
    ->middleware(['auth', 'verified'])
    ->name('timbangan-gabah.selesai_timbang');

Route::post('/timbangan-gabah/{id}/validate', [TimbanganGabahController::class, 'validateApproval'])
    ->middleware(['auth', 'verified'])
    ->name('timbangan-gabah.validate');

Route::post('/penerimaan-beras/{id}/approve', [PenerimaanBerasController::class, 'approve'])
    ->middleware(['auth', 'verified'])
    ->name('penerimaan_beras.approve');

Route::post('/penerimaan-beras/{id}/unapprove', [PenerimaanBerasController::class, 'unapprove'])
    ->middleware(['auth', 'verified'])
    ->name('penerimaan_beras.unapprove');

Route::post('/penerimaan-beras/{id}/uncheck', [PenerimaanBerasController::class, 'uncheck'])
    ->middleware(['auth', 'verified'])
    ->name('penerimaan_beras.uncheck');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
