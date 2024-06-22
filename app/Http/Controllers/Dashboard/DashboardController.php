<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
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
use App\Models\UserWarehouse;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user_auth = auth()->user();
        $warehouse_id = $request->input('warehouse_id', 0); // default all warehouse

        // filtering data untuk staff berdasarkan warehouse_id nya
        if ($user_auth->hasAnyRole(['staff'])) {
            $staff_warehouse_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->first();
            if ($staff_warehouse_id) {
                $warehouse_id = $staff_warehouse_id;
            }
        }
        $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
        $currentMonth = Carbon::now()->format('F Y');

        // Logika pengolahan data Top Customer di bulan ini
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
        if ($warehouse_id != 0) {
            $topClientsQuery->where('sales.warehouse_id', $warehouse_id);
        }
        $topClients = $topClientsQuery->get();

        // Logika pengolahan data Top Selling Product di bulan ini
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

        // Logika pengolahan data Recent sales 
        $saleQuery = Sale::select('sales.*')
            ->with('facture', 'client', 'warehouse')
            ->join('clients', 'sales.client_id', '=', 'clients.id')
            ->where('sales.deleted_at', '=', null)->latest()->take(5); //hanya mengamnil 5 data
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
        // Logika pengolahan data Today Sales di konten slider
        $data['today_sales'] = Sale::where('deleted_at', null)
            ->whereDate('date', Carbon::today())
            ->when($warehouse_id != 0, function ($query) use ($warehouse_id) {
                return $query->where('warehouse_id', $warehouse_id);
            })
            ->sum('GrandTotal');
        $data['today_sales'] = 'Rp ' . number_format($data['today_sales'], 2, ',', '.'); // format Rupiah
        // Logika pengolahan data Today Sales Return di konten slider
        $data['return_sales'] = SaleReturn::where('deleted_at', '=', null)
            ->where('date', \Carbon\Carbon::today())
            ->when($warehouse_id != 0, function ($query) use ($warehouse_id) {
                return $query->where('warehouse_id', $warehouse_id);
            })
            ->sum('GrandTotal');
        $data['return_sales'] = 'Rp ' . number_format($data['return_sales'], 2, ',', '.');
        // Logika pengolahan data Today Purchases di konten slider
        $data['today_purchases'] = Purchase::where('deleted_at', '=', null)
            ->where('date', \Carbon\Carbon::today())
            ->when($warehouse_id != 0, function ($query) use ($warehouse_id) {
                return $query->where('warehouse_id', $warehouse_id);
            })
            ->sum('GrandTotal');
        $data['today_purchases'] = 'Rp ' . number_format($data['today_purchases'], 2, ',', '.');
        // Logika pengolahan data Today Purchases Return di konten slider
        $data['return_purchases'] = PurchaseReturn::where('deleted_at', '=', null)
            ->where('date', \Carbon\Carbon::today())
            ->when($warehouse_id != 0, function ($query) use ($warehouse_id) {
                return $query->where('warehouse_id', $warehouse_id);
            })
            ->sum('GrandTotal');
        $data['return_purchases'] = 'Rp ' . number_format($data['return_purchases'], 2, ',', '.');
        // Logika pengolahan data di This Week Payment Sent & Received
        // Batasi rentang tanggal berdasarkan 6 hari terakhir dari hari ini
        $dates = collect();
        for ($i = -6; $i <= 0; $i++) {
            $date = Carbon::now()->addDays($i)->format('Y-m-d');
            $dates->put($date, 0); // Inisialisasi dengan nilai 0
        }
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

        // Logika pengolahan data di Report Attendance
        $today = Carbon::today()->toDateString();
        $attendanceQuery = Attendance::with('user.warehouses')
            ->where('deleted_at', null)
            ->whereDate('date', Carbon::today())
            ->get();
        $attendance = [];
        foreach ($attendanceQuery as $jadwal) {
            $warehouseNames = $jadwal->user->warehouses->pluck('name')->toArray();
            $warehouseNamesString = implode(' ~ ', $warehouseNames);
            $item = [
                'id' => $jadwal->id,
                'employee_name' => $jadwal->user->username,
                'employee_image' => $jadwal->user->image,
                'warehouse_name' => $warehouseNamesString,
                'status' => $jadwal->status,
                'clock_in' => $jadwal->clock_in,
                'clock_out' => $jadwal->clock_out,
            ];
            $attendance[] = $item;
        }
        // Logika pengolahan data Stock Alert
        $alertStockQuery = ProductWarehouse::with('product', 'warehouse')->where('deleted_at', '=', null)->where('qty', '<=', 'stock_alert')->take(2);
        if ($warehouse_id != 0) {
            $alertStockQuery->where('warehouse_id', '=', $warehouse_id);
        }
        $stock = $alertStockQuery->get();
        $stock_data = [];
        foreach ($stock as $item) {
            $stock_data[] = [
                'id' => $item->id,
                'product_name' => $item->product->name,
                'stock' => $item->qty,
                'alert' => $item->stock_alert,
                'warehouse_name' => $item->warehouse->name,
            ];
        }
        // pengiriman data ke frontend
        return view('templates.dashboard', [
            'topClients' => $topClients,
            'products' => $products,
            'currentMonth' => $currentMonth,
            'recentsales' => $sales_data,
            'report' => $data,
            'payment_sent' => $payment_sent,
            'payment_received' => $payment_received,
            'days' => $days,
            'attendance' => $attendance,
            'today' => $today,
            'stock' => $stock_data,
            'warehouses' => $warehouses,
        ]);
    }
}
