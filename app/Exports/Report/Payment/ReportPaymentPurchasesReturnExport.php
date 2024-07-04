<?php

namespace App\Exports\Report\Payment;

use App\Models\Sale;
use App\Models\UserWarehouse;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportPaymentPurchasesReturnExport implements FromQuery, WithHeadings, WithMapping
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
        $paymentsQuery =  DB::table('payment_purchase_returns')
            ->whereNull('payment_purchase_returns.deleted_at')
            ->join('purchase_returns', 'payment_purchase_returns.purchase_return_id', '=', 'purchase_returns.id')
            ->join('providers', 'purchase_returns.provider_id', '=', 'providers.id')
            ->select(
                'payment_purchase_returns.date',
                'payment_purchase_returns.Ref AS Payment_Ref',
                'purchase_returns.Ref AS PurchaseReturn_Ref',
                'payment_purchase_returns.Reglement',
                'payment_purchase_returns.montant',
                'providers.name AS provider_name'
            )
            ->latest('payment_purchase_returns.date');

        if (!$user_auth->hasRole(['superadmin', 'inventaris'])) {
            $paymentsQuery->whereIn('purchase_returns.warehouse_id', $warehouses_id);
        }

        // proses filtering
        if ($this->request->has('search') && $this->request->filled('search')) {
            $search = $this->request->input('search');
            $paymentsQuery->where(function ($query) use ($search) {
                $query->orWhere('payment_purchase_returns.Ref', 'LIKE', $search)
                    ->orWhere('providers.name', 'LIKE', $search);
            });
        }

        return $paymentsQuery;
    }

    public function headings(): array
    {
        return [
            'Supplier Name',
            'Date',
            'Reference',
            'Purchase Returns Reference',
            'Montant',
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->provider_name ?? 'deleted',
            $sale->date,
            $sale->Payment_Ref,
            $sale->PurchaseReturn_Ref ?? 'deleted',
            $sale->montant,
        ];
    }
}
