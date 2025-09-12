<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QRController;

Route::get('/', function () {
    return view('welcome');
});

// QR Code routes
Route::get('/qr', [QRController::class, 'index'])->name('qr.index');
Route::post('/qr/upload', [QRController::class, 'uploadExcel'])->name('qr.upload');
Route::get('/qr/list', [QRController::class, 'listStudents'])->name('qr.list');
Route::get('/qr/download/{id}', [QRController::class, 'downloadQR'])->name('qr.download');
Route::get('/qr/download-all', [QRController::class, 'downloadAllQR'])->name('qr.download-all');
