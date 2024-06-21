<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\PaymentPurchase;
use App\Models\PaymentPurchaseReturns;
use App\Models\PaymentSale;
use App\Models\PaymentSaleReturns;
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
    public function index1(Request $request)
    {
        // Inisialisasi tanggal-tanggal yang akan digunakan
        $dates = collect();
        for ($i = -6; $i <= 0; $i++) {
            $date = Carbon::now()->addDays($i)->format('Y-m-d');
            $dates->put($date, 0); // Inisialisasi dengan nilai 0
        }

        // Batasi rentang tanggal berdasarkan 6 hari terakhir dari hari ini
        $date_range = Carbon::today()->subDays(6);

        // Query untuk Payment_Sale
        $Payment_Sale = PaymentSale::where('date', '>=', $date_range)
            ->when($request->warehouse_id != 0, function ($query) use ($request) {
                $query->whereHas('sale', function ($q) use ($request) {
                    $q->where('warehouse_id', $request->warehouse_id);
                });
            })
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get([
                'date',
                \DB::raw('SUM(montant) AS count'),
            ])
            ->pluck('count', 'date');

        // Query untuk Payment_Sale_Returns
        $Payment_Sale_Returns = PaymentSaleReturns::where('date', '>=', $date_range)
            ->when($request->warehouse_id != 0, function ($query) use ($request) {
                $query->whereHas('SaleReturn', function ($q) use ($request) {
                    $q->where('warehouse_id', $request->warehouse_id);
                });
            })
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get([
                'date',
                \DB::raw('SUM(montant) AS count'),
            ])
            ->pluck('count', 'date');

        // Query untuk Payment_Purchases
        $Payment_Purchases = PaymentPurchase::where('date', '>=', $date_range)
            ->when($request->warehouse_id != 0, function ($query) use ($request) {
                $query->whereHas('purchase', function ($q) use ($request) {
                    $q->where('warehouse_id', $request->warehouse_id);
                });
            })
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get([
                'date',
                \DB::raw('SUM(montant) AS count'),
            ])
            ->pluck('count', 'date');

        // Query untuk Payment_Purchase_Returns
        $Payment_Purchase_Returns = PaymentPurchaseReturns::where('date', '>=', $date_range)
            ->when($request->warehouse_id != 0, function ($query) use ($request) {
                $query->whereHas('PurchaseReturn', function ($q) use ($request) {
                    $q->where('warehouse_id', $request->warehouse_id);
                });
            })
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get([
                'date',
                \DB::raw('SUM(montant) AS count'),
            ])
            ->pluck('count', 'date');

        // Query untuk Payment_Expense
        $Payment_Expense = Expense::where('date', '>=', $date_range)
            ->when($request->warehouse_id != 0, function ($query) use ($request) {
                $query->where('warehouse_id', $request->warehouse_id);
            })
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get([
                'date',
                \DB::raw('SUM(amount) AS count'),
            ])
            ->pluck('count', 'date');

        // Gabungkan hasil dari semua query untuk membangun data yang diperlukan
        $dates->transform(function ($value, $date) use ($Payment_Sale, $Payment_Sale_Returns, $Payment_Purchases, $Payment_Purchase_Returns, $Payment_Expense) {
            return [
                'payment_received' => $Payment_Sale->get($date, 0) + $Payment_Purchase_Returns->get($date, 0),
                'payment_sent' => $Payment_Purchases->get($date, 0) + $Payment_Sale_Returns->get($date, 0) + $Payment_Expense->get($date, 0),
            ];
        });

        // Mendapatkan array data yang sesuai format yang diinginkan
        $payment_received = $dates->pluck('payment_received')->toArray();
        $payment_sent = $dates->pluck('payment_sent')->toArray();
        $days = $dates->keys()->toArray();

        // Mengembalikan response JSON
        return response()->json([
            'payment_sent' => $payment_sent,
            'payment_received' => $payment_received,
            'days' => $days,
        ]);
    }
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
        // ===============================================
        // Inisialisasi tanggal-tanggal yang akan digunakan
        $dates = collect();
        for ($i = -6; $i <= 0; $i++) {
            $date = Carbon::now()->addDays($i)->format('Y-m-d');
            $dates->put($date, 0); // Inisialisasi dengan nilai 0
        }

        // Batasi rentang tanggal berdasarkan 6 hari terakhir dari hari ini
        $date_range = Carbon::today()->subDays(6);

        // Query untuk Payment_Sale
        $Payment_Sale = PaymentSale::where('date', '>=', $date_range)
            ->when($request->warehouse_id != 0, function ($query) use ($request) {
                $query->whereHas('sale', function ($q) use ($request) {
                    $q->where('warehouse_id', $request->warehouse_id);
                });
            })
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get([
                'date',
                \DB::raw('SUM(montant) AS count'),
            ])
            ->pluck('count', 'date');

        // Query untuk Payment_Sale_Returns
        $Payment_Sale_Returns = PaymentSaleReturns::where('date', '>=', $date_range)
            ->when($request->warehouse_id != 0, function ($query) use ($request) {
                $query->whereHas('SaleReturn', function ($q) use ($request) {
                    $q->where('warehouse_id', $request->warehouse_id);
                });
            })
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get([
                'date',
                \DB::raw('SUM(montant) AS count'),
            ])
            ->pluck('count', 'date');

        // Query untuk Payment_Purchases
        $Payment_Purchases = PaymentPurchase::where('date', '>=', $date_range)
            ->when($request->warehouse_id != 0, function ($query) use ($request) {
                $query->whereHas('purchase', function ($q) use ($request) {
                    $q->where('warehouse_id', $request->warehouse_id);
                });
            })
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get([
                'date',
                \DB::raw('SUM(montant) AS count'),
            ])
            ->pluck('count', 'date');

        // Query untuk Payment_Purchase_Returns
        $Payment_Purchase_Returns = PaymentPurchaseReturns::where('date', '>=', $date_range)
            ->when($request->warehouse_id != 0, function ($query) use ($request) {
                $query->whereHas('PurchaseReturn', function ($q) use ($request) {
                    $q->where('warehouse_id', $request->warehouse_id);
                });
            })
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get([
                'date',
                \DB::raw('SUM(montant) AS count'),
            ])
            ->pluck('count', 'date');

        // Query untuk Payment_Expense
        $Payment_Expense = Expense::where('date', '>=', $date_range)
            ->when($request->warehouse_id != 0, function ($query) use ($request) {
                $query->where('warehouse_id', $request->warehouse_id);
            })
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get([
                'date',
                \DB::raw('SUM(amount) AS count'),
            ])
            ->pluck('count', 'date');

        // Gabungkan hasil dari semua query untuk membangun data yang diperlukan
        $dates->transform(function ($value, $date) use ($Payment_Sale, $Payment_Sale_Returns, $Payment_Purchases, $Payment_Purchase_Returns, $Payment_Expense) {
            return [
                'payment_received' => $Payment_Sale->get($date, 0) + $Payment_Purchase_Returns->get($date, 0),
                'payment_sent' => $Payment_Purchases->get($date, 0) + $Payment_Sale_Returns->get($date, 0) + $Payment_Expense->get($date, 0),
            ];
        });

        // Mendapatkan array data yang sesuai format yang diinginkan
        $payment_received = $dates->pluck('payment_received')->toArray();
        $payment_sent = $dates->pluck('payment_sent')->toArray();
        $days = $dates->keys()->toArray();
        return view('templates.dashboard', [
            'topClients' => $topClients,
            'products' => $products,
            'currentMonth' => $currentMonth,
            'recentsales' => $sales_data,
            'report' => $data,
            'payment_sent' => $payment_sent,
            'payment_received' => $payment_received,
            'days' => $days,
        ]);
    }
    // public function Payment_chart($warehouse_id, $array_warehouses_id)
    // {
    //     // Build an array of the dates we want to show, oldest first
    //     $dates = collect();
    //     foreach (range(-6, 0) as $i) {
    //         $date = Carbon::now()->addDays($i)->format('Y-m-d');
    //         $dates->put($date, 0);
    //     }

    //     $date_range = \Carbon\Carbon::today()->subDays(6);
    //     // Get the sales counts
    //     $Payment_Sale = PaymentSale::with('sale')->where('date', '>=', $date_range)
    //         ->when($warehouse_id != 0, function ($query) use ($warehouse_id) {
    //             return $query->whereHas('sale', function ($q) use ($warehouse_id) {
    //                 $q->where('warehouse_id', $warehouse_id);
    //             });
    //         }, function ($query) use ($array_warehouses_id) {
    //             return $query->whereHas('sale', function ($q) use ($array_warehouses_id) {
    //                 $q->whereIn('warehouse_id', $array_warehouses_id);
    //             });
    //         })
    //         ->groupBy(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"))
    //         ->orderBy('date', 'asc')
    //         ->get([
    //             DB::raw(DB::raw("DATE_FORMAT(date,'%Y-%m-%d') as date")),
    //             DB::raw('SUM(montant) AS count'),
    //         ])
    //         ->pluck('count', 'date');

    //     $Payment_Sale_Returns = PaymentSaleReturns::with('SaleReturn')->where('date', '>=', $date_range)
    //         ->when($warehouse_id != 0, function ($query) use ($warehouse_id) {
    //             return $query->whereHas('SaleReturn', function ($q) use ($warehouse_id) {
    //                 $q->where('warehouse_id', $warehouse_id);
    //             });
    //         }, function ($query) use ($array_warehouses_id) {
    //             return $query->whereHas('SaleReturn', function ($q) use ($array_warehouses_id) {
    //                 $q->whereIn('warehouse_id', $array_warehouses_id);
    //             });
    //         })
    //         ->groupBy(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"))
    //         ->orderBy('date', 'asc')
    //         ->get([
    //             DB::raw(DB::raw("DATE_FORMAT(date,'%Y-%m-%d') as date")),
    //             DB::raw('SUM(montant) AS count'),
    //         ])
    //         ->pluck('count', 'date');

    //     $Payment_Purchases = PaymentPurchase::with('purchase')->where('date', '>=', $date_range)
    //         ->when($warehouse_id != 0, function ($query) use ($warehouse_id) {
    //             return $query->whereHas('purchase', function ($q) use ($warehouse_id) {
    //                 $q->where('warehouse_id', $warehouse_id);
    //             });
    //         }, function ($query) use ($array_warehouses_id) {
    //             return $query->whereHas('purchase', function ($q) use ($array_warehouses_id) {
    //                 $q->whereIn('warehouse_id', $array_warehouses_id);
    //             });
    //         })
    //         ->groupBy(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"))
    //         ->orderBy('date', 'asc')
    //         ->get([
    //             DB::raw(DB::raw("DATE_FORMAT(date,'%Y-%m-%d') as date")),
    //             DB::raw('SUM(montant) AS count'),
    //         ])
    //         ->pluck('count', 'date');

    //     $Payment_Purchase_Returns = PaymentPurchaseReturns::with('PurchaseReturn')->where('date', '>=', $date_range)
    //         ->when($warehouse_id != 0, function ($query) use ($warehouse_id) {
    //             return $query->whereHas('PurchaseReturn', function ($q) use ($warehouse_id) {
    //                 $q->where('warehouse_id', $warehouse_id);
    //             });
    //         }, function ($query) use ($array_warehouses_id) {
    //             return $query->whereHas('PurchaseReturn', function ($q) use ($array_warehouses_id) {
    //                 $q->whereIn('warehouse_id', $array_warehouses_id);
    //             });
    //         })
    //         ->groupBy(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"))
    //         ->orderBy('date', 'asc')
    //         ->get([
    //             DB::raw(DB::raw("DATE_FORMAT(date,'%Y-%m-%d') as date")),
    //             DB::raw('SUM(montant) AS count'),
    //         ])
    //         ->pluck('count', 'date');

    //     $Payment_Expense = Expense::where('date', '>=', $date_range)
    //         ->when($warehouse_id != 0, function ($query) use ($warehouse_id) {
    //             return $query->where('warehouse_id', $warehouse_id);
    //         })
    //         ->groupBy(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"))
    //         ->orderBy('date', 'asc')
    //         ->get([
    //             DB::raw(DB::raw("DATE_FORMAT(date,'%Y-%m-%d') as date")),
    //             DB::raw('SUM(amount) AS count'),
    //         ])
    //         ->pluck('count', 'date');

    //     $paymen_recieved = $this->array_merge_numeric_values($Payment_Sale, $Payment_Purchase_Returns);
    //     $payment_sent = $this->array_merge_numeric_values($Payment_Purchases, $Payment_Sale_Returns, $Payment_Expense);

    //     $dates_recieved = $dates->merge($paymen_recieved);
    //     $dates_sent = $dates->merge($payment_sent);

    //     $data_recieved = [];
    //     $data_sent = [];
    //     $days = [];
    //     foreach ($dates_recieved as $key => $value) {
    //         $data_recieved[] = $value;
    //         $days[] = $key;
    //     }

    //     foreach ($dates_sent as $key => $value) {
    //         $data_sent[] = $value;
    //     }

    //     return response()->json([
    //         'payment_sent' => $data_sent,
    //         'payment_received' => $data_recieved,
    //         'days' => $days,
    //     ]);
    // }
    public function array_merge_numeric_values()
    {
        $arrays = func_get_args();
        $merged = array();
        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (!is_numeric($value)) {
                    continue;
                }
                if (!isset($merged[$key])) {
                    $merged[$key] = $value;
                } else {
                    $merged[$key] += $value;
                }
            }
        }
        return $merged;
    }
}
