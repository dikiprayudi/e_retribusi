<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\StsPrintController;
use App\Http\Controllers\LaporanBpjsController;
use App\Http\Controllers\LaporanBkuController;

// Ganti route bawaan 'welcome' menjadi 'landing'
Route::get('/', function () {
    return view('landing');
});

Route::middleware(['auth']) 
    ->group(function () {
    
    Route::get('/laporan/bpjs/cetak', [LaporanBpjsController::class, 'cetak'])
         ->name('laporan.cetak-bpjs');
         
    Route::get('/laporan/bku/cetak', [LaporanBkuController::class, 'cetak'])
         ->name('laporan.cetak-bku');

    // --- 2. TAMBAHKAN RUTE BARU INI ---
    Route::get('/print/rekap-harian', [LaporanController::class, 'rekapHarianPenerimaan'])
        ->name('print.rekap.harian');

    // --- TAMBAHKAN RUTE BARU INI ---
    // Rute untuk Laporan Transaksi (Daftar)
    Route::get('/print/laporan-transaksi', [LaporanController::class, 'laporanTransaksi'])
        ->name('print.laporan.transaksi');

    // --- TAMBAHKAN RUTE BARU INI ---
    Route::get('/print/register-sts', [LaporanController::class, 'printRegisterSts'])
        ->name('print.register.sts');

    // Rute untuk cetak STS
    Route::get('/print/sts/register/{sts}', [StsPrintController::class, 'print'])
        ->name('print.sts.register') // <-- Nama route Anda
        ->middleware('auth');

});