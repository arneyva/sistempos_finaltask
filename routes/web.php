<?php

use App\Http\Controllers\Adjustment\AdjustmentController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\expense\ExpenseCategoryController;
use App\Http\Controllers\expense\ExpenseController;
use App\Http\Controllers\hrm\OfficeShiftController;
use App\Http\Controllers\hrm\ClockController;
use App\Http\Controllers\hrm\MyAttendanceController;
use App\Http\Controllers\people\ClientController;
use App\Http\Controllers\people\ProviderController;
use App\Http\Controllers\Product\BrandController;
use App\Http\Controllers\Product\CategoryController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\UnitController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\purchase\PurchaseController;
use App\Http\Controllers\pos\PosController;
use App\Http\Controllers\Reports\ReportsController;
use App\Http\Controllers\Sale\SaleController;
use App\Http\Controllers\Sale\SaleReturnController;
use App\Http\Controllers\Sale\ShipmentController;
use App\Http\Controllers\Settings\CurrencyController;
use App\Http\Controllers\Settings\MembershipController;
use App\Http\Controllers\Settings\WarehousesController;
use App\Http\Controllers\Transfer\TransferController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Settings\CompanyController;
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

/*
|--------------------------------------------------------------------------
| Universal SmartClock has clock-in and clock-out functions 
|--------------------------------------------------------------------------
*/
Route::get('webclock', [ClockController::class, 'index']);
Route::post('webclock/clocking', [ClockController::class, 'clocking'])->name('webclock.clocking');
Route::post('/update-alert-stock', [ProductController::class, 'updateAlertStock'])->name('updateAlertStock');
/*
|--------------------------------------------------------------------------
| Landing page from email 
|--------------------------------------------------------------------------
*/
Route::get('purchases/receipt/edit/{Ref}', [PurchaseController::class, 'editSupplier'])->name('edit.supplier')->middleware('signed');
Route::patch('purchases/receipt/update/{id}', [PurchaseController::class, 'updateSupplier'])->name('update.supplier')->middleware('signed');

