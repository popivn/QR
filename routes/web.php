<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QRController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QRScannerController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Debug routes removed for cleaner production code

// Authentication routes - sử dụng middleware riêng cho auth
Route::middleware(['auth-ngrok'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});


// Public routes - accessible to everyone
Route::get('/qr/statistics/{groupId?}', [QRScannerController::class, 'statistics'])->name('qr.statistics');
Route::get('/qr/leaderboard', [QRScannerController::class, 'leaderboard'])->name('qr.leaderboard');
Route::get('/qr/api/statistics/{groupId}', [QRScannerController::class, 'getStatistics'])->name('qr.api.statistics');

// Group routes (for all authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/group', [GroupController::class, 'index'])->name('group.index');
    
    // QR Scanner routes (for all authenticated users)
    Route::get('/qr/scanner', [QRScannerController::class, 'index'])->name('qr.scanner');
    Route::post('/qr/scan', [QRScannerController::class, 'scan'])->name('qr.scan');
    Route::post('/qr/scan-image', [QRScannerController::class, 'scanImage'])->name('qr.scan-image');
});

// Admin only routes (role_id = 1)
Route::middleware(['auth', 'role:admin'])->group(function () {
    // QR Code routes
    Route::get('/qr', [QRController::class, 'index'])->name('qr.index');
    Route::post('/qr/upload', [QRController::class, 'uploadExcel'])->name('qr.upload');
    Route::get('/qr/list', [QRController::class, 'listStudents'])->name('qr.list');
    Route::get('/qr/download/{id}', [QRController::class, 'downloadQR'])->name('qr.download');
    Route::get('/qr/download-all', [QRController::class, 'downloadAllQR'])->name('qr.download-all');
    
    // Group management routes - đặt route cụ thể trước route có parameter
    Route::get('/group/users', [GroupController::class, 'listUsers'])->name('group.users');
    Route::get('/group/create', [GroupController::class, 'create'])->name('group.create');
    Route::post('/group', [GroupController::class, 'store'])->name('group.store');
    Route::get('/group/list', [GroupController::class, 'listGroups'])->name('group.list');
    Route::post('/group/add-user', [GroupController::class, 'addUser'])->name('group.add-user');
    Route::delete('/group/remove-user/{userId}', [GroupController::class, 'removeUser'])->name('group.remove-user');
    
    // Routes với parameter phải đặt cuối
    Route::get('/group/{id}', [GroupController::class, 'show'])->name('group.show');
    Route::get('/group/{id}/edit', [GroupController::class, 'edit'])->name('group.edit');
    Route::put('/group/{id}', [GroupController::class, 'update'])->name('group.update');
    Route::delete('/group/{id}', [GroupController::class, 'destroy'])->name('group.destroy');
});
