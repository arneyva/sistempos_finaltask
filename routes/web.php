<?php

use App\Http\Controllers\Adjustment\AdjustmentController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Product\BrandController;
use App\Http\Controllers\Product\CategoryController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\UnitController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Reports\ReportsController;
use App\Http\Controllers\Sale\SaleController;
use App\Http\Controllers\Settings\CurrencyController;
use App\Http\Controllers\Settings\WarehousesController;
use App\Http\Controllers\Transfer\TransferController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// Route::get('/', function () {
//     return view('templates.auth.login');
//     // return view('auth.login');
// });
Route::get('/', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::prefix('/product')->middleware(['auth', 'verified'])->name('product.')->group(function () {
    Route::get('/list', [ProductController::class, 'index'])->name('index');
    Route::get('/detail/{id}', [ProductController::class, 'show'])->name('show');
    Route::get('/create', [ProductController::class, 'create'])->name('create');
    Route::post('/store', [ProductController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [ProductController::class, 'edit'])->name('edit');
    Route::patch('/update/{id}', [ProductController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [ProductController::class, 'destroy'])->name('destroy');
    // catgeory
    Route::prefix('/category')->name('category.')->group(function () {
        Route::get('/list', [CategoryController::class, 'index'])->name('index');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::delete('/destroy/{id}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::put('/update/{id}', [CategoryController::class, 'update'])->name('update');
    });
    // brand
    Route::prefix('/brand')->name('brand.')->group(function () {
        Route::get('/list', [BrandController::class, 'index'])->name('index');
        Route::post('/store', [BrandController::class, 'store'])->name('store');
        Route::delete('/destroy/{id}', [BrandController::class, 'destroy'])->name('destroy');
        Route::put('/update/{id}', [BrandController::class, 'update'])->name('update');
    });
    // unit
    Route::prefix('/unit')->name('unit.')->group(function () {
        Route::get('/list', [UnitController::class, 'index'])->name('index');
        Route::post('store', [UnitController::class, 'store'])->name('store');
        Route::put('update/{id}', [UnitController::class, 'update'])->name('update');
        Route::delete('destroy/{id}', [UnitController::class, 'destroy'])->name('destroy');
    });
});
Route::get('/product/get-units/{id}', [ProductController::class, 'getUnits'])->name('product.getUnits');

Route::prefix('adjustment')->middleware(['auth', 'verified'])->name('adjustment.')->group(function () {
    Route::get('list', [AdjustmentController::class, 'index'])->name('index');
    Route::get('detail/{id}', [AdjustmentController::class, 'show'])->name('show');
    Route::get('create', [AdjustmentController::class, 'create'])->name('create');
    Route::post('store', [AdjustmentController::class, 'store'])->name('store');
    Route::get('edit/{id}', [AdjustmentController::class, 'edit'])->name('edit');
    Route::patch('update/{id}', [AdjustmentController::class, 'update'])->name('update');
    Route::delete('destroy/{id}', [AdjustmentController::class, 'destroy'])->name('destroy');
    //
    Route::get('get_Products_by_warehouse/{id}', [AdjustmentController::class, 'Products_by_Warehouse'])->name('get_Warehouses');
    Route::get('show_product_data/{id}/{variant_id}/{warehouse_id}', [AdjustmentController::class, 'show_product_data']);
});
Route::prefix('transfer')->middleware(['auth', 'verified'])->name('transfer.')->group(function () {
    Route::get('list', [TransferController::class, 'index'])->name('index');
    Route::get('detail/{id}', [TransferController::class, 'show'])->name('show');
    Route::get('create', [TransferController::class, 'create'])->name('create');
    Route::post('store', [TransferController::class, 'store'])->name('store');
    Route::get('edit/{id}', [TransferController::class, 'edit'])->name('edit');
    Route::patch('update/{id}', [TransferController::class, 'update'])->name('update');
    Route::delete('destroy/{id}', [TransferController::class, 'destroy'])->name('destroy');
});
Route::prefix('sale')->middleware(['auth', 'verified'])->name('sale.')->group(function () {
    Route::get('list', [SaleController::class, 'index'])->name('index');
    Route::get('shipments', [SaleController::class, 'shipments'])->name('shipments');
    Route::get('detail/{id}', [SaleController::class, 'show'])->name('show');
    Route::get('create', [SaleController::class, 'create'])->name('create');
    Route::post('store', [SaleController::class, 'store'])->name('store');
    Route::get('edit/{id}', [SaleController::class, 'edit'])->name('edit');
    Route::patch('update/{id}', [SaleController::class, 'update'])->name('update');
    Route::delete('destroy/{id}', [SaleController::class, 'destroy'])->name('destroy');
    //
    Route::get('get_Products_by_warehouse/{id}', [AdjustmentController::class, 'Products_by_Warehouse'])->name('get_Warehouses');
    Route::get('show_product_data/{id}/{variant_id}/{warehouse_id}', [AdjustmentController::class, 'show_product_data']);
});
Route::prefix('reports')->middleware(['auth', 'verified'])->name('reports.')->group(function () {
    Route::get('payments', [ReportsController::class, 'payments'])->name('payments');
    Route::get('profit-loss', [ReportsController::class, 'profitLoss'])->name('profit-loss');
    Route::get('quantity-alerts', [ReportsController::class, 'quantityAlerts'])->name('quantity-alerts');
    Route::get('stock', [ReportsController::class, 'stock'])->name('stock');
    Route::get('stock/{id}', [ReportsController::class, 'stockDetail'])->name('stock-detail');
    Route::get('customers', [ReportsController::class, 'customers'])->name('customers');
    Route::get('customers/{id}', [ReportsController::class, 'customersDetail'])->name('customers-detail');
    Route::get('supplier', [ReportsController::class, 'supplier'])->name('supplier');
    Route::get('supplier/{id}', [ReportsController::class, 'supplierDetail'])->name('supplier-detail');
    Route::get('top-selling-product', [ReportsController::class, 'topSellingProduct'])->name('top-selling-product');
    Route::get('warehouse', [ReportsController::class, 'warehouse'])->name('warehouse');
});
Route::prefix('settings')->middleware(['auth', 'verified'])->name('settings.')->group(function () {
    Route::prefix('warehouses')->name('warehouses.')->group(function () {
        Route::get('list', [WarehousesController::class, 'index'])->name('index');
        Route::get('detail/{id}', [WarehousesController::class, 'show'])->name('show');
        Route::get('create', [WarehousesController::class, 'create'])->name('create');
        Route::post('store', [WarehousesController::class, 'store'])->name('store');
        Route::get('edit/{id}', [WarehousesController::class, 'edit'])->name('edit');
        Route::patch('update/{id}', [WarehousesController::class, 'update'])->name('update');
        Route::delete('destroy/{id}', [WarehousesController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('currency')->name('currency.')->group(function () {
        Route::get('list', [CurrencyController::class, 'index'])->name('index');
        Route::get('detail/{id}', [CurrencyController::class, 'show'])->name('show');
        Route::get('create', [CurrencyController::class, 'create'])->name('create');
        Route::post('store', [CurrencyController::class, 'store'])->name('store');
        Route::get('edit/{id}', [CurrencyController::class, 'edit'])->name('edit');
        Route::patch('update/{id}', [CurrencyController::class, 'update'])->name('update');
        Route::delete('destroy/{id}', [CurrencyController::class, 'destroy'])->name('destroy');
    });
});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