Route::prefix('/product')->middleware(['auth', 'verified'])->name('product.')->group(function () {
    Route::get('/list', [ProductController::class, 'index'])->name('index');
    Route::get('/detail/{id}', [ProductController::class, 'show'])->name('show');
    Route::get('/create', [ProductController::class, 'create'])->name('create');
    Route::post('/store', [ProductController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [ProductController::class, 'edit'])->name('edit');
    Route::patch('/update/{id}', [ProductController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [ProductController::class, 'destroy'])->name('destroy');
    Route::get('export', [ProductController::class, 'export'])->name('export');
    Route::get('pdf', [ProductController::class, 'exportToPDF'])->name('pdf');
    Route::post('import/csv', [ProductController::class, 'import_products'])->name('import');
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
    Route::get('get_Available_Products_by_warehouse/{id}', [AdjustmentController::class, 'Avilable_Products_by_Warehouse'])->name('get_Available_Warehouses');
    Route::get('Sale_get_Available_Products_by_warehouse/{id}', [AdjustmentController::class, 'Sale_Avilable_Products_by_Warehouse'])->name('Sale_get_Available_Warehouses');
    Route::get('show_product_data/{id}/{variant_id}/{warehouse_id}', [AdjustmentController::class, 'show_product_data']);
    //
    Route::get('export', [AdjustmentController::class, 'export'])->name('export');
    Route::get('pdf', [AdjustmentController::class, 'exportToPDF'])->name('pdf');
});
Route::prefix('transfer')->middleware(['auth', 'verified'])->name('transfer.')->group(function () {
    Route::get('list', [TransferController::class, 'index'])->name('index');
    Route::get('detail/{id}', [TransferController::class, 'show'])->name('show');
    Route::get('create', [TransferController::class, 'create'])->name('create');
    Route::post('store', [TransferController::class, 'store'])->name('store');
    Route::get('edit/{id}', [TransferController::class, 'edit'])->name('edit');
    Route::patch('update/{id}', [TransferController::class, 'update'])->name('update');
    Route::patch('confirm/{id}', [TransferController::class, 'updateForStaff'])->name('confirm');
    Route::delete('destroy/{id}', [TransferController::class, 'destroy'])->name('destroy');
    Route::get('export', [TransferController::class, 'export'])->name('export');
    Route::get('pdf', [TransferController::class, 'exportToPDF'])->name('pdf');
});
Route::get('/sale/payment/success/{transaction}', [SaleController::class, 'success'])->name('sale.payment.success');
Route::prefix('sale')->middleware(['auth', 'verified'])->name('sale.')->group(function () {
    Route::get('print-invoice/{id}', [SaleController::class, 'printInvoice'])->name('print-invoice');
    Route::get('list', [SaleController::class, 'index'])->name('index');
    Route::get('shipments', [ShipmentController::class, 'index'])->name('shipments');
    Route::get('shipments-export', [ShipmentController::class, 'shipmentsExport'])->name('shipments-export');
    Route::get('detail/{id}', [SaleController::class, 'show'])->name('show');
    Route::get('create', [SaleController::class, 'create'])->name('create');
    Route::post('store', [SaleController::class, 'store'])->name('store');
    Route::get('edit/{id}', [SaleController::class, 'edit'])->name('edit');
    Route::patch('update/{id}', [SaleController::class, 'update'])->name('update');
    Route::delete('destroy/{id}', [SaleController::class, 'destroy'])->name('destroy');
    //
    Route::get('get_Products_by_warehouse/{id}', [AdjustmentController::class, 'Products_by_Warehouse'])->name('get_Warehouses');
    Route::get('show_product_data/{id}/{variant_id}/{warehouse_id}', [AdjustmentController::class, 'show_product_data']);
    Route::get('get_payments_by_sale/{id}', [SaleController::class, 'Payments_Sale'])->name('get_payments_by_sale');
    Route::get('export', [SaleController::class, 'export'])->name('export');
    Route::get('pdf', [SaleController::class, 'exportToPDF'])->name('pdf');
    //
    Route::prefix('/shipment')->name('shipment.')->group(function () {
        Route::post('store', [ShipmentController::class, 'store'])->name('store');
    });
    Route::prefix('/return')->name('return.')->group(function () {
        Route::get('list', [SaleReturnController::class, 'index'])->name('index');
        Route::get('create/{id}', [SaleReturnController::class, 'create_sell_return'])->name('create');
        Route::post('store', [SaleReturnController::class, 'store'])->name('store');
        Route::get('export', [SaleReturnController::class, 'export'])->name('export');
        Route::get('detail/{id}', [SaleReturnController::class, 'show'])->name('show');
    });
});
Route::prefix('reports')->middleware(['auth', 'verified'])->name('reports.')->group(function () {
    Route::prefix('/payments')->name('payments.')->group(function () {
        Route::get('sales', [ReportsController::class, 'payments'])->name('sales');
        Route::get('sales/export', [ReportsController::class, 'exportPaymentSale'])->name('sales-export');
        Route::get('sales-returns', [ReportsController::class, 'paymentSaleReturns'])->name('sales-returns');
        Route::get('sales-returns/export', [ReportsController::class, 'exportPaymentReturnSale'])->name('sales-returns-export');
        Route::get('purchases', [ReportsController::class, 'paymentPurchases'])->name('purchases')->middleware('role:superadmin|inventaris');
        Route::get('purchases/export', [ReportsController::class, 'exportPaymentPurchases'])->name('purchases-export');
        Route::get('purchases-returns', [ReportsController::class, 'paymentPurchaseReturns'])->name('purchases-returns');
        Route::get('purchases-returns/export', [ReportsController::class, 'exportPaymentPurchasesReturn'])->name('purchases-returns-export');
    });
    Route::get('profit-loss', [ReportsController::class, 'profitLoss'])->name('profit-loss');
    Route::get('quantity-alerts', [ReportsController::class, 'quantityAlerts'])->name('quantity-alerts');
    Route::get('export-quantity-alerts', [ReportsController::class, 'ReportProductAlerts'])->name('export-quantity-alerts');
    Route::prefix('/stock')->name('stock.')->group(function () {
        Route::get('list', [ReportsController::class, 'stock'])->name('index');
        Route::get('export-product-stock', [ReportsController::class, 'exportReportStock'])->name('export-product-stock');
        Route::get('sales/{id}', [ReportsController::class, 'stockDetailSales'])->name('sales');
        Route::get('sales-export/{id}', [ReportsController::class, 'exportstockSales'])->name('sales-export');
        Route::get('sales-returns/{id}', [ReportsController::class, 'stockDetailSalesReturn'])->name('sales-returns');
        Route::get('sales-returns-export/{id}', [ReportsController::class, 'exportstockSalesReturn'])->name('sales-returns-export');
        Route::get('adjustment/{id}', [ReportsController::class, 'stockDetailAdjustment'])->name('adjustment');
        Route::get('adjustment-export/{id}', [ReportsController::class, 'exportstockAdjustment'])->name('adjustment-export');
        Route::get('transfer/{id}', [ReportsController::class, 'stockDetailTransfer'])->name('transfer');
        Route::get('transfer-export/{id}', [ReportsController::class, 'exportstockTransfer'])->name('transfer-export');
        Route::get('purchases/{id}', [ReportsController::class, 'stockDetailPurchases'])->name('purchases');
        Route::get('purchases-returns/{id}', [ReportsController::class, 'stockDetailPurchasesReturn'])->name('purchases-returns');
    });
    Route::get('stock/{id}', [ReportsController::class, 'stockDetail'])->name('stock-detail');
    Route::prefix('/customers')->name('customers.')->group(function () {
        Route::get('list', [ReportsController::class, 'customers'])->name('index');
        Route::get('sales/{id}', [ReportsController::class, 'customersDetailSales'])->name('sales');
        Route::get('sales/export/{id}', [ReportsController::class, 'customersDetailSalesExport'])->name('sales-export');
        Route::get('returns/{id}', [ReportsController::class, 'customersDetailReturns'])->name('returns');
        Route::get('returns/export/{id}', [ReportsController::class, 'customersDetailSalesReturnsExport'])->name('returns-export');
        Route::get('payments/{id}', [ReportsController::class, 'customersDetailPayments'])->name('payments');
        Route::get('payments/export/{id}', [ReportsController::class, 'customersDetailSalesPaymentExport'])->name('payments-export');
    });
    Route::prefix('/supplier')->name('supplier.')->middleware('role:superadmin|inventaris')->group(function () {
        Route::get('list', [ReportsController::class, 'supplier'])->name('index');
        Route::get('purchases/{id}', [ReportsController::class, 'Purchases_Provider'])->name('purchases');
        Route::get('purchases/export/{id}', [ReportsController::class, 'providerDetailPurchasesExport'])->name('purchases-export');
        Route::get('returns/{id}', [ReportsController::class, 'Returns_Provider'])->name('returns');
        Route::get('returns/export/{id}', [ReportsController::class, 'providerDetailPurchasesReturnsExport'])->name('returns-export');
        Route::get('payments/{id}', [ReportsController::class, 'Payments_Provider'])->name('payments');
        Route::get('payments/export/{id}', [ReportsController::class, 'providerDetailPaymentExport'])->name('payments-export');
    });
    Route::get('top-selling-product', [ReportsController::class, 'topSellingProduct'])->name('top-selling-product');
    Route::get('top-selling-product/export', [ReportsController::class, 'topSellingProductExport'])->name('top-selling-product-export');
    Route::prefix('/warehouse')->name('warehouse.')->middleware('role:superadmin|inventaris')->group(function () {
        Route::get('sales', [ReportsController::class, 'warehouseSales'])->name('sales');
        Route::get('export-sales', [ReportsController::class, 'exportwarehouseSales'])->name('export-sales');
        Route::get('expenses', [ReportsController::class, 'warehouseExpenses'])->name('expenses');
        Route::get('export-expenses', [ReportsController::class, 'exportwarehouseExpenses'])->name('export-expenses');
        Route::get('sales-returns', [ReportsController::class, 'warehouseSalesReturns'])->name('sales-returns');
        Route::get('export-sales-returns', [ReportsController::class, 'exportwarehouseSalesReturns'])->name('export-sales-returns');
        Route::get('purchase', [ReportsController::class, 'warehousePurchase'])->name('purchase');
        Route::get('purchase-returns', [ReportsController::class, 'warehousePurchaseReturns'])->name('purchase-returns');
        Route::get('export-purchase-returns', [ReportsController::class, 'exportwarehousePurchaseReturns'])->name('export-purchase-returns');
    });
    Route::get('sale', [ReportsController::class, 'sale'])->name('sale');
    Route::get('sale-export', [ReportsController::class, 'saleExport'])->name('sale-export');
    Route::get('purchase', [ReportsController::class, 'purchase'])->name('purchase')->middleware('role:superadmin|inventaris');
    Route::get('purchase-export', [ReportsController::class, 'exportReportPurchase'])->name('purchase-export');
});
Route::prefix('settings')->middleware(['auth', 'verified'])->name('settings.')->group(function () {
    Route::patch('company/update/{id}', [CompanyController::class, 'update'])->name('company.update');
    Route::get('company/edit', [CompanyController::class, 'edit'])->name('company.edit');
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
    Route::prefix('membership')->name('membership.')->group(function () {
        Route::get('list', [MembershipController::class, 'index'])->name('index');
        Route::patch('update/{id}', [MembershipController::class, 'update'])->name('update');
    });
});
Route::prefix('people')->middleware(['auth', 'verified'])->name('people.')->group(function () {
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('list', [UserController::class, 'index'])->name('index');
        Route::get('create', [UserController::class, 'create'])->name('create');
        Route::post('store', [UserController::class, 'store'])->name('store');
        Route::get('detail/{id}', [UserController::class, 'show'])->name('show');
        Route::patch('update/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('destroy/{id}', [UserController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('list', [ProviderController::class, 'index'])->name('index');
        Route::get('create', [ProviderController::class, 'create'])->name('create');
        Route::post('store', [ProviderController::class, 'store'])->name('store');
        Route::get('detail/{id}', [ProviderController::class, 'show'])->name('show');
        Route::patch('update/{id}', [ProviderController::class, 'update'])->name('update');
        Route::delete('destroy/{id}', [ProviderController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('clients')->name('clients.')->group(function () {
        Route::get('list', [ClientController::class, 'index'])->name('index');
        Route::post('store', [ClientController::class, 'store'])->name('store');
        Route::patch('update/{id}', [ClientController::class, 'update'])->name('update');
        Route::delete('destroy/{id}', [ClientController::class, 'destroy'])->name('destroy');
    });
});
Route::prefix('expenses')->middleware(['auth', 'verified'])->name('expenses.')->group(function () {
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('list', [ExpenseCategoryController::class, 'index'])->name('index');
        Route::post('store', [ExpenseCategoryController::class, 'store'])->name('store');
        Route::patch('update/{id}', [ExpenseCategoryController::class, 'update'])->name('update');
        Route::delete('destroy/{id}', [ExpenseCategoryController::class, 'destroy'])->name('destroy');
    });
    Route::get('list', [ExpenseController::class, 'index'])->name('index');
    Route::get('detail/{id}', [ExpenseController::class, 'show'])->name('show');
    Route::get('create', [ExpenseController::class, 'create'])->name('create');
    Route::post('store', [ExpenseController::class, 'store'])->name('store');
    Route::get('edit/{id}', [ExpenseController::class, 'edit'])->name('edit');
    Route::patch('update/{id}', [ExpenseController::class, 'update'])->name('update');
    Route::delete('destroy/{id}', [ExpenseController::class, 'destroy'])->name('destroy');
    Route::get('file/download/{id}', [ExpenseController::class, 'download'])->name('file');
});
Route::prefix('hrm')->middleware(['auth', 'verified'])->name('hrm.')->group(function () {
    Route::prefix('shifts')->name('shifts.')->group(function () {
        Route::get('list', [OfficeShiftController::class, 'index'])->name('index');
        Route::get('create', [OfficeShiftController::class, 'create'])->name('create');
        Route::post('store', [OfficeShiftController::class, 'store'])->name('store');
        Route::get('detail/{id}', [OfficeShiftController::class, 'show'])->name('show');
        Route::patch('update/{id}', [OfficeShiftController::class, 'update'])->name('update');
        Route::delete('destroy/{id}', [OfficeShiftController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('myattendances')->name('myattendances.')->group(function () {
        Route::get('list', [MyAttendanceController::class, 'index'])->name('index');
        Route::post('list', [MyAttendanceController::class, 'checkAttendance'])->name('check');
    });
});
Route::prefix('purchases')->middleware(['auth', 'verified'])->name('purchases.')->group(function () {
    Route::get('list', [PurchaseController::class, 'index'])->name('index');
    Route::get('create', [PurchaseController::class, 'create'])->name('create');
    Route::post('store', [PurchaseController::class, 'store'])->name('store');
    Route::get('detail/{id}', [PurchaseController::class, 'show'])->name('show');
    Route::get('edit/{id}', [PurchaseController::class, 'edit'])->name('edit');
    Route::patch('update/{id}', [PurchaseController::class, 'update'])->name('update');
    Route::delete('destroy/{id}', [PurchaseController::class, 'destroy'])->name('destroy');
    Route::post('scanner/{code}', [PurchaseController::class, 'getFromScanner']);
    Route::post('supplier/{id}', [PurchaseController::class, 'getSupplier']);
});

Route::get('cashier', [PosController::class, 'create'])->middleware(['auth', 'verified']);
Route::prefix('cashier')->middleware(['auth', 'verified'])->name('cashier.')->group(function () {
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware('auth')->group(function () {
    Route::get('lang/{locale}', [LanguageController::class, 'lang']);
});

require __DIR__ . '/auth.php';
