<?php

namespace App\Exports\Report\Payment;

use App\Models\Sale;
use App\Models\UserWarehouse;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportPaymentPurchasesExport implements FromQuery, WithHeadings, WithMapping
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
        $paymentsQuery = DB::table('payment_purchases')
            ->whereNull('payment_purchases.deleted_at')
            ->join('purchases', 'payment_purchases.purchase_id', '=', 'purchases.id')
            ->join('providers', 'purchases.provider_id', '=', 'providers.id')
            ->select(
                'payment_purchases.date',
                'payment_purchases.Ref AS Payment_Ref',
                'purchases.Ref AS Purchase_Ref',
                'payment_purchases.Reglement',
                'payment_purchases.montant',
                'providers.name AS provider_name'
            )
            ->latest('payment_purchases.date');

        if (!$user_auth->hasRole(['superadmin', 'inventaris'])) {
            $paymentsQuery->whereIn('purchases.warehouse_id', $warehouses_id);
        }

        // proses filtering
        if ($this->request->has('search') && $this->request->filled('search')) {
            $search = '%' . $this->request->input('search') . '%';
            $paymentsQuery->where(function ($query) use ($search) {
                $query->orWhere('payment_purchases.Ref', 'LIKE', $search)
                    ->orWhere('providers.name', 'LIKE', $search)
                    ->orWhere('payment_purchases.Reglement', 'LIKE', $search);
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
            'Purchase Reference',
            'Montant',
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->provider_name ?? 'deleted',
            $sale->date,
            $sale->Payment_Ref,
            $sale->Purchase_Ref ?? 'deleted',
            $sale->montant,
        ];
    }
}
