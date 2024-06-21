<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Sale;
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
            ->where('sales.deleted_at', '=', null)
            ->join('clients', 'sales.client_id', '=', 'clients.id')
            ->select(DB::raw('clients.name'), DB::raw("count(*) as sales_count"))
            ->groupBy('clients.name')
            ->orderBy('sales_count', 'desc')
            ->take(5);

        // Jika warehouse_id tidak 0, tambahkan kondisi filter berdasarkan warehouse_id
        if ($warehouse_id != 0) {
            $topClientsQuery->where('sales.warehouse_id', $warehouse_id);
        }
        $topClients = $topClientsQuery->get();
        return view('templates.dashboard', [
            'topClients' => $topClients,
            'currentMonth' => $currentMonth
        ]);
    }
}
