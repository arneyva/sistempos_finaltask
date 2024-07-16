<?php

namespace App\Exports\Report;

use App\Models\Purchase;
use App\Models\Sale;
use App\Models\UserWarehouse;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class ReportPurchasesExport implements FromQuery, WithHeadings, WithMapping, ShouldQueue
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $PurchasesQuery = Purchase::select('purchases.*')
            ->with('payment_purchases', 'provider', 'warehouse')
            ->join('providers', 'purchases.provider_id', '=', 'providers.id')
            ->where('purchases.deleted_at', '=', null)->latest();

        if ($this->request->filled('from_date') && $this->request->filled('to_date')) {
            $PurchasesQuery->whereBetween('purchases.date', [$this->request->from_date, $this->request->to_date]);
        }

        if ($this->request->filled('search')) {
            $PurchasesQuery->where(function ($query) {
                $query->where('Ref', 'LIKE', '%' . $this->request->input('search') . '%')
                    ->orWhere('statut', 'LIKE', '%' . $this->request->input('search') . '%')
                    ->orWhere('GrandTotal', $this->request->input('search'))
                    ->orWhere('payment_statut', 'like', '%' . $this->request->input('search') . '%')
                    ->orWhere(function ($query) {
                        $query->whereHas('provider', function ($q) {
                            $q->where('name', 'LIKE', '%' . $this->request->input('search') . '%');
                        });
                    })
                    ->orWhere(function ($query) {
                        $query->whereHas('warehouse', function ($q) {
                            $q->where('name', 'LIKE', '%' . $this->request->input('search') . '%');
                        });
                    });
            });
        }

        if ($this->request->filled('warehouse_id')) {
            $PurchasesQuery->where('warehouse_id', '=', $this->request->input('warehouse_id'));
        }

        if ($this->request->filled('provider_id')) {
            $PurchasesQuery->where('provider_id', '=', $this->request->input('provider_id'));
        }

        if ($this->request->filled('statut')) {
            $PurchasesQuery->where('statut', '=', $this->request->input('statut'));
        }

        if ($this->request->filled('payment_statut')) {
            $PurchasesQuery->where('payment_statut', '=', $this->request->input('payment_statut'));
        }

        return $PurchasesQuery;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Date',
            'Reference',
            'Status',
            'Grand Total',
            'Payment Status',
            'Warehouse Name',
            'Provider Name',
        ];
    }

    public function map($purchase): array
    {
        return [
            $purchase->id,
            $purchase->date,
            $purchase->Ref,
            $purchase->statut,
            $purchase->GrandTotal,
            $purchase->payment_statut,
            $purchase->warehouse->name,
            $purchase->provider->name,
        ];
    }
}
