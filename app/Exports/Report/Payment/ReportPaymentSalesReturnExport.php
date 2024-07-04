<?php

namespace App\Exports\Report\Payment;

use App\Models\Sale;
use App\Models\UserWarehouse;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportPaymentSalesReturnExport implements FromQuery, WithHeadings, WithMapping
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
        $paymentsQuery =  DB::table('payment_sale_returns')
            ->where('payment_sale_returns.deleted_at', '=', null)
            ->join('sale_returns', 'payment_sale_returns.sale_return_id', '=', 'sale_returns.id')
            ->join('clients', 'sale_returns.client_id', '=', 'clients.id')
            ->select(
                'payment_sale_returns.date',
                'payment_sale_returns.Ref AS Payment_Ref',
                'sale_returns.Ref AS Sale_Return_Ref',
                'payment_sale_returns.Reglement',
                'payment_sale_returns.montant',
                'clients.name AS client_name'
            )
            ->latest('payment_sale_returns.date');

        if (!$user_auth->hasRole(['superadmin', 'inventaris'])) {
            $paymentsQuery->whereIn('sale_returns.warehouse_id', $warehouses_id);
        }

        // proses filtering
        if ($this->request->has('search') && $this->request->filled('search')) {
            $search = $this->request->input('search');
            $paymentsQuery->where(function ($query) use ($search) {
                $query->orWhere('payment_sale_returns.Ref', 'LIKE', $search)
                    ->orWhere('clients.name', 'LIKE', $search)
                    ->orWhere('payment_sale_returns.Reglement', 'LIKE', $search);
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
            $sale->Sale_Return_Ref ?? 'deleted',
            $sale->montant,
        ];
    }
}
