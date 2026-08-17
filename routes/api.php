<?php

use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\AuthController; // <-- Tambahkan AuthController
use App\Http\Controllers\Api\BarangController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\KategoriController; 
use App\Http\Controllers\Api\PenerimaanController;
use App\Http\Controllers\Api\PenggunaanController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SatuanController;
use App\Http\Controllers\Api\StockMovementController;
use App\Http\Controllers\Api\StokOpnameController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\StokController;
Use App\Http\Controllers\Api\StokMutasiController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Tanpa Auth / Tidak Perlu Token)
|--------------------------------------------------------------------------
*/
Route::post('login', [AuthController::class, 'login'])->name('login');


/*
|--------------------------------------------------------------------------
| Protected Routes (Wajib Login / Perlu Token Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function (): void {
    
    // Auth Management
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']); // (Opsional) Cek profile user login

    // Dashboard
    Route::get('dashboard', DashboardController::class);

    Route::put('penerimaan/{id}/confirm', [PenerimaanController::class, 'confirmReceipt']);
    
    Route::get('/stok-mutasi', [App\Http\Controllers\Api\StokMutasiController::class, 'index']);
    Route::get('/stock-movements', [StockMovementController::class, 'index']);
    
    // Read-only untuk semua user terautentikasi (Admin & Staff)
    Route::apiResource('penerimaan', PenerimaanController::class)->only(['index', 'show']);
    Route::apiResource('penggunaan', PenggunaanController::class)->only(['index', 'show']);
    Route::apiResource('kategori', KategoriController::class)->only(['index', 'show']); 
    Route::apiResource('satuan', SatuanController::class)->only(['index', 'show']);
    Route::apiResource('barang', BarangController::class)->only(['index', 'show']);
    Route::apiResource('supplier', SupplierController::class)->only(['index', 'show']);
    Route::apiResource('stok-opname', StokOpnameController::class)->only(['index', 'show']);
    Route::apiResource('stock-movements', StockMovementController::class)->only(['index', 'show']);

    /*
    |--------------------------------------------------------------------------
    | Admin Only Routes (Khusus Admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware(EnsureAdmin::class)->group(function (): void {
        // CRUD Penuh / Transaksi
        Route::apiResource('penerimaan', PenerimaanController::class)->except(['index', 'show']);
        Route::apiResource('penggunaan', PenggunaanController::class)->except(['index', 'show']);
        Route::apiResource('kategori', KategoriController::class)->except(['index', 'show']); 
        Route::apiResource('satuan', SatuanController::class)->except(['index', 'show']);
        Route::apiResource('barang', BarangController::class)->except(['index', 'show']);
        Route::apiResource('supplier', SupplierController::class)->except(['index', 'show']);
        Route::apiResource('stok-opname', StokOpnameController::class)->only(['store']);
        Route::get('laporan/export', [LaporanController::class, 'export']);
        // Audit Logs & User Management
        Route::apiResource('audit-logs', AuditLogController::class)->only(['index', 'show']);
        Route::get('roles', [RoleController::class, 'index']);
        Route::apiResource('users', UserController::class);
    });
});