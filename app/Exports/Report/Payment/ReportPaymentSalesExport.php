<?php

namespace App\Exports\Report\Payment;

use App\Models\Sale;
use App\Models\UserWarehouse;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportPaymentSalesExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $request;


    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $user_auth = auth()->user();
        $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');

        // mendapatkan data payment
        $paymentsQuery = DB::table('payment_sales')
            ->whereNull('payment_sales.deleted_at')
            ->join('sales', 'payment_sales.sale_id', '=', 'sales.id')
            ->join('clients', 'sales.client_id', '=', 'clients.id')
            ->select(
                'payment_sales.date',
                'payment_sales.Ref AS Payment_Ref',
                'sales.Ref AS Sale_Ref',
                'payment_sales.Reglement',
                'payment_sales.montant',
                'clients.name AS client_name'
            )
            ->latest('payment_sales.date');

        if (!$user_auth->hasRole(['superadmin', 'inventaris'])) {
            $paymentsQuery->whereIn('sales.warehouse_id', $warehouses_id);
        }

        // proses filtering
        if ($this->request->has('search') && $this->request->filled('search')) {
            $search = $this->request->input('search');
            $paymentsQuery->where(function ($query) use ($search) {
                $query->orWhere('payment_sales.Ref', 'LIKE', '%' . $search . '%')
                    ->orWhere('clients.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('payment_sales.Reglement', 'LIKE', '%' . $search . '%');
            });
        }

        return $paymentsQuery;
    }

    public function headings(): array
    {
        return [
            'Client Name',
            'Date',
            'Reference',
            'Sale Reference',
            'Montant',
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->client_name ?? 'deleted',
            $sale->date,
            $sale->Payment_Ref,
            $sale->Sale_Ref ?? 'deleted',
            $sale->montant,
        ];
    }
}
