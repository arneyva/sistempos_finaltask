<?php

namespace App\Exports\Report\Provider;

use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportProviderPurchasesPaymentExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $request;
    protected $id;

    public function __construct($request, $id)
    {
        $this->request = $request;
        $this->id = $id;
    }

    public function query()
    {
        $paymentsQuery = DB::table('payment_purchases')
            ->where('payment_purchases.deleted_at', '=', null)
            ->join('purchases', 'payment_purchases.purchase_id', '=', 'purchases.id')
            ->where('purchases.provider_id', $this->id)
            ->latest('payment_purchases.date');

        if ($this->request->filled('search')) {
            $paymentsQuery->where(function ($query) {
                $query->orWhere('payment_purchases.Ref', 'LIKE', '%' . $this->request->input('search') . '%')
                    ->orWhere('payment_purchases.date', 'LIKE', '%' . $this->request->input('search') . '%')
                    ->orWhere('payment_purchases.Reglement', 'LIKE', '%' . $this->request->input('search') . '%');
            });
        }

        return $paymentsQuery->select(
            'payment_purchases.id',
            'payment_purchases.date',
            'payment_purchases.Ref AS Ref',
            'purchases.Ref AS purchase_Ref',
            'payment_purchases.Reglement',
            'payment_purchases.montant'
        );
    }

    public function headings(): array
    {
        return [
            'ID',
            'Date',
            'Reference',
            'Purchase Reference',
            'Reglement',
            'Montant',
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->id,
            $payment->date,
            $payment->Ref,
            $payment->purchase_Ref,
            $payment->Reglement == 'cash' ? 'Cash' : ($payment->Reglement == 'pending' ? 'Pending' : 'Via Midtrans'),
            $payment->montant,
        ];
    }
}
