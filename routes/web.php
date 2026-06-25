<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DamagedController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\CategoryController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Role: operator & admin
    Route::middleware('role:operator,admin')->group(function () {
        // Resource controllers
        Route::resource('suppliers', SupplierController::class);
        Route::resource('products', ProductController::class);
        Route::resource('deliveries', DeliveryController::class);
        Route::resource('sales', SaleController::class);
        Route::resource('cash', CashController::class);
        Route::resource('damaged', DamagedController::class);

        // Custom routes for supplier-product many-to-many
        Route::post('/suppliers/{supplier}/products', [SupplierController::class, 'attachProduct'])
            ->name('suppliers.attachProduct');
        Route::delete('/suppliers/{supplier}/products/{product}', [SupplierController::class, 'detachProduct'])
            ->name('suppliers.detachProduct');

        // Payment routes
        Route::get('payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('payments/store', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('payments/print/{id}', [PaymentController::class, 'printNota'])->name('payments.print');
        Route::get('/payments/history', [PaymentController::class, 'history'])->name('payments.history');
        Route::delete('/payments/{id}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    });

    // Role khusus admin
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        // Route::get('backup', [BackupController::class, 'index'])->name('backup.index');
        // Route::post('backup/create', [BackupController::class, 'create'])->name('backup.create');
        // Route::get('backup/download/{id}', [BackupController::class, 'download'])->name('backup.download');
    });

    // Laporan (semua role bisa lihat)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('daily', [ReportController::class, 'daily'])->name('daily');
        Route::get('monthly', [ReportController::class, 'monthly'])->name('monthly');
        Route::get('yearly', [ReportController::class, 'yearly'])->name('yearly');
    });

    Route::get(
    '/deliveries/get-products/{supplier_id}',
    [DeliveryController::class, 'getProductsBySupplier']
)->name('deliveries.get-products');

Route::get('/products/{id}/get-prices', function ($id) {
    $product = \App\Models\Product::find($id);
    return response()->json([
        'harga_beli' => $product->harga_beli ?? 0,
        'harga_jual' => $product->harga_jual ?? 0,
    ]);
})->name('products.get-prices');

Route::resource('units', UnitController::class);
Route::resource('categories', CategoryController::class);
});
