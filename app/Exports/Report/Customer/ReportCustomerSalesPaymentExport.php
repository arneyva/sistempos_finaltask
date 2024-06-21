<?php

namespace App\Exports\Report\Customer;

use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportCustomerSalesPaymentExport implements FromQuery, WithHeadings, WithMapping
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
        $paymentsQuery = DB::table('payment_sales')
            ->where('payment_sales.deleted_at', '=', null)
            ->join('sales', 'payment_sales.sale_id', '=', 'sales.id')
            ->where('sales.client_id', $this->id)
            ->latest('payment_sales.date');

        if ($this->request->filled('search')) {
            $paymentsQuery->where(function ($query) {
                $query->orWhere('payment_sales.Ref', 'LIKE', '%' . $this->request->input('search') . '%')
                    ->orWhere('payment_sales.date', 'LIKE', '%' . $this->request->input('search') . '%')
                    ->orWhere('payment_sales.Reglement', 'LIKE', '%' . $this->request->input('search') . '%');
            });
        }

        return $paymentsQuery->select(
            'payment_sales.id',
            'payment_sales.Ref',
            'payment_sales.date',
            'payment_sales.Reglement',
            'payment_sales.Montant',
            'sales.Ref as SaleRef'
        );
    }

    public function headings(): array
    {
        return [
            'ID',
            'Reference',
            'Date',
            'Reglement',
            'Amount',
            'Sale Reference',
        ];
    }

    public function map($payment): array
    {
        $reglement = '';

        if ($payment->Reglement == 'cash') {
            $reglement = 'Cash';
        } elseif ($payment->Reglement == 'pending') {
            $reglement = 'Pending';
        } else {
            $reglement = 'via midtrans';
        }

        return [
            $payment->id,
            $payment->Ref,
            $payment->date,
            $reglement,
            $payment->Montant,
            $payment->SaleRef,
        ];
    }
}
