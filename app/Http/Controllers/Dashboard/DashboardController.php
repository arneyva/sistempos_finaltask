<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleDetail;
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
        // return response()->json([
        //     'topClients' => $topClients,
        //     'products' => $products,
        //     'recentsales' => $sales_data,
        //     'currentMonth' => $currentMonth,

        // ]);
        return view('templates.dashboard', [
            'topClients' => $topClients,
            'products' => $products,
            'currentMonth' => $currentMonth,
            'recentsales' => $sales_data,
        ]);
    }
}
