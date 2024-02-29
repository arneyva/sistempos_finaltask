<?php

use App\Http\Controllers\Adjustment\AdjustmentController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Product\BrandController;
use App\Http\Controllers\Product\CategoryController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\UnitController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Settings\WarehousesController;
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
    Route::prefix('/category')->name('category.')->group(function () {
        Route::get('/list', [CategoryController::class, 'index'])->name('index');
    });
    Route::prefix('/brand')->name('brand.')->group(function () {
        Route::get('/list', [BrandController::class, 'index'])->name('index');
    });
    Route::prefix('/unit')->name('unit.')->group(function () {
        Route::get('/list', [UnitController::class, 'index'])->name('index');
    });
});
Route::prefix('adjustment')->middleware(['auth', 'verified'])->name('adjustment.')->group(function () {
    Route::get('list', [AdjustmentController::class, 'index'])->name('index');
    Route::get('detail/{id}', [AdjustmentController::class, 'show'])->name('show');
    Route::get('create', [AdjustmentController::class, 'create'])->name('create');
    Route::post('store', [AdjustmentController::class, 'store'])->name('store');
    Route::get('edit/{id}', [AdjustmentController::class, 'edit'])->name('edit');
    Route::patch('update/{id}', [AdjustmentController::class, 'update'])->name('update');
    Route::delete('destroy/{id}', [AdjustmentController::class, 'destroy'])->name('destroy');
});
Route::prefix('settings')->middleware(['auth', 'verified'])->name('settings.')->group(function () {
    Route::prefix('warehouses')->name('warehouses.')->group(function () {
        Route::get('list', [WarehousesController::class, 'index'])->name('index');
        Route::get('detail/{id}', [AdjustmentController::class, 'show'])->name('show');
        Route::get('create', [AdjustmentController::class, 'create'])->name('create');
        Route::post('store', [AdjustmentController::class, 'store'])->name('store');
        Route::get('edit/{id}', [AdjustmentController::class, 'edit'])->name('edit');
        Route::patch('update/{id}', [AdjustmentController::class, 'update'])->name('update');
        Route::delete('destroy/{id}', [AdjustmentController::class, 'destroy'])->name('destroy');
    });
});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
