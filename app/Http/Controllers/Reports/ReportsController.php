<?php

namespace App\Http\Controllers\Reports;

use App\Exports\Report\Customer\ReportCustomerSalesExport;
use App\Exports\Report\Customer\ReportCustomerSalesPaymentExport;
use App\Exports\Report\Customer\ReportCustomerSalesReturnExport;
use App\Exports\Report\Customer\SalesExport;
use App\Exports\Report\Payment\ReportPaymentPurchasesExport;
use App\Exports\Report\Payment\ReportPaymentPurchasesReturnExport;
use App\Exports\Report\Payment\ReportPaymentSalesExport;
use App\Exports\Report\Payment\ReportPaymentSalesReturnExport;
use App\Exports\Report\ProfitLossExport;
use App\Exports\Report\Provider\ReportProviderPurchasesExport;
use App\Exports\Report\Provider\ReportProviderPurchasesPaymentExport;
use App\Exports\Report\Provider\ReportProviderPurchasesReturnExport;
use App\Exports\Report\ReportProductAlerts;
use App\Exports\Report\ReportPurchasesExport;
use App\Exports\Report\ReportQuantityAlert;
use App\Exports\Report\ReportSalesExport;
use App\Exports\Report\Stock\ReportAdjustmentStock;
use App\Exports\Report\Stock\ReportProductStock;
use App\Exports\Report\Stock\ReportSalesReturnStock;
use App\Exports\Report\Stock\ReportSalesStock;
use App\Exports\Report\Stock\ReportTransferStock;
use App\Exports\Report\TopSellingProductExport;
use App\Exports\Report\Warehouse\ReportExpensesWarehouse as WarehouseReportExpensesWarehouse;
use App\Exports\Report\Warehouse\ReportPurchasesReturnWarehouse;
use App\Exports\Report\Warehouse\ReportSaleReturnWarehouse;
use App\Exports\Report\Warehouse\ReportSaleWarehouse;
use App\Exports\ReportExpensesWarehouse;
use App\Http\Controllers\Controller;
use App\Models\AdjustmentDetail;
use App\Models\Client;
use App\Models\Expense;
use App\Models\PaymentPurchase;
use App\Models\PaymentPurchaseReturns;
use App\Models\PaymentSale;
use App\Models\PaymentSaleReturns;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductWarehouse;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetails;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\SaleReturn;
use App\Models\SaleReturnDetails;
use App\Models\TransferDetail;
use App\Models\Unit;
use App\Models\UserWarehouse;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportsController extends Controller
{
    public function payments(Request $request)
    {
        $user_auth = auth()->user();
        $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');
        // mendapatkan data payment
        $paymentsQuery = DB::table('payment_sales')
            ->where('payment_sales.deleted_at', '=', null)
            ->join('sales', 'payment_sales.sale_id', '=', 'sales.id')
            ->join('clients', 'sales.client_id', '=', 'clients.id')
            ->latest('payment_sales.date');
        if ($user_auth->hasRole(['superadmin', 'inventaris'])) {
            $paymentsQuery = $paymentsQuery;
        } else {
            $paymentsQuery = $paymentsQuery->whereIn('sales.warehouse_id', $warehouses_id);
        }
        // proses filtering
        if ($request->filled('search')) {
            $paymentsQuery->where(function ($query) use ($request) {
                $query->orWhere('payment_sales.Ref', 'LIKE', '%' . $request->input('search') . '%')
                    ->orWhere('clients.name', 'LIKE', '%' . $request->input('search') . '%')
                    ->orWhere('payment_sales.Reglement', 'LIKE', '%' . $request->input('search') . '%');
            });
        }
        // mengambil data yang sudah difilter dan dipaginasi
        $payments = $paymentsQuery->select(
            'payment_sales.date',
            'payment_sales.Ref AS Payment_Ref',
            'sales.Ref AS Sale_Ref',
            'payment_sales.Reglement',
            'payment_sales.montant',
            'clients.name AS client_name'
        )->paginate($request->input('limit', 10))->appends($request->except('page'));
        $paymentDetails = [];
        // proses menampilkan data
        foreach ($payments as $payment) {
            $item = [
                'date' => $payment->date,
                'Payment_Ref' => $payment->Payment_Ref,
                'Sale_Ref' => $payment->Sale_Ref,
                'Reglement' => $payment->Reglement,
                'montant' => $payment->montant,
                'client_name' => $payment->client_name,
            ];

            $paymentDetails[] = $item;
        }
        //mengirim data ke frontend
        return view('templates.reports.payments.payments-sale', [
            'payments' => $payments,
            'paymentDetails' => $paymentDetails
        ]);
    }
    public function exportPaymentSale(Request $request)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "report-payments-sales_{$timestamp}.xlsx";

        return Excel::download(new ReportPaymentSalesExport($request), $filename);
    }
    public function exportPaymentReturnSale(Request $request)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "report-payments-sales-return_{$timestamp}.xlsx";

        return Excel::download(new ReportPaymentSalesReturnExport($request), $filename);
    }
    public function paymentSaleReturns(Request $request)
    {
        $user_auth = auth()->user();
        $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');
        $paymentsQuery = DB::table('payment_sale_returns')
            ->where('payment_sale_returns.deleted_at', '=', null)
            ->join('sale_returns', 'payment_sale_returns.sale_return_id', '=', 'sale_returns.id')
            ->join('clients', 'sale_returns.client_id', '=', 'clients.id')
            ->latest('payment_sale_returns.date');
        if (!$user_auth->hasRole(['superadmin', 'inventaris'])) {
            $paymentsQuery->whereIn('sale_returns.warehouse_id', $warehouses_id);
        }
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->input('search') . '%';
            $paymentsQuery->where(function ($query) use ($searchTerm) {
                $query->orWhere('payment_sale_returns.Ref', 'LIKE', $searchTerm)
                    ->orWhere('clients.name', 'LIKE', $searchTerm)
                    ->orWhere('payment_sale_returns.Reglement', 'LIKE', $searchTerm);
            });
        }

        $payments = $paymentsQuery->select(
            'payment_sale_returns.date',
            'payment_sale_returns.Ref AS Payment_Ref',
            'sale_returns.Ref AS Sale_Return_Ref',
            'payment_sale_returns.Reglement',
            'payment_sale_returns.montant',
            'clients.name AS client_name'
        )->paginate($request->input('limit', 10))->appends($request->except('page'));

        $paymentDetails = [];
        foreach ($payments as $payment) {
            $item = [
                'date' => $payment->date,
                'Payment_Ref' => $payment->Payment_Ref,
                'Sale_Return_Ref' => $payment->Sale_Return_Ref,
                'Reglement' => $payment->Reglement,
                'montant' => $payment->montant,
                'client_name' => $payment->client_name,
            ];

            $paymentDetails[] = $item;
        }

        return view('templates.reports.payments.payments-sale-returns', [
            'payments' => $payments,
            'paymentDetails' => $paymentDetails
        ]);
    }
    public function paymentPurchases(Request $request)
    {
        $user_auth = auth()->user();
        $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');
        $paymentsQuery = DB::table('payment_purchases')
            ->whereNull('payment_purchases.deleted_at')
            ->join('purchases', 'payment_purchases.purchase_id', '=', 'purchases.id')
            ->join('providers', 'purchases.provider_id', '=', 'providers.id')
            ->latest('payment_purchases.date');
        if (!$user_auth->hasRole(['superadmin', 'inventaris'])) {
            $paymentsQuery->whereIn('purchases.warehouse_id', $warehouses_id);
        }
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->input('search') . '%';
            $paymentsQuery->where(function ($query) use ($searchTerm) {
                $query->orWhere('payment_purchases.Ref', 'LIKE', $searchTerm)
                    ->orWhere('providers.name', 'LIKE', $searchTerm)
                    ->orWhere('payment_purchases.Reglement', 'LIKE', $searchTerm);
            });
        }
        $payments = $paymentsQuery->select(
            'payment_purchases.date',
            'payment_purchases.Ref AS Payment_Ref',
            'purchases.Ref AS Purchase_Ref',
            'payment_purchases.Reglement',
            'payment_purchases.montant',
            'providers.name AS provider_name'
        )->paginate($request->input('limit', 10))->appends($request->except('page'));

        $paymentDetails = [];
        foreach ($payments as $payment) {
            $item = [
                'date' => $payment->date,
                'Payment_Ref' => $payment->Payment_Ref,
                'Purchase_Ref' => $payment->Purchase_Ref,
                'Reglement' => $payment->Reglement,
                'montant' => $payment->montant,
                'provider_name' => $payment->provider_name,
            ];

            $paymentDetails[] = $item;
        }

        return view('templates.reports.payments.payments-purchase', [
            'payments' => $payments,
            'paymentDetails' => $paymentDetails
        ]);
    }
    public function exportPaymentPurchases(Request $request)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "report-payments-purchases_{$timestamp}.xlsx";

        return Excel::download(new ReportPaymentPurchasesExport($request), $filename);
    }
    public function paymentPurchaseReturns(Request $request)
    {
        $user_auth = auth()->user();
        $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');
        $paymentsQuery = DB::table('payment_purchase_returns')
            ->whereNull('payment_purchase_returns.deleted_at')
            ->join('purchase_returns', 'payment_purchase_returns.purchase_return_id', '=', 'purchase_returns.id')
            ->join('providers', 'purchase_returns.provider_id', '=', 'providers.id')
            ->latest('payment_purchase_returns.date');
        if (!$user_auth->hasRole(['superadmin', 'inventaris'])) {
            $paymentsQuery->whereIn('purchase_returns.warehouse_id', $warehouses_id);
        }
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->input('search') . '%';
            $paymentsQuery->where(function ($query) use ($searchTerm) {
                $query->orWhere('payment_purchase_returns.Ref', 'LIKE', $searchTerm)
                    ->orWhere('providers.name', 'LIKE', $searchTerm);
                // ->orWhere('payment_purchase_returns.Reglement', 'LIKE', $searchTerm);
            });
        }

        $payments = $paymentsQuery->select(
            'payment_purchase_returns.date',
            'payment_purchase_returns.Ref AS Payment_Ref',
            'purchase_returns.Ref AS PurchaseReturn_Ref',
            'payment_purchase_returns.Reglement',
            'payment_purchase_returns.montant',
            'providers.name AS provider_name'
        )->paginate($request->input('limit', 10))->appends($request->except('page'));

        $paymentDetails = [];
        foreach ($payments as $payment) {
            $item = [
                'date' => $payment->date,
                'Payment_Ref' => $payment->Payment_Ref,
                'PurchaseReturn_Ref' => $payment->PurchaseReturn_Ref,
                'Reglement' => $payment->Reglement,
                'montant' => $payment->montant,
                'provider_name' => $payment->provider_name,
            ];

            $paymentDetails[] = $item;
        }

        return view('templates.reports.payments.payments-purchase-returns', [
            'payments' => $payments,
            'paymentDetails' => $paymentDetails
        ]);
    }
    public function exportPaymentPurchasesReturn(Request $request)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "report-payments-purchases-return_{$timestamp}.xlsx";

        return Excel::download(new ReportPaymentPurchasesReturnExport($request), $filename);
    }
    public function profitLoss(request $request)
    {
        // Set default values
        $start_date = $request->input('from', '2024-02-12'); // Default start date
        $end_date = $request->input('to', now()->format('Y-m-d')); // Default end date (today)
        $warehouse_id = $request->input('warehouse_id', 0); // Default warehouse_id
        $user_auth = auth()->user();
        if ($user_auth->hasAnyRole(['superadmin', 'inventaris'])) {
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
            $array_warehouses_id = Warehouse::where('deleted_at', '=', null)->pluck('id')->toArray();
        } else {
            $array_warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
            $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $array_warehouses_id)->get(['id', 'name']);
        }
        if (empty($request->warehouse_id)) {
            $warehouse_id = 0;
        } else {
            $warehouse_id = $request->warehouse_id;
        }

        $data = [];


        //-------------Sale
        $report_total_sales = Sale::where('deleted_at', '=', null)
            ->where('statut', 'completed')
            ->whereBetween('date', array($start_date, $end_date))

            ->where(function ($query) use ($request, $warehouse_id, $array_warehouses_id) {
                if ($warehouse_id !== 0) {
                    return $query->where('warehouse_id', $warehouse_id);
                } else {
                    return $query->whereIn('warehouse_id', $array_warehouses_id);
                }
            })

            ->select(
                DB::raw('SUM(GrandTotal) AS sum'),
                DB::raw("count(*) as nmbr")
            )->first();

        // $item['sales_sum'] =   'Rp ' . number_format($report_total_sales->sum, 2, ',', '.');
        $item['sales_sum'] =   'Rp ' . number_format($report_total_sales->sum, 2, ',', '.');

        $item['sales_count'] =   $report_total_sales->nmbr;


        //--------Purchase
        $report_total_purchases =  Purchase::where('deleted_at', '=', null)
            ->where('statut', 'received')
            ->whereBetween('date', array($start_date, $end_date))

            ->where(function ($query) use ($request, $warehouse_id, $array_warehouses_id) {
                if ($warehouse_id !== 0) {
                    return $query->where('warehouse_id', $warehouse_id);
                } else {
                    return $query->whereIn('warehouse_id', $array_warehouses_id);
                }
            })
            ->select(
                DB::raw('SUM(GrandTotal) AS sum'),
                DB::raw("count(*) as nmbr")
            )->first();

        $item['purchases_sum'] =   'Rp ' . number_format($report_total_purchases->sum, 2, ',', '.');
        $item['purchases_count'] =  $report_total_purchases->nmbr;


        //--------SaleReturn
        $report_total_returns_sales = SaleReturn::where('deleted_at', '=', null)
            ->where('statut', 'received')
            ->whereBetween('date', array($start_date, $end_date))

            ->where(function ($query) use ($request, $warehouse_id, $array_warehouses_id) {
                if ($warehouse_id !== 0) {
                    return $query->where('warehouse_id', $warehouse_id);
                } else {
                    return $query->whereIn('warehouse_id', $array_warehouses_id);
                }
            })

            ->select(
                DB::raw('SUM(GrandTotal) AS sum'),
                DB::raw("count(*) as nmbr")
            )->first();

        $item['returns_sales_sum'] =   'Rp ' . number_format($report_total_returns_sales->sum, 2, ',', '.');
        $item['returns_sales_count'] =   $report_total_returns_sales->nmbr;



        //--------returns_purchases
        $report_total_returns_purchases = PurchaseReturn::where('deleted_at', '=', null)
            ->where('statut', 'completed')
            ->whereBetween('date', array($start_date, $end_date))

            ->where(function ($query) use ($request, $warehouse_id, $array_warehouses_id) {
                if ($warehouse_id !== 0) {
                    return $query->where('warehouse_id', $warehouse_id);
                } else {
                    return $query->whereIn('warehouse_id', $array_warehouses_id);
                }
            })

            ->select(
                DB::raw('SUM(GrandTotal) AS sum'),
                DB::raw("count(*) as nmbr")
            )->first();

        $item['returns_purchases_sum'] =   'Rp ' . number_format($report_total_returns_purchases->sum, 2, ',', '.');
        $item['returns_purchases_count'] =   $report_total_returns_purchases->nmbr;


        //--------paiement_sales
        $report_total_paiement_sales = PaymentSale::with('sale')
            ->where('deleted_at', '=', null)
            ->whereBetween('date', array($start_date, $end_date))

            ->where(function ($query) use ($request, $warehouse_id, $array_warehouses_id) {
                if ($warehouse_id !== 0) {
                    return $query->whereHas('sale', function ($q) use ($request, $array_warehouses_id, $warehouse_id) {
                        $q->where('warehouse_id', $warehouse_id);
                    });
                } else {
                    return $query->whereHas('sale', function ($q) use ($request, $array_warehouses_id, $warehouse_id) {
                        $q->whereIn('warehouse_id', $array_warehouses_id);
                    });
                }
            })

            ->select(
                DB::raw('SUM(montant) AS sum')
            )->first();

        $item['paiement_sales'] =   'Rp ' . number_format($report_total_paiement_sales->sum, 2, ',', '.');


        //--------PaymentSaleReturns
        $report_total_PaymentSaleReturns = PaymentSaleReturns::with('SaleReturn')
            ->where('deleted_at', '=', null)
            ->whereBetween('date', array($start_date, $end_date))

            ->where(function ($query) use ($request, $warehouse_id, $array_warehouses_id) {
                if ($warehouse_id !== 0) {
                    return $query->whereHas('SaleReturn', function ($q) use ($request, $array_warehouses_id, $warehouse_id) {
                        $q->where('warehouse_id', $warehouse_id);
                    });
                } else {
                    return $query->whereHas('SaleReturn', function ($q) use ($request, $array_warehouses_id, $warehouse_id) {
                        $q->whereIn('warehouse_id', $array_warehouses_id);
                    });
                }
            })

            ->select(
                DB::raw('SUM(montant) AS sum')
            )->first();

        $item['PaymentSaleReturns'] =   'Rp ' . number_format($report_total_PaymentSaleReturns->sum, 2, ',', '.');


        //--------PaymentPurchaseReturns
        $report_total_PaymentPurchaseReturns = PaymentPurchaseReturns::with('PurchaseReturn')
            ->where('deleted_at', '=', null)
            ->whereBetween('date', array($start_date, $end_date))

            ->where(function ($query) use ($request, $warehouse_id, $array_warehouses_id) {
                if ($warehouse_id !== 0) {
                    return $query->whereHas('PurchaseReturn', function ($q) use ($request, $array_warehouses_id, $warehouse_id) {
                        $q->where('warehouse_id', $warehouse_id);
                    });
                } else {
                    return $query->whereHas('PurchaseReturn', function ($q) use ($request, $array_warehouses_id, $warehouse_id) {
                        $q->whereIn('warehouse_id', $array_warehouses_id);
                    });
                }
            })

            ->select(
                DB::raw('SUM(montant) AS sum')
            )->first();

        $item['PaymentPurchaseReturns'] =   'Rp ' . number_format($report_total_PaymentPurchaseReturns->sum, 2, ',', '.');


        //--------paiement_purchases
        $report_total_paiement_purchases = PaymentPurchase::with('purchase')
            ->where('deleted_at', '=', null)
            ->whereBetween('date', array($start_date, $end_date))

            ->where(function ($query) use ($request, $warehouse_id, $array_warehouses_id) {
                if ($warehouse_id !== 0) {
                    return $query->whereHas('purchase', function ($q) use ($request, $array_warehouses_id, $warehouse_id) {
                        $q->where('warehouse_id', $warehouse_id);
                    });
                } else {
                    return $query->whereHas('purchase', function ($q) use ($request, $array_warehouses_id, $warehouse_id) {
                        $q->whereIn('warehouse_id', $array_warehouses_id);
                    });
                }
            })

            ->select(
                DB::raw('SUM(montant) AS sum')
            )->first();

        $item['paiement_purchases'] =   'Rp ' . number_format($report_total_paiement_purchases->sum, 2, ',', '.');


        //--------expenses
        $report_total_expenses = Expense::whereBetween('date', array($start_date, $end_date))
            ->where('deleted_at', '=', null)

            ->where(function ($query) use ($request, $warehouse_id, $array_warehouses_id) {
                if ($warehouse_id !== 0) {
                    return $query->where('warehouse_id', $warehouse_id);
                } else {
                    return $query->whereIn('warehouse_id', $array_warehouses_id);
                }
            })

            ->select(
                DB::raw('SUM(amount) AS sum'),
                DB::raw("count(*) as nmbr")
            )->first();

        $item['expenses_sum'] =   'Rp ' . number_format($report_total_expenses->sum, 2, ',', '.');
        $item['expenses_count'] =   $report_total_expenses->nmbr;
        $item['payment_received'] = 'Rp ' . number_format($report_total_paiement_sales->sum  + $report_total_PaymentPurchaseReturns->sum, 2, ',', '.');
        $item['payment_sent'] = 'Rp ' . number_format($report_total_paiement_purchases->sum + $report_total_PaymentSaleReturns->sum + $report_total_expenses->sum, 2, ',', '.');
        $item['paiement_net'] = 'Rp ' . number_format(($report_total_paiement_sales->sum  + $report_total_PaymentPurchaseReturns->sum) - ($report_total_paiement_purchases->sum + $report_total_PaymentSaleReturns->sum + $report_total_expenses->sum), 2, ',', '.');
        $item['total_revenue'] =   'Rp ' . number_format($report_total_sales->sum -  $report_total_returns_sales->sum, 2, ',', '.');


        // return response()->json([
        //     'data' => $item,
        //     'warehouses' => $warehouses,
        // ]);
        return view('templates.reports.profit-loss', [
            'data' => $item,
            'warehouses' => $warehouses,
        ]);
    }
    public function quantityAlerts(Request $request)
    {
        $user_auth = auth()->user();
        $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();

        // Mengambil warehouse yang relevan berdasarkan peran pengguna
        if ($user_auth->hasAnyRole(['superadmin', 'inventaris'])) {
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
        } else {
            $warehouses = Warehouse::where('deleted_at', '=', null)
                ->whereIn('id', $warehouses_id)
                ->get(['id', 'name']);
        }

        // Mengambil produk yang sesuai dengan alert stock
        $products_alertsQuery = ProductWarehouse::join('products', 'product_warehouse.product_id', '=', 'products.id')
            ->whereRaw('qty <= stock_alert')
            ->when($request->filled('warehouse_id'), function ($query) use ($request) {
                return $query->where('warehouse_id', $request->input('warehouse_id'));
            })
            ->when(!$user_auth->hasRole(['superadmin', 'inventaris']), function ($query) use ($warehouses_id) {
                return $query->whereIn('warehouse_id', $warehouses_id);
            })
            ->paginate($request->input('limit', 5))->appends($request->except('page'));

        return view('templates.reports.quantity-alerts', [
            'stockalert' => $products_alertsQuery,
            'warehouses' => $warehouses,
        ]);
    }
    public function exportProductAlerts(Request $request)
    {
        $user_auth = auth()->user();
        $warehouses_id = Warehouse::pluck('id')->toArray(); // Anda mungkin perlu menyesuaikan ini

        return Excel::download(new ReportQuantityAlert($request, $user_auth, $warehouses_id), 'product_alerts.xlsx');
    }
    public function ReportProductAlerts(Request $request)
    {
        $user_auth = auth()->user();
        $warehouses_id = Warehouse::pluck('id')->toArray(); // Anda mungkin perlu menyesuaikan ini

        return Excel::download(new ReportProductAlerts($request, $user_auth, $warehouses_id), 'product_alerts.xlsx');
    }
    public function stockDetail($id)
    {
        return view('templates.reports.stock.stock-detail');
    }
    public function stock(Request $request)
    {
        $data = array();
        $user_auth = auth()->user();
        if ($user_auth->hasAnyRole(['superadmin', 'inventaris'])) {
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
        } else {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
            $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
        }

        $products_dataQuery = Product::with('unit', 'category', 'brand')
            ->where('deleted_at', '=', null)->latest();

        if ($request->filled('search')) {
            $products_dataQuery->where(function ($query) use ($request) {
                $query->where('products.name', 'LIKE', '%' . $request->input('search') . '%')
                    ->orWhere('products.code', 'LIKE', '%' . $request->input('search') . '%')
                    ->orWhere(function ($query) use ($request) {
                        $query->whereHas('category', function ($q) use ($request) {
                            $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                        });
                    });
            });
        }
        // Filter khusus untuk staff berdasarkan gudang


        $products = $products_dataQuery->paginate($request->input('limit', 5))->appends($request->except('page'));
        // return response()->json($products);

        foreach ($products as $product) {
            if ($product->type != 'is_service') {
                $item['id'] = $product->id;
                $item['code'] = $product->code;
                $item['name'] = $product->name;
                $item['category'] = $product['category']->name;
                $current_stock_query = ProductWarehouse::where('product_id', $product->id)
                    ->where('deleted_at', '=', null);

                // Jika role user adalah staff, tambahkan filter berdasarkan warehouse_ids
                if ($user_auth->hasRole('staff')) {
                    $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');
                    $current_stock_query->whereIn('warehouse_id', $warehouses_id);
                } else {
                    // Jika ada warehouse_id di request, tambahkan filter
                    if ($request->filled('warehouse_id')) {
                        $current_stock_query->where('warehouse_id', $request->warehouse_id);
                    }
                }

                // Hitung total stok
                $current_stock = $current_stock_query->sum('qty');

                $item['quantity'] = $current_stock . ' ' . $product['unit']->ShortName;
                $data[] = $item;
            } else {
                $item['id'] = $product->id;
                $item['code'] = $product->code;
                $item['name'] = $product->name;
                $item['category'] = $product['category']->name;
                $item['quantity'] = '---';

                $data[] = $item;
            }
        }
        return view('templates.reports.stock.stock', [
            'report' => $data,
            'products' => $products,
            'warehouses' => $warehouses,
        ]);
    }
    public function exportReportStock(Request $request)
    {
        $user_auth = auth()->user();
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "exportReportStock_{$timestamp}.xlsx";

        return Excel::download(new ReportProductStock($request, $user_auth), $filename);
    }
    public function stockDetailSales(Request $request, $id)
    {
        $user_auth = auth()->user();
        $product = Product::where('deleted_at', '=', null)->findOrFail($id);
        $sale_details_dataQuery = SaleDetail::with('product', 'sale', 'sale.client', 'sale.warehouse')
            ->where('product_id', $id)->latest();

        if ($request->filled('search')) {
            $sale_details_dataQuery->where(function ($query) use ($request) {
                $query->orWhereHas('sale.client', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                })
                    ->orWhereHas('sale.warehouse', function ($q) use ($request) {
                        $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                    })
                    ->orWhereHas('sale', function ($q) use ($request) {
                        $q->where('Ref', 'LIKE', '%' . $request->input('search') . '%')
                            ->orWhere('statut', 'LIKE', '%' . $request->input('search') . '%')
                            ->orWhere('payment_statut', 'LIKE', '%' . $request->input('search') . '%')
                            ->orWhere('payment_method', 'LIKE', '%' . $request->input('search') . '%')
                            ->orWhere('shipping_status', 'LIKE', '%' . $request->input('search') . '%');
                    })
                    ->orWhereHas('product', function ($q) use ($request) {
                        $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                    });
            });
        }
        // Filter khusus untuk staff berdasarkan gudang
        if ($user_auth->hasRole('staff')) {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
            $sale_details_dataQuery->whereHas('sale', function ($query) use ($warehouses_id) {
                $query->whereIn('warehouse_id', $warehouses_id);
            });
        }
        $sale_details = $sale_details_dataQuery->paginate($request->input('limit', 5))->appends($request->except('page'));

        $data = [];
        foreach ($sale_details as $detail) {
            $unit = null;

            if ($detail->sale_unit_id !== null) {
                $unit = Unit::where('id', $detail->sale_unit_id)->first();
            } else {
                $product_unit_sale_id = Product::with('unitSale')
                    ->where('id', $detail->product_id)
                    ->first();

                if ($product_unit_sale_id && $product_unit_sale_id->unitSale) {
                    $unit = Unit::where('id', $product_unit_sale_id->unitSale->id)->first();
                }
            }

            $product_name = $detail->product->name;
            // dd($product_name);
            if ($detail->product_variant_id) {
                $productVariant = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)->first();
                if ($productVariant) {
                    $product_name = '[' . $productVariant->name . ']' . $detail->product->name;
                }
            }

            // Check if related models exist before accessing their properties
            $sale = $detail->sale;
            $client = $sale ? $sale->client : null;
            $warehouse = $sale ? $sale->warehouse : null;

            $item['date'] = $detail->date ?? '';
            $item['Ref'] = $sale ? $sale->Ref : '';
            $item['sale_id'] = $sale ? $sale->id : '';
            $item['client_name'] = $client ? $client->name : '';
            $item['unit_sale'] = $unit ? $unit->ShortName : '';
            $item['warehouse_name'] = $warehouse ? $warehouse->name : '';
            $item['quantity'] = $detail->quantity . ' ' . ($unit ? $unit->ShortName : '');
            $item['total'] = $detail->total ?? 0;
            $item['product_name'] = $product_name;

            $data[] = $item;
        }
        //  $product_name = $detail->product->name;
        $product_stock = ProductWarehouse::where('product_id', $id)->where('deleted_at', '=', null)->get();
        if ($product->product_variant_id) {
            $product_stock = ProductWarehouse::where('product_id', $id)->where('product_variant_id', $product->product_variant_id ?? '')->where('deleted_at', '=', null)->get();
        }
        $b = [];
        foreach ($product_stock as $value) {
            $a['warehouse'] = $value->warehouse->name;
            $a['qty'] = $value->qty;
            $a['unit'] = $value->product->unit->ShortName;
            $b[] = $a;
        }
        return view('templates.reports.stock.stock-detail-sales', [
            'sales' => $data,
            'sale_details' => $sale_details,
            'product' => $product,
            'b' => $b
        ]);
    }
    public function exportstockSales(Request $request, $id)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "exportstockSales_{$timestamp}.xlsx";

        return Excel::download(new ReportSalesStock($request, $id), $filename);
    }
    public function exportstockSalesReturn(Request $request, $id)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "exportstockSalesReturn_{$timestamp}.xlsx";

        return Excel::download(new ReportSalesReturnStock($request, $id), $filename);
    }
    public function exportstockAdjustment(Request $request, $id)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "exportstockAdjustment_{$timestamp}.xlsx";

        return Excel::download(new ReportAdjustmentStock($request, $id), $filename);
    }
    public function exportstockTransfer(Request $request, $id)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "exportstockTransfer_{$timestamp}.xlsx";

        return Excel::download(new ReportTransferStock($request, $id), $filename);
    }
    public function stockDetailSalesReturn(request $request, $id)
    {
        $user_auth = auth()->user();
        $product = Product::where('deleted_at', '=', null)->findOrFail($id);
        $Sale_Return_details_data = SaleReturnDetails::with('product', 'SaleReturn', 'SaleReturn.client', 'SaleReturn.warehouse')
            ->where('quantity', '>', 0)
            ->where('product_id', $id)->latest();
        if ($request->filled('search')) {
            $Sale_Return_details_data->where(function ($query) use ($request) {
                $query->orWhereHas('SaleReturn.client', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                })
                    ->orWhereHas('SaleReturn', function ($q) use ($request) {
                        $q->where('Ref', 'LIKE', '%' . $request->input('search') . '%');
                    })
                    ->orWhereHas('product', function ($q) use ($request) {
                        $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                    });
            });
        }
        // Filter khusus untuk staff berdasarkan gudang
        if ($user_auth->hasRole('staff')) {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
            $Sale_Return_details_data->whereHas('SaleReturn', function ($query) use ($warehouses_id) {
                $query->whereIn('warehouse_id', $warehouses_id);
            });
        }
        $Sale_Return_details = $Sale_Return_details_data->paginate($request->input('limit', 5))->appends($request->except('page'));
        $data = [];
        foreach ($Sale_Return_details as $detail) {
            //check if detail has sale_unit_id Or Null
            if ($detail->sale_unit_id !== null) {
                $unit = Unit::where('id', $detail->sale_unit_id)->first();
            } else {
                $product_unit_sale_id = Product::with('unitSale')
                    ->where('id', $detail->product_id)
                    ->first();

                if ($product_unit_sale_id['unitSale']) {
                    $unit = Unit::where('id', $product_unit_sale_id['unitSale']->id)->first();
                } {
                    $unit = NULL;
                }
            }

            if ($detail->product_variant_id) {
                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)->first();

                $product_name = '[' . $productsVariants->name . ']' . $detail['product']['name'];
            } else {
                $product_name = $detail['product']['name'];
            }

            $item['date'] = $detail['SaleReturn']->date;
            $item['Ref'] = $detail['SaleReturn']->Ref;
            $item['return_sale_id'] = $detail['SaleReturn']->id;
            $item['client_name'] = $detail['SaleReturn']['client']->name;
            $item['warehouse_name'] = $detail['SaleReturn']['warehouse']->name;
            $item['unit_sale'] = $unit ? $unit->ShortName : '';
            $item['quantity'] = $detail->quantity . ' ' . $item['unit_sale'];
            $item['total'] = $detail->total;
            $item['product_name'] = $product_name;

            $data[] = $item;
        }
        $product_stock = ProductWarehouse::where('product_id', $id)->where('deleted_at', '=', null)->get();
        $b = [];
        foreach ($product_stock as $value) {
            $a['warehouse'] = $value->warehouse->name;
            $a['qty'] = $value->qty;
            $a['unit'] = $value->product->unit->ShortName;
            $b[] = $a;
        }
        return view('templates.reports.stock.stock-detail-sales-return', [
            'sales_return' => $data,
            'Sale_Return_details' => $Sale_Return_details,
            'product' => $product,
            'b' => $b
        ]);
    }

    public function stockDetailPurchases(request $request, $id)
    {
        $product = Product::where('deleted_at', '=', null)->findOrFail($id);
        $purchase_details_data = PurchaseDetail::with('product', 'purchase', 'purchase.provider', 'purchase.warehouse')
            ->where('product_id', $id)->latest();
        if ($request->filled('search')) {
            $purchase_details_data->where(function ($query) use ($request) {
                $query->orWhereHas('purchase.provider', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                })
                    ->orWhereHas('purchase.warehouse', function ($q) use ($request) {
                        $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                    })
                    ->orWhereHas('purchase', function ($q) use ($request) {
                        $q->where('Ref', 'LIKE', '%' . $request->input('search') . '%');
                    })
                    ->orWhereHas('product', function ($q) use ($request) {
                        $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                    });
            });
        }

        $purchase_details = $purchase_details_data->paginate($request->input('limit', 5))->appends($request->except('page'));
        $data = [];
        foreach ($purchase_details as $detail) {

            //-------check if detail has purchase_unit_id Or Null
            if ($detail->purchase_unit_id !== null) {
                $unit = Unit::where('id', $detail->purchase_unit_id)->first();
            } else {
                $product_unit_purchase_id = Product::with('unitPurchase')
                    ->where('id', $detail->product_id)
                    ->first();
                $unit = Unit::where('id', $product_unit_purchase_id['unitPurchase']->id)->first();
            }

            if ($detail->product_variant_id) {
                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)->first();

                $product_name = '[' . $productsVariants->name . ']' . $detail['product']['name'];
            } else {
                $product_name = $detail['product']['name'];
            }

            $item['date'] = $detail['purchase']->date;
            $item['Ref'] = $detail['purchase']->Ref;
            $item['purchase_id'] = $detail['purchase']->id;
            $item['provider_name'] = $detail['purchase']['provider']->name;
            $item['warehouse_name'] = $detail['purchase']['warehouse']->name;
            $item['quantity'] = $detail->quantity . ' ' . $unit->ShortName;;
            $item['total'] = $detail->total;
            $item['product_name'] = $product_name;
            $item['unit_purchase'] = $unit->ShortName;

            $data[] = $item;
        }
        $product_stock = ProductWarehouse::where('product_id', $id)->where('deleted_at', '=', null)->get();
        $b = [];
        foreach ($product_stock as $value) {
            $a['warehouse'] = $value->warehouse->name;
            $a['qty'] = $value->qty;
            $a['unit'] = $value->product->unit->ShortName;
            $b[] = $a;
        }
        return view('templates.reports.stock.stock-detail-purchases', [
            'purchases' => $data,
            'purchase_details' => $purchase_details,
            'product' => $product,
            'b' => $b
        ]);
        // return response()->json([
        //     'totalRows' => $totalRows,
        //     'purchases' => $data,
        // ]);
    }
    public function stockDetailPurchasesReturn(request $request, $id)
    {
        $product = Product::where('deleted_at', '=', null)->findOrFail($id);
        $purchase_return_details_data = PurchaseReturnDetails::with('product', 'PurchaseReturn', 'PurchaseReturn.provider', 'PurchaseReturn.warehouse')
            ->where('quantity', '>', 0)
            ->where('product_id', $id)
            ->latest();

        if ($request->filled('search')) {
            $purchase_return_details_data->where(function ($query) use ($request) {
                $query->orWhereHas('PurchaseReturn.provider', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                })
                    ->orWhereHas('PurchaseReturn.warehouse', function ($q) use ($request) {
                        $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                    })
                    ->orWhereHas('PurchaseReturn', function ($q) use ($request) {
                        $q->where('Ref', 'LIKE', '%' . $request->input('search') . '%');
                    })
                    ->orWhereHas('product', function ($q) use ($request) {
                        $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                    });
            });
        }
        $purchase_return_details = $purchase_return_details_data->paginate($request->input('limit', 5))->appends($request->except('page'));
        $data = [];
        foreach ($purchase_return_details as $detail) {
            //-------check if detail has purchase_unit_id Or Null
            if ($detail->purchase_unit_id !== null) {
                $unit = Unit::where('id', $detail->purchase_unit_id)->first();
            } else {
                $product_unit_purchase_id = Product::with('unitPurchase')
                    ->where('id', $detail->product_id)
                    ->first();
                $unit = Unit::where('id', $product_unit_purchase_id['unitPurchase']->id)->first();
            }

            if ($detail->product_variant_id) {
                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)->first();

                $product_name = '[' . $productsVariants->name . ']' . $detail['product']['name'];
            } else {
                $product_name = $detail['product']['name'];
            }

            $item['date'] = $detail['PurchaseReturn']->date;
            $item['Ref'] = $detail['PurchaseReturn']->Ref;
            $item['return_purchase_id'] = $detail['PurchaseReturn']->id;
            $item['provider_name'] = $detail['PurchaseReturn']['provider']->name;
            $item['warehouse_name'] = $detail['PurchaseReturn']['warehouse']->name;
            $item['quantity'] = $detail->quantity . ' ' . $unit->ShortName;;
            $item['total'] = $detail->total;
            $item['product_name'] = $product_name;
            $item['unit_purchase'] = $unit->ShortName;

            $data[] = $item;
        }
        $product_stock = ProductWarehouse::where('product_id', $id)->where('deleted_at', '=', null)->get();
        $b = [];
        foreach ($product_stock as $value) {
            $a['warehouse'] = $value->warehouse->name;
            $a['qty'] = $value->qty;
            $a['unit'] = $value->product->unit->ShortName;
            $b[] = $a;
        }
        return view('templates.reports.stock.stock-detail-purchases-return', [
            'purchases_return' => $data,
            'purchase_return_details' => $purchase_return_details,
            'product' => $product,
            'b' => $b
        ]);
        return response()->json([

            'purchases_return' => $data,
        ]);
    }
    public function stockDetailAdjustment(request $request, $id)
    {
        $user_auth = auth()->user();
        $product = Product::where('deleted_at', '=', null)->findOrFail($id);
        $adjustment_details_data = AdjustmentDetail::with('product', 'adjustment', 'adjustment.warehouse')
            ->where('product_id', $id)
            ->latest();

        if ($request->filled('search')) {
            $adjustment_details_data->where(function ($query) use ($request) {
                $query->orWhereHas('adjustment.warehouse', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                })
                    ->orWhereHas('adjustment', function ($q) use ($request) {
                        $q->where('Ref', 'LIKE', '%' . $request->input('search') . '%');
                    })
                    ->orWhereHas('product', function ($q) use ($request) {
                        $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                    });
            });
        }
        if ($user_auth->hasRole('staff')) {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
            $adjustment_details_data->whereHas('adjustment', function ($query) use ($warehouses_id) {
                $query->whereIn('warehouse_id', $warehouses_id);
            });
        }
        $adjustment_details = $adjustment_details_data->paginate($request->input('limit', 5))->appends($request->except('page'));
        $data = [];
        foreach ($adjustment_details as $detail) {

            if ($detail->product_variant_id) {
                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)->first();

                $product_name = '[' . $productsVariants->name . ']' . $detail['product']['name'];
            } else {
                $product_name = $detail['product']['name'];
            }

            $item['date'] = $detail['adjustment']->date;
            $item['Ref'] = $detail['adjustment']->Ref;
            $item['warehouse_name'] = $detail['adjustment']['warehouse']->name;
            $item['product_name'] = $product_name;

            $data[] = $item;
        }
        $product_stock = ProductWarehouse::where('product_id', $id)->where('deleted_at', '=', null)->get();
        $b = [];
        foreach ($product_stock as $value) {
            $a['warehouse'] = $value->warehouse->name;
            $a['qty'] = $value->qty;
            $a['unit'] = $value->product->unit->ShortName;
            $b[] = $a;
        }
        return view('templates.reports.stock.stock-detail-adjustment', [
            'adjustment' => $data,
            'adjustment_details' => $adjustment_details,
            'product' => $product,
            'b' => $b
        ]);
    }
    public function stockDetailTransfer(request $request, $id)
    {
        $user_auth = auth()->user();
        $product = Product::where('deleted_at', '=', null)->findOrFail($id);
        $transfer_details_data = TransferDetail::with('product', 'transfer', 'transfer.from_warehouse', 'transfer.to_warehouse')
            ->where('product_id', $id)
            ->latest();

        if ($request->filled('search')) {
            $transfer_details_data->where(function ($query) use ($request) {
                $query->orWhereHas('transfer.from_warehouse', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                })
                    ->orWhereHas('transfer.to_warehouse', function ($q) use ($request) {
                        $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                    })
                    ->orWhereHas('transfer', function ($q) use ($request) {
                        $q->where('Ref', 'LIKE', '%' . $request->input('search') . '%');
                    })
                    ->orWhereHas('product', function ($q) use ($request) {
                        $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                    });
            });
        }
        if ($user_auth->hasRole('staff')) {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
            $transfer_details_data->whereHas('transfer', function ($query) use ($warehouses_id) {
                $query->whereIn('to_warehouse_id', $warehouses_id);
            });
        }
        $transfer_details = $transfer_details_data->paginate($request->input('limit', 5))->appends($request->except('page'));
        $data = [];
        foreach ($transfer_details as $detail) {
            if ($detail->product_variant_id) {
                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)->first();

                $product_name = '[' . $productsVariants->name . ']' . $detail['product']['name'];
            } else {
                $product_name = $detail['product']['name'];
            }

            $item['date'] = $detail['transfer']->date;
            $item['Ref'] = $detail['transfer']->Ref;
            $item['from_warehouse'] = $detail['transfer']['from_warehouse']->name;
            $item['to_warehouse'] = $detail['transfer']['to_warehouse']->name;
            $item['product_name'] = $product_name;

            $data[] = $item;
        }
        $product_stock = ProductWarehouse::where('product_id', $id)->where('deleted_at', '=', null)->get();
        $b = [];
        foreach ($product_stock as $value) {
            $a['warehouse'] = $value->warehouse->name;
            $a['qty'] = $value->qty;
            $a['unit'] = $value->product->unit->ShortName;
            $b[] = $a;
        }
        return view('templates.reports.stock.stock-detail-transfer', [
            'transfer' => $data,
            'transfer_details' => $transfer_details,
            'product' => $product,
            'b' => $b
        ]);
    }
    //----------------- Customers Report -----------------------\\
    // public function customers(Request $request)
    // {
    //     $clientsQuery = Client::where('deleted_at', '=', null)->latest();
    //     if ($request->filled('search')) {
    //         $clientsQuery->where(function ($query) use ($request) {
    //             $query->orWhere('name', 'like', '%' . $request->input('search') . '%')
    //                 ->orWhere('phone', 'like', '%' . $request->input('search') . '%');
    //         });
    //     }
    //     $clients = $clientsQuery->paginate($request->input('limit', 5))->appends($request->except('page'));
    //     $data = array();
    //     // Initialize totals
    //     $totalSales = 0;
    //     $totalAmount = 0;
    //     $totalPaid = 0;
    //     $totalDue = 0;
    //     $totalReturnDue = 0;
    //     foreach ($clients as $client) {
    //         $item['total_sales'] = DB::table('sales')
    //             ->where('deleted_at', '=', null)
    //             ->where('client_id', $client->id)
    //             ->count();

    //         $item['total_amount'] = DB::table('sales')
    //             ->where('deleted_at', '=', null)
    //             ->where('client_id', $client->id)
    //             ->sum('GrandTotal');

    //         $item['total_paid'] = DB::table('sales')
    //             ->where('sales.deleted_at', '=', null)
    //             ->where('sales.client_id', $client->id)
    //             ->sum('paid_amount');

    //         $item['due'] = $item['total_amount'] - $item['total_paid'];

    //         $item['total_amount_return'] = DB::table('sale_returns')
    //             ->where('deleted_at', '=', null)
    //             ->where('client_id', $client->id)
    //             ->sum('GrandTotal');

    //         $item['total_paid_return'] = DB::table('sale_returns')
    //             ->where('sale_returns.deleted_at', '=', null)
    //             ->where('sale_returns.client_id', $client->id)
    //             ->sum('paid_amount');

    //         $item['return_Due'] = $item['total_amount_return'] - $item['total_paid_return'];

    //         $item['name'] = $client->name;
    //         $item['phone'] = $client->phone;
    //         $item['code'] = $client->code;
    //         $item['id'] = $client->id;

    //         $data[] = $item;
    //         // Add to totals
    //         $totalSales += $item['total_sales'];
    //         $totalAmount += $item['total_amount'];
    //         $totalPaid += $item['total_paid'];
    //         $totalDue += $item['due'];
    //         $totalReturnDue += $item['return_Due'];
    //     }
    //     return view('templates.reports.customers.customers', [
    //         'report' => $data,
    //         'clients' => $clients,
    //         'total_sales' => $totalSales,
    //         'total_amount' => $totalAmount,
    //         'total_paid' => $totalPaid,
    //         'total_due' => $totalDue,
    //         'total_return_due' => $totalReturnDue,
    //     ]);
    // }
    public function customers(Request $request)
    {
        $user_auth = auth()->user();
        $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();

        $clientsQuery = Client::where('deleted_at', '=', null)->latest();
        if ($request->filled('search')) {
            $clientsQuery->where(function ($query) use ($request) {
                $query->orWhere('name', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('phone', 'like', '%' . $request->input('search') . '%');
            });
        }
        $clients = $clientsQuery->paginate($request->input('limit', 5))->appends($request->except('page'));
        $data = array();
        // Initialize totals
        $totalSales = 0;
        $totalAmount = 0;
        $totalPaid = 0;
        $totalDue = 0;
        $totalReturnDue = 0;
        $totalPaidReturn = 0;

        foreach ($clients as $client) {
            $salesQuery = DB::table('sales')
                ->where('deleted_at', '=', null)
                ->where('client_id', $client->id);

            $salesReturnQuery = DB::table('sale_returns')
                ->where('deleted_at', '=', null)
                ->where('client_id', $client->id);

            // Apply warehouse filtering for staff
            if (!$user_auth->hasAnyRole(['superadmin', 'inventaris'])) {
                $salesQuery->whereIn('warehouse_id', $warehouses_id);
                $salesReturnQuery->whereIn('warehouse_id', $warehouses_id);
            }

            $item['total_sales'] = $salesQuery->count();

            $item['total_amount'] = $salesQuery->sum('GrandTotal');

            $item['total_paid'] = $salesQuery->sum('paid_amount');

            $item['due'] = $item['total_amount'] - $item['total_paid'];

            $item['total_amount_return'] = $salesReturnQuery->sum('GrandTotal');

            $item['total_paid_return'] = $salesReturnQuery->sum('paid_amount');

            $item['return_Due'] = $item['total_amount_return'] - $item['total_paid_return'];

            $item['name'] = $client->name;
            $item['phone'] = $client->phone;
            $item['code'] = $client->code;
            $item['id'] = $client->id;

            $data[] = $item;
            // Add to totals
            $totalSales += $item['total_sales'];
            $totalAmount += $item['total_amount'];
            $totalPaid += $item['total_paid'];
            $totalPaidReturn += $item['total_amount_return'];
            // $totalDue += $item['due'];
            $totalReturnDue += $item['return_Due'];
        }

        return view('templates.reports.customers.customers', [
            'report' => $data,
            'clients' => $clients,
            'total_sales' => $totalSales,
            'total_amount' => $totalAmount,
            'total_paid' => $totalPaid,
            'total_paid_return' => $totalPaidReturn,
            'total_due' => $totalDue,
            'total_return_due' => $totalReturnDue,
        ]);
    }

    public function customersDetailSales(Request $request, $id)
    {
        // Find the client or fail if not found
        $client = Client::where('deleted_at', '=', null)->findOrFail($id);

        // Calculate client-specific data
        $data['total_sales'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)->count();
        $data['total_amount'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)->sum('GrandTotal');
        $data['total_paid'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)->sum('paid_amount');
        $data['due'] = $data['total_amount'] - $data['total_paid'];


        // Retrieve sales data for the specific client
        $salesQuery = Sale::where('deleted_at', '=', null)->where('client_id', $id)->with('client', 'warehouse')->latest();
        if ($request->filled('search')) {
            $salesQuery->where(function ($query) use ($request) {
                $query->orWhere('Ref', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('statut', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('payment_statut', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('payment_method', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('shipping_status', 'like', '%' . $request->input('search') . '%')
                    ->orWhere(function ($query) use ($request) {
                        $query->whereHas('client', function ($q) use ($request) {
                            $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                        });
                    });
            });
        }
        $sales =  $salesQuery->paginate($request->input('limit', 5))->appends($request->except('page'));
        $report = [];
        foreach ($sales as $sale) {
            $item = [
                'id' => $sale->id,
                'date' => $sale->date,
                'Ref' => $sale->Ref,
                'warehouse_name' => $sale->warehouse->name,
                'client_name' => $sale->client->name,
                'statut' => $sale->statut,
                'GrandTotal' => $sale->GrandTotal,
                'paid_amount' => $sale->paid_amount,
                'due' => $sale->GrandTotal - $sale->paid_amount,
                'payment_status' => $sale->payment_statut,
                'shipping_status' => $sale->shipping_status,
                "shipping" => $sale->shipping,
            ];

            $report[] = $item;
        }

        return view('templates.reports.customers.customers-detail-sales', [
            'client_data' => $data,
            'report' => $report,
            'sales' => $sales,
            'client' =>  $client,
        ]);
    }
    public function customersDetailSalesExport(Request $request, $id)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "report-customer-sales_{$timestamp}.xlsx";
        return Excel::download(new ReportCustomerSalesExport($request, $id), $filename);
    }

    public function customersDetailReturns(Request $request, $id)
    {
        // Find the client or fail if not found
        $client = Client::where('deleted_at', '=', null)->findOrFail($id);

        // Calculate client-specific data
        $data['total_sales'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)->count();
        $data['total_amount'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)->sum('GrandTotal');
        $data['total_paid'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)->sum('paid_amount');
        $data['due'] = $data['total_amount'] - $data['total_paid'];
        $saleReturnsQuery = SaleReturn::where('deleted_at', '=', null)
            ->where('client_id', $id)
            ->with('sale', 'client', 'warehouse')
            ->latest();

        if ($request->filled('search')) {
            $saleReturnsQuery->where(function ($query) use ($request) {
                $query->orWhere('Ref', 'LIKE', '%' . $request->input('search') . '%')
                    ->orWhere('statut', 'LIKE', '%' . $request->input('search') . '%')
                    ->orWhere('payment_statut', 'LIKE', '%' . $request->input('search') . '%')
                    ->orWhereHas('client', function ($q) use ($request) {
                        $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                    })
                    ->orWhereHas('sale', function ($q) use ($request) {
                        $q->where('Ref', 'LIKE', '%' . $request->input('search') . '%');
                    })
                    ->orWhereHas('warehouse', function ($q) use ($request) {
                        $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                    });
            });
        }

        $saleReturns = $saleReturnsQuery->paginate($request->input('limit', 5))->appends($request->except('page'));
        $returnsCustomer = [];
        foreach ($saleReturns as $saleReturn) {
            $item = [
                'id' => $saleReturn->id,
                'Ref' => $saleReturn->Ref,
                'statut' => $saleReturn->statut,
                'client_name' => $saleReturn->client->name,
                'sale_ref' => $saleReturn->sale ? $saleReturn->sale->Ref : '---',
                'sale_id' => $saleReturn->sale ? $saleReturn->sale->id : null,
                'warehouse_name' => $saleReturn->warehouse->name,
                'GrandTotal' => $saleReturn->GrandTotal,
                'paid_amount' => $saleReturn->paid_amount,
                'due' => $saleReturn->GrandTotal - $saleReturn->paid_amount,
                'payment_status' => $saleReturn->payment_statut,
            ];

            $returnsCustomer[] = $item;
        }
        return view('templates.reports.customers.customers-detail-returns', [
            'client_data' => $data,
            'saleReturns' => $saleReturns,
            'returns_customer' => $returnsCustomer,
            'client' =>  $client,
        ]);
    }
    public function customersDetailSalesReturnsExport(Request $request, $id)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "report-customer-sales-returns_{$timestamp}.xlsx";
        return Excel::download(new ReportCustomerSalesReturnExport($request, $id), $filename);
    }
    public function customersDetailPayments(Request $request, $id)
    {
        // Find the client or fail if not found
        $client = Client::where('deleted_at', '=', null)->findOrFail($id);

        // Calculate client-specific data
        $data['total_sales'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)->count();
        $data['total_amount'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)->sum('GrandTotal');
        $data['total_paid'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)->sum('paid_amount');
        $data['due'] = $data['total_amount'] - $data['total_paid'];
        $paymentsQuery = DB::table('payment_sales')
            ->where('payment_sales.deleted_at', '=', null)
            ->join('sales', 'payment_sales.sale_id', '=', 'sales.id')
            ->where('sales.client_id', $id)
            ->latest('payment_sales.date');
        if ($request->filled('search')) {
            $paymentsQuery->where(function ($query) use ($request) {
                $query->orWhere('payment_sales.Ref', 'LIKE', '%' . $request->input('search') . '%')
                    ->orWhere('payment_sales.date', 'LIKE', '%' . $request->input('search') . '%')
                    ->orWhere('payment_sales.Reglement', 'LIKE', '%' . $request->input('search') . '%');
            });
        }
        $payments = $paymentsQuery->select(
            'payment_sales.date',
            'payment_sales.Ref AS Payment_Ref',
            'sales.Ref AS Sale_Ref',
            'payment_sales.Reglement',
            'payment_sales.montant'
        )->paginate($request->input('limit', 10))->appends($request->except('page'));
        // dd($payments);
        $paymentDetails = [];
        foreach ($payments as $payment) {
            $item = [
                'date' => $payment->date,
                'Payment_Ref' => $payment->Payment_Ref,
                'Sale_Ref' => $payment->Sale_Ref,
                'Reglement' => $payment->Reglement,
                'montant' => $payment->montant,
            ];

            $paymentDetails[] = $item;
        }
        return view('templates.reports.customers.customers-detail-payments', [
            'client_data' => $data,
            'paymentDetails' => $paymentDetails,
            'payments' => $payments,
            'client' =>  $client,
        ]);
    }
    public function customersDetailSalesPaymentExport(Request $request, $id)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "report-customer-sales-payment_{$timestamp}.xlsx";
        return Excel::download(new ReportCustomerSalesPaymentExport($request, $id), $filename);
    }
    //----------------- Customers Report -----------------------\\
    public function supplier(request $request)
    {
        $providersQuery = Provider::where('deleted_at', '=', null)->latest();
        if ($request->filled('search')) {
            $providersQuery->where(function ($query) use ($request) {
                $query->orWhere('name', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('phone', 'like', '%' . $request->input('search') . '%');
            });
        }
        $providers = $providersQuery->paginate($request->input('limit', 5))->appends($request->except('page'));
        $data = array();
        $totalPurchase = 0;
        $totalAmount = 0;
        $totalPaid = 0;
        $totalDue = 0;
        $totalReturnDue = 0;
        foreach ($providers as $provider) {
            $item['total_purchase'] = DB::table('purchases')
                ->where('deleted_at', '=', null)
                ->where('provider_id', $provider->id)
                ->count();

            $item['total_amount'] = DB::table('purchases')
                ->where('deleted_at', '=', null)
                ->where('provider_id', $provider->id)
                ->sum('GrandTotal');

            $item['total_paid'] = DB::table('purchases')
                ->where('purchases.deleted_at', '=', null)
                ->where('purchases.provider_id', $provider->id)
                ->sum('paid_amount');

            $item['due'] = $item['total_amount'] - $item['total_paid'];

            $item['total_amount_return'] = DB::table('purchase_returns')
                ->where('deleted_at', '=', null)
                ->where('provider_id', $provider->id)
                ->sum('GrandTotal');

            $item['total_paid_return'] = DB::table('purchase_returns')
                ->where('deleted_at', '=', null)
                ->where('provider_id', $provider->id)
                ->sum('paid_amount');

            $item['return_Due'] = $item['total_amount_return'] - $item['total_paid_return'];

            $item['id'] = $provider->id;
            $item['name'] = $provider->name;
            $item['phone'] = $provider->phone;
            $item['code'] = $provider->code;

            $data[] = $item;
            $totalPurchase += $item['total_purchase'];
            $totalAmount += $item['total_amount'];
            $totalPaid += $item['total_paid'];
            $totalDue += $item['due'];
            $totalReturnDue += $item['return_Due'];
        }
        return view('templates.reports.supplier.supplier', [
            'report' => $data,
            'providers' => $providers,
            'total_purchase' => $totalPurchase,
            'total_amount' => $totalAmount,
            'total_paid' => $totalPaid,
            'total_due' => $totalDue,
            'total_return_due' => $totalReturnDue,

        ]);
        // return response()->json([
        //     'totalRows' => $totalRows,
        // ]);
    }
    public function Purchases_Provider(request $request, $id)
    {
        $provider = Provider::where('deleted_at', '=', null)->findOrFail($id);
        // Calculate client-specific data
        $data['total_purchases'] = DB::table('purchases')->where('deleted_at', '=', null)->where('provider_id', $id)->count();
        $data['total_amount'] = DB::table('purchases')->where('deleted_at', '=', null)->where('provider_id', $id)->sum('GrandTotal');
        $data['total_paid'] = DB::table('purchases')->where('deleted_at', '=', null)->where('provider_id', $id)->sum('paid_amount');
        $data['due'] = $data['total_amount'] - $data['total_paid'];

        $purchasesQuery = Purchase::where('deleted_at', '=', null)
            ->with('provider', 'warehouse')
            ->where('provider_id', $id)->latest();
        if ($request->filled('search')) {
            $purchasesQuery->where(function ($query) use ($request) {
                $query->orWhere('Ref', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('statut', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('payment_statut', 'like', '%' . $request->input('search') . '%')
                    // ->orWhere('payment_method', 'like', '%' . $request->input('search') . '%')
                    // ->orWhere('shipping_status', 'like', '%' . $request->input('search') . '%')
                    ->orWhere(function ($query) use ($request) {
                        $query->whereHas('provider', function ($q) use ($request) {
                            $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                        });
                    })
                    ->orWhere(function ($query) use ($request) {
                        $query->whereHas('warehouse', function ($q) use ($request) {
                            $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                        });
                    });
            });
        }
        $purchases =  $purchasesQuery->paginate($request->input('limit', 5))->appends($request->except('page'));
        $report = [];
        foreach ($purchases as $purchase) {
            $item['id'] = $purchase->id;
            $item['Ref'] = $purchase->Ref;
            $item['warehouse_name'] = $purchase['warehouse']->name;
            $item['provider_name'] = $purchase['provider']->name;
            $item['statut'] = $purchase->statut;
            $item['GrandTotal'] = $purchase->GrandTotal;
            $item['paid_amount'] = $purchase->paid_amount;
            $item['due'] = $purchase->GrandTotal - $purchase->paid_amount;
            $item['payment_status'] = $purchase->payment_statut;

            $report[] = $item;
        }

        // return response()->json([
        //     'purchases' => $purchases,
        //     'purchases_data' => $data,
        //     'report' => $report,
        // ]);
        return view('templates.reports.supplier.supplier-detail-purchase', [
            'purchases_data' => $data,
            'purchases' => $purchases,
            'provider' => $provider,
            'report' => $report,
        ]);
    }
    public function providerDetailPurchasesExport(Request $request, $id)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "report-supplier-purchases_{$timestamp}.xlsx";
        return Excel::download(new ReportProviderPurchasesExport($request, $id), $filename);
    }
    public function Returns_Provider(request $request, $id)
    {

        $provider = Provider::where('deleted_at', '=', null)->findOrFail($id);
        // Calculate client-specific data
        $data['total_purchases'] = DB::table('purchases')->where('deleted_at', '=', null)->where('provider_id', $id)->count();
        $data['total_amount'] = DB::table('purchases')->where('deleted_at', '=', null)->where('provider_id', $id)->sum('GrandTotal');
        $data['total_paid'] = DB::table('purchases')->where('deleted_at', '=', null)->where('provider_id', $id)->sum('paid_amount');
        $data['due'] = $data['total_amount'] - $data['total_paid'];


        $PurchaseReturnQuery = PurchaseReturn::where('deleted_at', '=', null)
            ->with('purchase', 'provider', 'warehouse')
            ->where('provider_id', $id)->latest();
        if ($request->filled('search')) {
            $PurchaseReturnQuery->where(function ($query) use ($request) {
                $query->orWhere('Ref', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('statut', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('payment_statut', 'like', '%' . $request->input('search') . '%')
                    ->orWhere(function ($query) use ($request) {
                        $query->whereHas('provider', function ($q) use ($request) {
                            $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                        });
                    })
                    ->orWhere(function ($query) use ($request) {
                        return $query->whereHas('purchase', function ($q) use ($request) {
                            $q->where('Ref', 'LIKE', '%' . $request->input('search') . '%');
                        });
                    })
                    ->orWhere(function ($query) use ($request) {
                        $query->whereHas('warehouse', function ($q) use ($request) {
                            $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                        });
                    });
            });
        }
        $PurchaseReturn =  $PurchaseReturnQuery->paginate($request->input('limit', 5))->appends($request->except('page'));
        $report = [];
        foreach ($PurchaseReturn as $Purchase_Return) {
            $item['id'] = $Purchase_Return->id;
            $item['Ref'] = $Purchase_Return->Ref;
            $item['statut'] = $Purchase_Return->statut;
            $item['purchase_ref'] = $Purchase_Return['purchase'] ? $Purchase_Return['purchase']->Ref : '---';
            $item['purchase_id'] = $Purchase_Return['purchase'] ? $Purchase_Return['purchase']->id : NULL;
            $item['provider_name'] = $Purchase_Return['provider']->name;
            $item['warehouse_name'] = $Purchase_Return['warehouse']->name;
            $item['GrandTotal'] = $Purchase_Return->GrandTotal;
            $item['paid_amount'] = $Purchase_Return->paid_amount;
            $item['due'] = $Purchase_Return->GrandTotal - $Purchase_Return->paid_amount;
            $item['payment_status'] = $Purchase_Return->payment_statut;

            $report[] = $item;
        }
        return view('templates.reports.supplier.supplier-detail-returns', [
            'purchases_data' => $data,
            'PurchaseReturn' => $PurchaseReturn,
            'provider' => $provider,
            'report' => $report,
        ]);
    }
    public function providerDetailPurchasesReturnsExport(Request $request, $id)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "report-supplier-purchases-returns_{$timestamp}.xlsx";
        return Excel::download(new ReportProviderPurchasesReturnExport($request, $id), $filename);
    }
    public function Payments_Provider(request $request, $id)
    {
        $provider = Provider::where('deleted_at', '=', null)->findOrFail($id);
        // Calculate client-specific data
        $data['total_purchases'] = DB::table('purchases')->where('deleted_at', '=', null)->where('provider_id', $id)->count();
        $data['total_amount'] = DB::table('purchases')->where('deleted_at', '=', null)->where('provider_id', $id)->sum('GrandTotal');
        $data['total_paid'] = DB::table('purchases')->where('deleted_at', '=', null)->where('provider_id', $id)->sum('paid_amount');
        $data['due'] = $data['total_amount'] - $data['total_paid'];

        $paymentsQuery = DB::table('payment_purchases')
            ->where('payment_purchases.deleted_at', '=', null)
            ->join('purchases', 'payment_purchases.purchase_id', '=', 'purchases.id')
            ->where('purchases.provider_id', $id)->latest();
        if ($request->filled('search')) {
            $paymentsQuery->where(function ($query) use ($request) {
                $query->orWhere('payment_purchases.Ref', 'LIKE', '%' . $request->input('search') . '%')
                    ->orWhere('payment_purchases.date', 'LIKE', '%' . $request->input('search') . '%')
                    ->orWhere('payment_purchases.Reglement', 'LIKE', '%' . $request->input('search') . '%');
            });
        }
        $payments = $paymentsQuery->select(
            'payment_purchases.date',
            'payment_purchases.Ref AS Ref',
            'purchases.Ref AS purchase_Ref',
            'payment_purchases.Reglement',
            'payment_purchases.montant'
        )->paginate($request->input('limit', 10))->appends($request->except('page'));
        $paymentDetails = [];
        foreach ($payments as $payment) {
            $item = [
                'date' => $payment->date,
                'Ref' => $payment->Ref,
                'purchase_Ref' => $payment->purchase_Ref,
                'Reglement' => $payment->Reglement,
                'montant' => $payment->montant,
            ];

            $paymentDetails[] = $item;
        }
        return view('templates.reports.supplier.supplier-detail-payments', [
            'purchases_data' => $data,
            'payments' => $payments,
            'paymentDetails' => $paymentDetails,
            'provider' => $provider,
        ]);
    }
    public function providerDetailPaymentExport(Request $request, $id)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "report-supplier-purchases-payments_{$timestamp}.xlsx";
        return Excel::download(new ReportProviderPurchasesPaymentExport($request, $id), $filename);
    }
    public function supplierDetail($id)
    {
        return view('templates.reports.supplier-detail');
    }

    public function topSellingProduct(Request $request)
    {
        $user_auth = auth()->user();
        $productsQuery = SaleDetail::join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->whereNull('sales.deleted_at');

        // Filter berdasarkan tanggal jika from_date dan to_date diisi
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $productsQuery->whereBetween('sale_details.date', [$request->from_date, $request->to_date]);
        }

        // Filter berdasarkan pencarian jika search diisi
        if ($request->filled('search')) {
            $productsQuery->where(function ($query) use ($request) {
                $query->where('products.name', 'LIKE', '%' . $request->input('search') . '%')
                    ->orWhere('products.code', 'LIKE', '%' . $request->input('search') . '%');
            });
        }
        // Filter khusus untuk staff berdasarkan gudang
        if ($user_auth->hasRole('staff')) {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');
            $productsQuery->whereIn('sales.warehouse_id', $warehouses_id); // Filter berdasarkan warehouse_id pada tabel sales
        }
        // Hilangkan paginasi
        $products = $productsQuery->select(
            DB::raw('products.name as name'),
            DB::raw('products.code as code'),
            DB::raw('count(*) as total_sales'),
            DB::raw('sum(sale_details.total) as total')
        )->groupBy('products.name', 'products.code')->paginate($request->input('limit', 5))->appends($request->except('page')); // Menggunakan get() bukan paginate()

        $productDetail = [];
        foreach ($products as $product) {
            $item = [
                'code' => $product->code,
                'name' => $product->name,
                'total_sales' => $product->total_sales,
                'total' => $product->total,
            ];

            $productDetail[] = $item;
        }

        return view('templates.reports.top-selling-product', [
            'products' => $products,
            'productDetail' => $productDetail,
        ]);
    }

    public function topSellingProductExport(Request $request)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "report-top-selling-product_{$timestamp}.xlsx";
        return Excel::download(new TopSellingProductExport($request), $filename);
    }


    //----------------- Warehouse Report-----------------------\\
    public function warehouseSales(request $request)
    {
        $saleQuery = Sale::where('deleted_at', '=', null);
        if ($request->filled('warehouse_id')) {
            $saleQuery->where('warehouse_id', '=', $request->input('warehouse_id'));
        }
        $data['sales'] = $saleQuery->count();

        $data['purchases'] = Purchase::where('deleted_at', '=', null);
        if ($request->filled('warehouse_id')) {
            $data['purchases']->where('warehouse_id', '=', $request->input('warehouse_id'));
        }
        $data['purchases'] = $data['purchases']->count();

        $data['ReturnPurchase'] = PurchaseReturn::where('deleted_at', '=', null);
        if ($request->filled('warehouse_id')) {
            $data['ReturnPurchase']->where('warehouse_id', '=', $request->input('warehouse_id'));
        }
        $data['ReturnPurchase'] = $data['ReturnPurchase']->count();

        $data['ReturnSale'] = SaleReturn::where('deleted_at', '=', null);
        if ($request->filled('warehouse_id')) {
            $data['ReturnSale']->where('warehouse_id', '=', $request->input('warehouse_id'));
        }
        $data['ReturnSale'] = $data['ReturnSale']->count();

        $user_auth = auth()->user();
        if ($user_auth->hasAnyRole(['superadmin', 'inventaris'])) {
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
        } else {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');
            $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
        }
        // 
        $salesQuery = Sale::where('deleted_at', '=', null)->with('client', 'warehouse');

        if ($request->filled('warehouse_id')) {
            $salesQuery->where('warehouse_id', '=', $request->input('warehouse_id'));
        }

        if ($request->filled('search')) {
            $salesQuery->where(function ($query) use ($request) {
                $query->where('Ref', 'like', '%' . $request->input('search') . '%')
                    ->orWhere(function ($query) use ($request) {
                        $query->whereHas('client', function ($q) use ($request) {
                            $q->where('name', 'like', '%' . $request->input('search') . '%');
                        });
                    });
            });
        }

        $sales = $salesQuery->paginate($request->input('limit', 5))->appends($request->except('page'));

        $sales_data = [];
        foreach ($sales as $sale) {
            $item = [
                'id' => $sale->id,
                'date' => $sale->date,
                'Ref' => $sale->Ref,
                'warehouse_name' => $sale->warehouse->name,
                'client_name' => $sale->client->name,
                'statut' => $sale->statut,
                'GrandTotal' => $sale->GrandTotal,
                'paid_amount' => $sale->paid_amount,
                'due' => $sale->GrandTotal - $sale->paid_amount,
                'payment_status' => $sale->payment_statut,
                'shipping_status' => $sale->shipping_status,
                "shipping" => $sale->shipping,
            ];

            $sales_data[] = $item;
        }
        return view('templates.reports.warehouse.warehouse', [
            'data' => $data,
            'sales_data' => $sales_data,
            'sales' => $sales,
            'warehouses' => $warehouses,
        ]);
    }
    public function exportwarehouseSales(Request $request)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "warehouseSales_{$timestamp}.xlsx";

        return Excel::download(new ReportSaleWarehouse($request), $filename);
    }
    public function warehouseSalesReturns(Request $request)
    {
        $saleQuery = Sale::where('deleted_at', '=', null);
        if ($request->filled('warehouse_id')) {
            $saleQuery->where('warehouse_id', '=', $request->input('warehouse_id'));
        }
        $data['sales'] = $saleQuery->count();

        $data['purchases'] = Purchase::where('deleted_at', '=', null);
        if ($request->filled('warehouse_id')) {
            $data['purchases']->where('warehouse_id', '=', $request->input('warehouse_id'));
        }
        $data['purchases'] = $data['purchases']->count();

        $data['ReturnPurchase'] = PurchaseReturn::where('deleted_at', '=', null);
        if ($request->filled('warehouse_id')) {
            $data['ReturnPurchase']->where('warehouse_id', '=', $request->input('warehouse_id'));
        }
        $data['ReturnPurchase'] = $data['ReturnPurchase']->count();

        $data['ReturnSale'] = SaleReturn::where('deleted_at', '=', null);
        if ($request->filled('warehouse_id')) {
            $data['ReturnSale']->where('warehouse_id', '=', $request->input('warehouse_id'));
        }
        $data['ReturnSale'] = $data['ReturnSale']->count();
        $user_auth = auth()->user();
        if ($user_auth->hasAnyRole(['superadmin', 'inventaris'])) {
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
        } else {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');
            $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
        }
        // Sale Returns with pagination and filtering
        $saleReturnQuery = SaleReturn::where('deleted_at', '=', null)
            ->with('sale', 'client', 'warehouse');

        if ($request->filled('warehouse_id')) {
            $saleReturnQuery->where('warehouse_id', '=', $request->input('warehouse_id'));
        }

        if ($request->filled('search')) {
            $saleReturnQuery->where(function ($query) use ($request) {
                $query->where('Ref', 'LIKE', '%' . $request->input('search') . '%')
                    ->orWhere(function ($query) use ($request) {
                        $query->whereHas('sale', function ($q) use ($request) {
                            $q->where('Ref', 'LIKE', '%' . $request->input('search') . '%');
                        });
                    })
                    ->orWhere(function ($query) use ($request) {
                        $query->whereHas('client', function ($q) use ($request) {
                            $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                        });
                    });
            });
        }
        $saleReturns = $saleReturnQuery->paginate($request->input('limit', 5))->appends($request->except('page'));
        $return_data = [];
        foreach ($saleReturns as $saleReturn) {
            $item = [
                'id' => $saleReturn->id,
                'warehouse_name' => $saleReturn->warehouse->name,
                'Ref' => $saleReturn->Ref,
                'statut' => $saleReturn->statut,
                'client_name' => $saleReturn->client->name,
                'sale_ref' => $saleReturn->sale ? $saleReturn->sale->Ref : '---',
                'sale_id' => $saleReturn->sale ? $saleReturn->sale->id : null,
                'GrandTotal' => $saleReturn->GrandTotal,
                'paid_amount' => $saleReturn->paid_amount,
                'due' => $saleReturn->GrandTotal - $saleReturn->paid_amount,
                'payment_status' => $saleReturn->payment_statut,
            ];

            $return_data[] = $item;
        }
        // return response()->json([
        //     'data' => $data,
        //     'warehouses' => $warehouses,
        //     'saleReturns' => $saleReturns,
        //     'saleReturns_data' => $return_data
        // ]);
        return view('templates.reports.warehouse.warehouse-sales-returns', [
            'data' => $data,
            'warehouses' => $warehouses,
            'saleReturns' => $saleReturns,
            'saleReturns_data' => $return_data
        ]);
    }
    public function exportwarehouseSalesReturns(Request $request)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "warehouseSalesReturns_{$timestamp}.xlsx";

        return Excel::download(new ReportSaleReturnWarehouse($request), $filename);
    }
    public function warehousePurchaseReturns(Request $request)
    {
        $saleQuery = Sale::where('deleted_at', '=', null);
        if ($request->filled('warehouse_id')) {
            $saleQuery->where('warehouse_id', '=', $request->input('warehouse_id'));
        }
        $data['sales'] = $saleQuery->count();

        $data['purchases'] = Purchase::where('deleted_at', '=', null);
        if ($request->filled('warehouse_id')) {
            $data['purchases']->where('warehouse_id', '=', $request->input('warehouse_id'));
        }
        $data['purchases'] = $data['purchases']->count();

        $data['ReturnPurchase'] = PurchaseReturn::where('deleted_at', '=', null);
        if ($request->filled('warehouse_id')) {
            $data['ReturnPurchase']->where('warehouse_id', '=', $request->input('warehouse_id'));
        }
        $data['ReturnPurchase'] = $data['ReturnPurchase']->count();

        $data['ReturnSale'] = SaleReturn::where('deleted_at', '=', null);
        if ($request->filled('warehouse_id')) {
            $data['ReturnSale']->where('warehouse_id', '=', $request->input('warehouse_id'));
        }
        $data['ReturnSale'] = $data['ReturnSale']->count();
        $user_auth = auth()->user();
        if ($user_auth->hasAnyRole(['superadmin', 'inventaris'])) {
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
        } else {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');
            $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
        }
        $purchaseReturnQuery = PurchaseReturn::where('deleted_at', '=', null)
            ->with('purchase', 'provider', 'warehouse');

        if ($request->filled('warehouse_id')) {
            $purchaseReturnQuery->where('warehouse_id', '=', $request->input('warehouse_id'));
        }

        if ($request->filled('search')) {
            $purchaseReturnQuery->where(function ($query) use ($request) {
                $query->whereHas('purchase', function ($q) use ($request) {
                    $q->where('Ref', 'LIKE', '%' . $request->input('search') . '%');
                })
                    ->orWhere('Ref', 'LIKE', '%' . $request->input('search') . '%')
                    ->orWhere('GrandTotal', $request->input('search'))
                    ->orWhere('payment_statut', 'like', '%' . $request->input('search') . '%')
                    ->orWhere(function ($query) use ($request) {
                        $query->whereHas('provider', function ($q) use ($request) {
                            $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                        });
                    });
            });
        }

        $purchaseReturns = $purchaseReturnQuery->paginate($request->input('limit', 5))->appends($request->except('page'));

        $purchase_return_data = [];
        foreach ($purchaseReturns as $purchaseReturn) {
            $item = [
                'id' => $purchaseReturn->id,
                'Ref' => $purchaseReturn->Ref,
                'statut' => $purchaseReturn->statut,
                'purchase_ref' => $purchaseReturn->purchase ? $purchaseReturn->purchase->Ref : '---',
                'purchase_id' => $purchaseReturn->purchase ? $purchaseReturn->purchase->id : null,
                'warehouse_name' => $purchaseReturn->warehouse->name,
                'provider_name' => $purchaseReturn->provider->name,
                'GrandTotal' => $purchaseReturn->GrandTotal,
                'paid_amount' => $purchaseReturn->paid_amount,
                'due' => $purchaseReturn->GrandTotal - $purchaseReturn->paid_amount,
                'payment_status' => $purchaseReturn->payment_statut,
            ];

            $purchase_return_data[] = $item;
        }

        // return response()->json([
        //     'data' => $data,
        //     'warehouses' => $warehouses,
        //     'saleReturns' => $saleReturns,
        //     'saleReturns_data' => $return_data
        // ]);
        return view('templates.reports.warehouse.warehouse-purchase-returns', [
            'data' => $data,
            'warehouses' => $warehouses,
            'purchaseReturns' => $purchaseReturns,
            'purchase_return_data' => $purchase_return_data
        ]);
    }
    public function exportwarehousePurchaseReturns(Request $request)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "warehousePurchaseReturns_{$timestamp}.xlsx";

        return Excel::download(new ReportPurchasesReturnWarehouse($request), $filename);
    }
    public function warehouseExpenses(Request $request)
    {
        $saleQuery = Sale::where('deleted_at', '=', null);
        if ($request->filled('warehouse_id')) {
            $saleQuery->where('warehouse_id', '=', $request->input('warehouse_id'));
        }
        $data['sales'] = $saleQuery->count();

        $data['purchases'] = Purchase::where('deleted_at', '=', null);
        if ($request->filled('warehouse_id')) {
            $data['purchases']->where('warehouse_id', '=', $request->input('warehouse_id'));
        }
        $data['purchases'] = $data['purchases']->count();

        $data['ReturnPurchase'] = PurchaseReturn::where('deleted_at', '=', null);
        if ($request->filled('warehouse_id')) {
            $data['ReturnPurchase']->where('warehouse_id', '=', $request->input('warehouse_id'));
        }
        $data['ReturnPurchase'] = $data['ReturnPurchase']->count();

        $data['ReturnSale'] = SaleReturn::where('deleted_at', '=', null);
        if ($request->filled('warehouse_id')) {
            $data['ReturnSale']->where('warehouse_id', '=', $request->input('warehouse_id'));
        }
        $data['ReturnSale'] = $data['ReturnSale']->count();
        $user_auth = auth()->user();
        if ($user_auth->hasAnyRole(['superadmin', 'inventaris'])) {
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
        } else {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');
            $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
        }
        $expenseQuery = Expense::where('deleted_at', '=', null)
            ->with('expense_category', 'warehouse');

        if ($request->filled('warehouse_id')) {
            $expenseQuery->where('warehouse_id', '=', $request->input('warehouse_id'));
        }

        if ($request->filled('search')) {
            $expenseQuery->where(function ($query) use ($request) {
                $query->where('Ref', 'LIKE', '%' . $request->input('search') . '%')
                    ->orWhere('details', 'LIKE', '%' . $request->input('search') . '%')
                    ->orWhere(function ($query) use ($request) {
                        $query->whereHas('expense_category', function ($q) use ($request) {
                            $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                        });
                    });
            });
        }

        $expenses = $expenseQuery->paginate($request->input('limit', 5))->appends($request->except('page'));

        $expenses_data = [];
        foreach ($expenses as $expense) {
            $item = [
                'date' => $expense->date,
                'Ref' => $expense->Ref,
                'details' => $expense->details,
                'amount' => $expense->amount,
                'warehouse_name' => $expense->warehouse->name,
                'category_name' => $expense->expense_category->name,
            ];

            $expenses_data[] = $item;
        }
        return view('templates.reports.warehouse.warehouse-expenses', [
            'data' => $data,
            'warehouses' => $warehouses,
            'expenses' => $expenses,
            'expenses_data' => $expenses_data
        ]);
    }
    public function exportwarehouseExpenses(Request $request)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "warehouseExpenses_{$timestamp}.xlsx";

        return Excel::download(new WarehouseReportExpensesWarehouse($request), $filename);
    }
    //----------------- Warehouse Report-----------------------\\

    //----------------- Sale Report-----------------------\\
    public function sale(request $request)
    {
        $user_auth = auth()->user();
        $saleQuery = Sale::select('sales.*')
            ->with('facture', 'client', 'warehouse')
            ->join('clients', 'sales.client_id', '=', 'clients.id')
            ->where('sales.deleted_at', '=', null)->latest();

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $saleQuery->whereBetween('sales.date', [$request->from_date, $request->to_date]);
        }
        if ($request->filled('search')) {
            $saleQuery->where(function ($query) use ($request) {
                $query->where('Ref', 'LIKE', '%' . $request->input('search') . '%')
                    ->orWhere('statut', 'LIKE', '%' . $request->input('search') . '%')
                    ->orWhere('GrandTotal', $request->input('search'))
                    ->orWhere('payment_statut', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('shipping_status', 'like', '%' . $request->input('search') . '%')
                    ->orWhere(function ($query) use ($request) {
                        $query->whereHas('client', function ($q) use ($request) {
                            $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                        });
                    })
                    ->orWhere(function ($query) use ($request) {
                        $query->whereHas('warehouse', function ($q) use ($request) {
                            $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                        });
                    });
            });
        }
        if ($request->filled('warehouse_id')) {
            $saleQuery->where('warehouse_id', '=', $request->input('warehouse_id'));
        }
        if ($request->filled('client_id')) {
            $saleQuery->where('client_id', '=', $request->input('client_id'));
        }
        if ($request->filled('statut')) {
            $saleQuery->where('statut', '=', $request->input('statut'));
        }
        if ($request->filled('payment_statut')) {
            $saleQuery->where('payment_statut', '=', $request->input('payment_statut'));
        }
        // Filter khusus untuk staff berdasarkan gudang
        if ($user_auth->hasRole('staff')) {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');
            $saleQuery->whereIn('warehouse_id', $warehouses_id);
        }
        $sales = $saleQuery->paginate($request->input('limit', 5))->appends($request->except('page'));
        $totalPaid = $sales->sum('paid_amount');
        $totalAmount = $sales->sum('GrandTotal');
        $totalDue = $totalAmount - $totalPaid;
        $sales_data = [];
        foreach ($sales as $sale) {
            $item = [
                'id' => $sale->id,
                'date' => $sale->date,
                'Ref' => $sale->Ref,
                'statut' => $sale->statut,
                'discount' => $sale->discount,
                'shipping' => $sale->shipping,
                'warehouse_name' => $sale->warehouse->name,
                'client_name' => $sale->client->name,
                'client_email' => $sale->client->email,
                'client_tele' => $sale->client->phone,
                'client_code' => $sale->client->code,
                'client_adr' => $sale->client->adresse,
                'GrandTotal' => $sale->GrandTotal,
                'paid_amount' => $sale->paid_amount,
                'due' => $sale->GrandTotal - $sale->paid_amount,
                'payment_status' => $sale->payment_statut,
            ];

            $sales_data[] = $item;
        }

        $customers = client::where('deleted_at', '=', null)->get(['id', 'name']);

        if ($user_auth->hasAnyRole(['superadmin', 'inventaris'])) {
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
        } else {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');
            $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
        }
        return view('templates.reports.sale', [
            'sales' => $sales,
            'sales_data' => $sales_data,
            'client' => $customers,
            'warehouse' => $warehouses,
            'total_paid' => $totalPaid,
            'total_due' => $totalDue,
            'total_amount' => $totalAmount,
        ]);
    }
    public function saleExport(Request $request)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "report-sales_{$timestamp}.xlsx";

        return Excel::download(new ReportSalesExport($request), $filename);
    }
    //----------------- Sale Report-----------------------\\


    public function purchase(request $request)
    {
        $data = array();

        $PurchasesQuery = Purchase::select('purchases.*')
            ->with('payment_purchases', 'provider', 'warehouse')
            ->join('providers', 'purchases.provider_id', '=', 'providers.id')
            ->where('purchases.deleted_at', '=', null)->latest();
        // ->whereBetween('purchases.date', array($request->from, $request->to));
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $PurchasesQuery->whereBetween('sales.date', [$request->from_date, $request->to_date]);
        }
        if ($request->filled('search')) {
            $PurchasesQuery->where(function ($query) use ($request) {
                $query->where('Ref', 'LIKE', '%' . $request->input('search') . '%')
                    ->orWhere('statut', 'LIKE', '%' . $request->input('search') . '%')
                    ->orWhere('GrandTotal', $request->input('search'))
                    ->orWhere('payment_statut', 'like', '%' . $request->input('search') . '%')
                    ->orWhere(function ($query) use ($request) {
                        $query->whereHas('provider', function ($q) use ($request) {
                            $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                        });
                    })
                    ->orWhere(function ($query) use ($request) {
                        $query->whereHas('warehouse', function ($q) use ($request) {
                            $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
                        });
                    });
            });
        }
        if ($request->filled('warehouse_id')) {
            $PurchasesQuery->where('warehouse_id', '=', $request->input('warehouse_id'));
        }
        if ($request->filled('provider_id')) {
            $PurchasesQuery->where('provider_id', '=', $request->input('provider_id'));
        }
        if ($request->filled('statut')) {
            $PurchasesQuery->where('statut', '=', $request->input('statut'));
        }
        if ($request->filled('payment_statut')) {
            $PurchasesQuery->where('payment_statut', '=', $request->input('payment_statut'));
        }
        $Purchases = $PurchasesQuery->paginate($request->input('limit', 5))->appends($request->except('page'));
        $totalPaid = $Purchases->sum('paid_amount');
        $totalAmount = $Purchases->sum('GrandTotal');
        $totalDue = $totalAmount - $totalPaid;
        $Purchases_data = [];
        foreach ($Purchases as $Purchase) {

            $item['id'] = $Purchase->id;
            $item['date'] = $Purchase->date;
            $item['Ref'] = $Purchase->Ref;
            $item['warehouse_name'] = $Purchase['warehouse']->name;
            $item['discount'] = $Purchase->discount;
            $item['shipping'] = $Purchase->shipping;
            $item['statut'] = $Purchase->statut;
            $item['provider_name'] = $Purchase['provider']->name;
            $item['provider_email'] = $Purchase['provider']->email;
            $item['provider_tele'] = $Purchase['provider']->phone;
            $item['provider_code'] = $Purchase['provider']->code;
            $item['provider_adr'] = $Purchase['provider']->adresse;
            $item['GrandTotal'] = $Purchase['GrandTotal'];
            $item['paid_amount'] = $Purchase['paid_amount'];
            $item['due'] = $Purchase['GrandTotal'] - $Purchase['paid_amount'];
            $item['payment_status'] = $Purchase['payment_statut'];

            $Purchases_data[] = $item;
        }

        $provider = Provider::where('deleted_at', '=', null)->get(['id', 'name']);

        //get warehouses assigned to user
        $user_auth = auth()->user();
        $user_auth = auth()->user();
        if ($user_auth->hasAnyRole(['superadmin', 'inventaris'])) {
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
        } else {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');
            $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
        }
        return view('templates.reports.purchase', [
            'purchases' => $Purchases,
            'purchases_data' => $Purchases_data,
            'provider' => $provider,
            'warehouse' => $warehouses,
            'total_paid' => $totalPaid,
            'total_due' => $totalDue,
            'total_amount' => $totalAmount,
        ]);
    }
    public function exportReportPurchase(Request $request)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "report-purchases_{$timestamp}.xlsx";

        return Excel::download(new ReportPurchasesExport($request), $filename);
    }
}
