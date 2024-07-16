<?php

namespace App\Exports\Report;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\SaleReturn;
use App\Models\PurchaseReturn;
use App\Models\PaymentSale;
use App\Models\PaymentSaleReturns;
use App\Models\PaymentPurchase;
use App\Models\PaymentPurchaseReturns;
use App\Models\Expense;
use App\Models\UserWarehouse;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProfitLossExport implements FromQuery, WithHeadings, WithMapping
{
    private $start_date;
    private $end_date;
    private $warehouse_id;
    private $array_warehouses_id;

    public function __construct($start_date, $end_date, $warehouse_id)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->warehouse_id = $warehouse_id;

        $user_auth = auth()->user();

        if ($user_auth->hasAnyRole(['superadmin', 'inventaris'])) {
            $this->array_warehouses_id = Warehouse::where('deleted_at', '=', null)->pluck('id')->toArray();
        } else {
            $this->array_warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
        }
    }

    public function query()
    {
        return Sale::query()
            ->select(
                DB::raw('SUM(GrandTotal) AS sales_sum'),
                DB::raw("count(*) as sales_count"),
                DB::raw('(SELECT SUM(GrandTotal) FROM purchases WHERE deleted_at IS NULL AND statut = "received" AND date BETWEEN ? AND ? AND (warehouse_id = ? OR warehouse_id IN (?))) as purchases_sum'),
                DB::raw('(SELECT count(*) FROM purchases WHERE deleted_at IS NULL AND statut = "received" AND date BETWEEN ? AND ? AND (warehouse_id = ? OR warehouse_id IN (?))) as purchases_count'),
                DB::raw('(SELECT SUM(GrandTotal) FROM sale_returns WHERE deleted_at IS NULL AND statut = "received" AND date BETWEEN ? AND ? AND (warehouse_id = ? OR warehouse_id IN (?))) as returns_sales_sum'),
                DB::raw('(SELECT count(*) FROM sale_returns WHERE deleted_at IS NULL AND statut = "received" AND date BETWEEN ? AND ? AND (warehouse_id = ? OR warehouse_id IN (?))) as returns_sales_count'),
                DB::raw('(SELECT SUM(GrandTotal) FROM purchase_returns WHERE deleted_at IS NULL AND statut = "completed" AND date BETWEEN ? AND ? AND (warehouse_id = ? OR warehouse_id IN (?))) as returns_purchases_sum'),
                DB::raw('(SELECT count(*) FROM purchase_returns WHERE deleted_at IS NULL AND statut = "completed" AND date BETWEEN ? AND ? AND (warehouse_id = ? OR warehouse_id IN (?))) as returns_purchases_count'),
                DB::raw('(SELECT SUM(montant) FROM payment_sales WHERE deleted_at IS NULL AND date BETWEEN ? AND ? AND (warehouse_id = ? OR warehouse_id IN (?))) as paiement_sales'),
                DB::raw('(SELECT SUM(montant) FROM payment_sale_returns WHERE deleted_at IS NULL AND date BETWEEN ? AND ? AND (warehouse_id = ? OR warehouse_id IN (?))) as PaymentSaleReturns'),
                DB::raw('(SELECT SUM(montant) FROM payment_purchase_returns WHERE deleted_at IS NULL AND date BETWEEN ? AND ? AND (warehouse_id = ? OR warehouse_id IN (?))) as PaymentPurchaseReturns'),
                DB::raw('(SELECT SUM(montant) FROM payment_purchases WHERE deleted_at IS NULL AND date BETWEEN ? AND ? AND (warehouse_id = ? OR warehouse_id IN (?))) as paiement_purchases'),
                DB::raw('(SELECT SUM(amount) FROM expenses WHERE deleted_at IS NULL AND date BETWEEN ? AND ? AND (warehouse_id = ? OR warehouse_id IN (?))) as expenses_sum'),
                DB::raw('(SELECT count(*) FROM expenses WHERE deleted_at IS NULL AND date BETWEEN ? AND ? AND (warehouse_id = ? OR warehouse_id IN (?))) as expenses_count')
            )
            ->where('deleted_at', '=', null)
            ->where('statut', 'completed')
            ->whereBetween('date', [$this->start_date, $this->end_date])
            ->where(function ($query) {
                if ($this->warehouse_id !== 0) {
                    return $query->where('warehouse_id', $this->warehouse_id);
                } else {
                    return $query->whereIn('warehouse_id', $this->array_warehouses_id);
                }
            });
    }

    public function headings(): array
    {
        return [
            'Sales Sum',
            'Sales Count',
            'Purchases Sum',
            'Purchases Count',
            'Returns Sales Sum',
            'Returns Sales Count',
            'Returns Purchases Sum',
            'Returns Purchases Count',
            'Paiement Sales',
            'Payment Sale Returns',
            'Payment Purchase Returns',
            'Paiement Purchases',
            'Expenses Sum',
            'Expenses Count',
        ];
    }

    public function map($row): array
    {
        return [
            'Rp ' . number_format($row->sales_sum, 2, ',', '.'),
            $row->sales_count,
            'Rp ' . number_format($row->purchases_sum, 2, ',', '.'),
            $row->purchases_count,
            'Rp ' . number_format($row->returns_sales_sum, 2, ',', '.'),
            $row->returns_sales_count,
            'Rp ' . number_format($row->returns_purchases_sum, 2, ',', '.'),
            $row->returns_purchases_count,
            'Rp ' . number_format($row->paiement_sales, 2, ',', '.'),
            'Rp ' . number_format($row->PaymentSaleReturns, 2, ',', '.'),
            'Rp ' . number_format($row->PaymentPurchaseReturns, 2, ',', '.'),
            'Rp ' . number_format($row->paiement_purchases, 2, ',', '.'),
            'Rp ' . number_format($row->expenses_sum, 2, ',', '.'),
            $row->expenses_count,
        ];
    }
}
