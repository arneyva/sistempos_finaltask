<?php

namespace App\Exports\Report\Warehouse;

use App\Models\PurchaseReturn;
use App\Models\Sale;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportPurchasesReturnWarehouse implements FromQuery, WithHeadings, WithMapping, ShouldQueue
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $purchaseReturnQuery = PurchaseReturn::where('deleted_at', '=', null)
            ->with('purchase', 'provider', 'warehouse');

        if ($this->request->filled('warehouse_id')) {
            $purchaseReturnQuery->where('warehouse_id', '=', $this->request->input('warehouse_id'));
        }

        if ($this->request->filled('search')) {
            $purchaseReturnQuery->where(function ($query) {
                $query->whereHas('purchase', function ($q) {
                    $q->where('Ref', 'LIKE', '%' . $this->request->input('search') . '%');
                })
                    ->orWhere('Ref', 'LIKE', '%' . $this->request->input('search') . '%')
                    ->orWhere('GrandTotal', $this->request->input('search'))
                    ->orWhere('payment_statut', 'like', '%' . $this->request->input('search') . '%')
                    ->orWhere(function ($query) {
                        $query->whereHas('provider', function ($q) {
                            $q->where('name', 'LIKE', '%' . $this->request->input('search') . '%');
                        });
                    });
            });
        }

        return $purchaseReturnQuery;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Reference',
            'Status',
            'Purchase Reference',
            'Purchase ID',
            'Warehouse Name',
            'Provider Name',
            'Grand Total',
            'Paid Amount',
            'Due Amount',
            'Payment Status',
        ];
    }

    public function map($purchaseReturn): array
    {
        return [
            $purchaseReturn->id,
            $purchaseReturn->Ref,
            $purchaseReturn->statut,
            $purchaseReturn->purchase ? $purchaseReturn->purchase->Ref : '---',
            $purchaseReturn->purchase ? $purchaseReturn->purchase->id : null,
            $purchaseReturn->warehouse->name,
            $purchaseReturn->provider->name,
            $purchaseReturn->GrandTotal,
            $purchaseReturn->paid_amount,
            $purchaseReturn->GrandTotal - $purchaseReturn->paid_amount,
            $purchaseReturn->payment_statut,
        ];
    }
}
