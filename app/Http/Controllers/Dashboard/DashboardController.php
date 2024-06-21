<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ProductWarehouse;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\SaleReturn;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil warehouse_id dari request atau menggunakan nilai default 0 untuk semua warehouse
        $warehouse_id = $request->input('warehouse_id', 0);
        $currentMonth = Carbon::now()->format('F Y');

        // Query untuk mendapatkan top 5 pelanggan berdasarkan jumlah penjualan
        $topClientsQuery = Sale::whereBetween('date', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth(),
        ])
            ->whereNull('sales.deleted_at')
            ->join('clients', 'sales.client_id', '=', 'clients.id')
            ->select(DB::raw('clients.name'), DB::raw('count(*) as sales_count'))
            ->groupBy('clients.name')
            ->orderByDesc('sales_count')
            ->take(5);

        // Jika warehouse_id tidak 0, tambahkan kondisi filter berdasarkan warehouse_id
        if ($warehouse_id != 0) {
            $topClientsQuery->where('sales.warehouse_id', $warehouse_id);
        }
        $topClients = $topClientsQuery->get();

        // Query untuk mendapatkan top 5 produk berdasarkan jumlah penjualannya
        $productsQuery = SaleDetail::join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->whereBetween('sale_details.date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])
            ->where(function ($query) use ($warehouse_id) {
                if ($warehouse_id !== 0) {
                    $query->where('sales.warehouse_id', $warehouse_id);
                }
            })
            ->select(
                DB::raw('products.name as name'),
                DB::raw('count(*) as value')
            )
            ->groupBy('products.name')
            ->orderByDesc('value')
            ->take(5);
        if ($warehouse_id != 0) {
            $productsQuery->where('sales.warehouse_id', $warehouse_id);
        }
        $products = $productsQuery->get();

        // Recent sales
        $saleQuery = Sale::select('sales.*')
            ->with('facture', 'client', 'warehouse')
            ->join('clients', 'sales.client_id', '=', 'clients.id')
            ->where('sales.deleted_at', '=', null)->latest()->take(5);
        if ($warehouse_id != 0) {
            $saleQuery->where('warehouse_id', '=', $warehouse_id);
        }
        $sales = $saleQuery->get();
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
        //  Today Sales
        $data['today_sales'] = Sale::where('deleted_at', null)
            ->whereDate('date', Carbon::today())
            ->when($warehouse_id != 0, function ($query) use ($warehouse_id) {
                return $query->where('warehouse_id', $warehouse_id);
            })
            ->sum('GrandTotal');
        $data['today_sales'] = 'Rp ' . number_format($data['today_sales'], 2, ',', '.');
        //Roday Sales Returns
        $data['return_sales'] = SaleReturn::where('deleted_at', '=', null)
            ->where('date', \Carbon\Carbon::today())
            ->when($warehouse_id != 0, function ($query) use ($warehouse_id) {
                return $query->where('warehouse_id', $warehouse_id);
            })
            ->sum('GrandTotal');

        $data['return_sales'] = 'Rp ' . number_format($data['return_sales'], 2, ',', '.');
        //Roday Sales Returns
        $data['today_purchases'] = Purchase::where('deleted_at', '=', null)
            ->where('date', \Carbon\Carbon::today())
            ->when($warehouse_id != 0, function ($query) use ($warehouse_id) {
                return $query->where('warehouse_id', $warehouse_id);
            })
            ->sum('GrandTotal');

        $data['today_purchases'] = 'Rp ' . number_format($data['today_purchases'], 2, ',', '.');
        //Roday Sales Returns
        $data['return_purchases'] = PurchaseReturn::where('deleted_at', '=', null)
            ->where('date', \Carbon\Carbon::today())
            ->when($warehouse_id != 0, function ($query) use ($warehouse_id) {
                return $query->where('warehouse_id', $warehouse_id);
            })
            ->sum('GrandTotal');

        $data['return_purchases'] = 'Rp ' . number_format($data['return_purchases'], 2, ',', '.');
        // return response()->json([
        //     'topClients' => $topClients,
        //     'products' => $products,
        //     'recentsales' => $sales_data,
        //     'currentMonth' => $currentMonth,
        //     'report' => $data,
        // ]);
        return view('templates.dashboard', [
            'topClients' => $topClients,
            'products' => $products,
            'currentMonth' => $currentMonth,
            'recentsales' => $sales_data,
            'report' => $data,
        ]);
    }
    // public function report_dashboard($warehouse_id, $array_warehouses_id)
    // {
    //     // top selling product this month
    //     // $products = SaleDetail::join('sales', 'sale_details.sale_id', '=', 'sales.id')
    //         ->join('products', 'sale_details.product_id', '=', 'products.id')
    //         ->whereBetween('sale_details.date', [
    //             Carbon::now()->startOfMonth(),
    //             Carbon::now()->endOfMonth(),
    //         ])
    //         ->where(function ($query) use ($view_records) {
    //             if (!$view_records) {
    //                 return $query->where('sales.user_id', '=', Auth::user()->id);
    //             }
    //         })
    //         ->where(function ($query) use ($warehouse_id, $array_warehouses_id) {
    //             if ($warehouse_id !== 0) {
    //                 return $query->where('sales.warehouse_id', $warehouse_id);
    //             } else {
    //                 return $query->whereIn('sales.warehouse_id', $array_warehouses_id);
    //             }
    //         })
    //         ->select(
    //             DB::raw('products.name as name'),
    //             DB::raw('count(*) as total_sales'),
    //             DB::raw('sum(total) as total'),
    //         )
    //         ->groupBy('products.name')
    //         ->orderBy('total_sales', 'desc')
    //         ->take(5)
    //         ->get();

    //     // Stock Alerts
    //     $product_warehouse_data = ProductWarehouse::with('warehouse', 'product', 'productVariant')
    //         ->join('products', 'product_warehouse.product_id', '=', 'products.id')
    //         ->where('manage_stock', true)
    //         ->whereRaw('qty <= stock_alert')
    //         ->where('product_warehouse.deleted_at', null)
    //         ->where(function ($query) use ($warehouse_id, $array_warehouses_id) {
    //             if ($warehouse_id !== 0) {
    //                 return $query->where('product_warehouse.warehouse_id', $warehouse_id);
    //             } else {
    //                 return $query->whereIn('product_warehouse.warehouse_id', $array_warehouses_id);
    //             }
    //         })

    //         ->take('5')->get();

    //     $stock_alert = [];
    //     if ($product_warehouse_data->isNotEmpty()) {

    //         foreach ($product_warehouse_data as $product_warehouse) {
    //             if ($product_warehouse->qte <= $product_warehouse['product']->stock_alert) {
    //                 if ($product_warehouse->product_variant_id !== null) {
    //                     $item['code'] = $product_warehouse['productVariant']->name . '-' . $product_warehouse['product']->code;
    //                 } else {
    //                     $item['code'] = $product_warehouse['product']->code;
    //                 }
    //                 $item['quantity'] = $product_warehouse->qte;
    //                 $item['name'] = $product_warehouse['product']->name;
    //                 $item['warehouse'] = $product_warehouse['warehouse']->name;
    //                 $item['stock_alert'] = $product_warehouse['product']->stock_alert;
    //                 $stock_alert[] = $item;
    //             }
    //         }
    //     }

    //     //---------------- sales

    //     $data['today_sales'] = Sale::where('deleted_at', '=', null)
    //         ->where('date', \Carbon\Carbon::today())
    //         ->where(function ($query) use ($view_records) {
    //             if (!$view_records) {
    //                 return $query->where('user_id', '=', Auth::user()->id);
    //             }
    //         })
    //         ->where(function ($query) use ($warehouse_id, $array_warehouses_id) {
    //             if ($warehouse_id !== 0) {
    //                 return $query->where('warehouse_id', $warehouse_id);
    //             } else {
    //                 return $query->whereIn('warehouse_id', $array_warehouses_id);
    //             }
    //         })
    //         ->get(DB::raw('SUM(GrandTotal)  As sum'))
    //         ->first()->sum;

    //     $data['today_sales'] = number_format($data['today_sales'], 2, '.', ',');


    //     //--------------- return_sales

    //     $data['return_sales'] = SaleReturn::where('deleted_at', '=', null)
    //         ->where('date', \Carbon\Carbon::today())
    //         ->where(function ($query) use ($view_records) {
    //             if (!$view_records) {
    //                 return $query->where('user_id', '=', Auth::user()->id);
    //             }
    //         })
    //         ->where(function ($query) use ($warehouse_id, $array_warehouses_id) {
    //             if ($warehouse_id !== 0) {
    //                 return $query->where('warehouse_id', $warehouse_id);
    //             } else {
    //                 return $query->whereIn('warehouse_id', $array_warehouses_id);
    //             }
    //         })
    //         ->get(DB::raw('SUM(GrandTotal)  As sum'))
    //         ->first()->sum;

    //     $data['return_sales'] = number_format($data['return_sales'], 2, '.', ',');

    //     //------------------- purchases

    //     $data['today_purchases'] = Purchase::where('deleted_at', '=', null)
    //         ->where('date', \Carbon\Carbon::today())
    //         ->where(function ($query) use ($view_records) {
    //             if (!$view_records) {
    //                 return $query->where('user_id', '=', Auth::user()->id);
    //             }
    //         })
    //         ->where(function ($query) use ($warehouse_id, $array_warehouses_id) {
    //             if ($warehouse_id !== 0) {
    //                 return $query->where('warehouse_id', $warehouse_id);
    //             } else {
    //                 return $query->whereIn('warehouse_id', $array_warehouses_id);
    //             }
    //         })
    //         ->get(DB::raw('SUM(GrandTotal)  As sum'))
    //         ->first()->sum;

    //     $data['today_purchases'] = number_format($data['today_purchases'], 2, '.', ',');

    //     //------------------------- return_purchases

    //     $data['return_purchases'] = PurchaseReturn::where('deleted_at', '=', null)
    //         ->where('date', \Carbon\Carbon::today())
    //         ->where(function ($query) use ($view_records) {
    //             if (!$view_records) {
    //                 return $query->where('user_id', '=', Auth::user()->id);
    //             }
    //         })
    //         ->where(function ($query) use ($warehouse_id, $array_warehouses_id) {
    //             if ($warehouse_id !== 0) {
    //                 return $query->where('warehouse_id', $warehouse_id);
    //             } else {
    //                 return $query->whereIn('warehouse_id', $array_warehouses_id);
    //             }
    //         })
    //         ->get(DB::raw('SUM(GrandTotal)  As sum'))
    //         ->first()->sum;

    //     $data['return_purchases'] = number_format($data['return_purchases'], 2, '.', ',');

    //     $last_sales = [];

    //     //last sales
    //     $Sales = Sale::with('details', 'client', 'facture', 'warehouse')->where('deleted_at', '=', null)
    //         ->where(function ($query) use ($view_records) {
    //             if (!$view_records) {
    //                 return $query->where('user_id', '=', Auth::user()->id);
    //             }
    //         })
    //         ->where(function ($query) use ($warehouse_id, $array_warehouses_id) {
    //             if ($warehouse_id !== 0) {
    //                 return $query->where('warehouse_id', $warehouse_id);
    //             } else {
    //                 return $query->whereIn('warehouse_id', $array_warehouses_id);
    //             }
    //         })
    //         ->orderBy('id', 'desc')
    //         ->take(5)
    //         ->get();

    //     foreach ($Sales as $Sale) {

    //         $item_sale['Ref'] = $Sale['Ref'];
    //         $item_sale['statut'] = $Sale['statut'];
    //         $item_sale['client_name'] = $Sale['client']['name'];
    //         $item_sale['warehouse_name'] = $Sale['warehouse']['name'];
    //         $item_sale['GrandTotal'] = $Sale['GrandTotal'];
    //         $item_sale['paid_amount'] = $Sale['paid_amount'];
    //         $item_sale['due'] = $Sale['GrandTotal'] - $Sale['paid_amount'];
    //         $item_sale['payment_status'] = $Sale['payment_statut'];

    //         $last_sales[] = $item_sale;
    //     }

    //     return response()->json([
    //         'products' => $products,
    //         'stock_alert' => $stock_alert,
    //         'report' => $data,
    //         'last_sales' => $last_sales,
    //     ]);
    // }
}
