<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\UserWarehouse;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function payments()
    {
        return view('templates.reports.payments');
    }

    public function profitLoss()
    {
        return view('templates.reports.profit-loss');
    }

    public function quantityAlerts()
    {
        return view('templates.reports.quantity-alerts');
    }

    public function stock()
    {
        return view('templates.reports.stock');
    }

    public function stockDetail($id)
    {
        return view('templates.reports.stock-detail');
    }

    //----------------- Customers Report -----------------------\\
    public function customers(Request $request)
    {
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
        foreach ($clients as $client) {
            $item['total_sales'] = DB::table('sales')
                ->where('deleted_at', '=', null)
                ->where('client_id', $client->id)
                ->count();

            $item['total_amount'] = DB::table('sales')
                ->where('deleted_at', '=', null)
                ->where('client_id', $client->id)
                ->sum('GrandTotal');

            $item['total_paid'] = DB::table('sales')
                ->where('sales.deleted_at', '=', null)
                ->where('sales.client_id', $client->id)
                ->sum('paid_amount');

            $item['due'] = $item['total_amount'] - $item['total_paid'];

            $item['total_amount_return'] = DB::table('sale_returns')
                ->where('deleted_at', '=', null)
                ->where('client_id', $client->id)
                ->sum('GrandTotal');

            $item['total_paid_return'] = DB::table('sale_returns')
                ->where('sale_returns.deleted_at', '=', null)
                ->where('sale_returns.client_id', $client->id)
                ->sum('paid_amount');

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
            $totalDue += $item['due'];
            $totalReturnDue += $item['return_Due'];
        }
        return view('templates.reports.customers.customers', [
            'report' => $data,
            'clients' => $clients,
            'total_sales' => $totalSales,
            'total_amount' => $totalAmount,
            'total_paid' => $totalPaid,
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
                    ->orWhere('shipping_status', 'like', '%' . $request->input('search') . '%');
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
    //----------------- Customers Report -----------------------\\
    public function supplier()
    {
        return view('templates.reports.supplier');
    }

    public function supplierDetail($id)
    {
        return view('templates.reports.supplier-detail');
    }

    public function topSellingProduct()
    {
        return view('templates.reports.top-selling-product');
    }

    // public function warehouse()
    // {
    //     return view('templates.reports.warehouse');
    // }
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
                    ->orWhere('statut', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('GrandTotal', $request->input('search'))
                    ->orWhere('payment_statut', 'like', '%' . $request->input('search') . '%')
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
    // public function warehouseSalesReturns(request $request)
    // {
    //     $saleQuery = Sale::where('deleted_at', '=', null);
    //     if ($request->filled('warehouse_id')) {
    //         $saleQuery->where('warehouse_id', '=', $request->input('warehouse_id'));
    //     }
    //     $data['sales'] = $saleQuery->count();

    //     $data['purchases'] = Purchase::where('deleted_at', '=', null);
    //     if ($request->filled('warehouse_id')) {
    //         $data['purchases']->where('warehouse_id', '=', $request->input('warehouse_id'));
    //     }
    //     $data['purchases'] = $data['purchases']->count();

    //     $data['ReturnPurchase'] = PurchaseReturn::where('deleted_at', '=', null);
    //     if ($request->filled('warehouse_id')) {
    //         $data['ReturnPurchase']->where('warehouse_id', '=', $request->input('warehouse_id'));
    //     }
    //     $data['ReturnPurchase'] = $data['ReturnPurchase']->count();

    //     $data['ReturnSale'] = SaleReturn::where('deleted_at', '=', null);
    //     if ($request->filled('warehouse_id')) {
    //         $data['ReturnSale']->where('warehouse_id', '=', $request->input('warehouse_id'));
    //     }
    //     $data['ReturnSale'] = $data['ReturnSale']->count();

    //     $user_auth = auth()->user();
    //     if ($user_auth->hasAnyRole(['superadmin', 'inventaris'])) {
    //         $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
    //     } else {
    //         $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');
    //         $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
    //     }
    //     // 
    //     $data = array();
    //     $SaleReturn = SaleReturn::where('deleted_at', '=', null)
    //         ->with('sale', 'client', 'warehouse')
    //         ->where(function ($query) use ($request) {
    //             return $query->when($request->filled('warehouse_id'), function ($query) use ($request) {
    //                 return $query->where('warehouse_id', $request->warehouse_id);
    //             });
    //         })
    //         //Search With Multiple Param
    //         ->where(function ($query) use ($request) {
    //             return $query->when($request->filled('search'), function ($query) use ($request) {
    //                 return $query->where('Ref', 'LIKE', "%{$request->search}%")
    //                     ->orWhere('statut', 'LIKE', "%{$request->search}%")
    //                     ->orWhere('GrandTotal', $request->search)
    //                     ->orWhere('payment_statut', 'like', "$request->search")

    //                     ->orWhere(function ($query) use ($request) {
    //                         return $query->whereHas('sale', function ($q) use ($request) {
    //                             $q->where('Ref', 'LIKE', "%{$request->search}%");
    //                         });
    //                     })
    //                     ->orWhere(function ($query) use ($request) {
    //                         return $query->whereHas('client', function ($q) use ($request) {
    //                             $q->where('name', 'LIKE', "%{$request->search}%");
    //                         });
    //                     });
    //             });
    //         });
    //     foreach ($SaleReturn as $Sale_Return) {
    //         $item['id'] = $Sale_Return->id;
    //         $item['warehouse_name'] = $Sale_Return['warehouse']->name;
    //         $item['Ref'] = $Sale_Return->Ref;
    //         $item['statut'] = $Sale_Return->statut;
    //         $item['client_name'] = $Sale_Return['client']->name;
    //         $item['sale_ref'] = $Sale_Return['sale'] ? $Sale_Return['sale']->Ref : '---';
    //         $item['sale_id'] = $Sale_Return['sale'] ? $Sale_Return['sale']->id : NULL;
    //         $item['GrandTotal'] = $Sale_Return->GrandTotal;
    //         $item['paid_amount'] = $Sale_Return->paid_amount;
    //         $item['due'] = $Sale_Return->GrandTotal - $Sale_Return->paid_amount;
    //         $item['payment_status'] = $Sale_Return->payment_statut;

    //         $data[] = $item;
    //     }
    //     return view('templates.reports.warehouse.warehouse-sales-returns', [
    //         'data' => $data,

    //         'warehouses' => $warehouses,
    //     ]);
    // }
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
                    ->orWhere('statut', 'LIKE', '%' . $request->input('search') . '%')
                    ->orWhere('GrandTotal', $request->input('search'))
                    ->orWhere('payment_statut', 'like', '%' . $request->input('search') . '%')
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

        $saleReturns = $saleReturnQuery->paginate($request->input('limit', 1))->appends($request->except('page'));

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
                    ->orWhere('statut', 'LIKE', '%' . $request->input('search') . '%')
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

    //----------------- Warehouse Report-----------------------\\
    public function sale()
    {
        return view('templates.reports.sale');
    }

    public function purchase()
    {
        return view('templates.reports.purchase');
    }
}
