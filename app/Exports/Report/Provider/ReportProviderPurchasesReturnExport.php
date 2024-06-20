<?php

namespace App\Exports\Report\Provider;

use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Sale;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportProviderPurchasesReturnExport implements FromQuery, WithHeadings, WithMapping
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
        $PurchaseReturnQuery = PurchaseReturn::where('deleted_at', '=', null)
            ->with('purchase', 'provider', 'warehouse')
            ->where('provider_id', $this->id)
            ->latest();

        if ($this->request->filled('search')) {
            $PurchaseReturnQuery->where(function ($query) {
                $query->orWhere('Ref', 'like', '%' . $this->request->input('search') . '%')
                    ->orWhere('statut', 'like', '%' . $this->request->input('search') . '%')
                    ->orWhere('payment_statut', 'like', '%' . $this->request->input('search') . '%')
                    ->orWhere(function ($query) {
                        $query->whereHas('provider', function ($q) {
                            $q->where('name', 'LIKE', '%' . $this->request->input('search') . '%');
                        });
                    })
                    ->orWhere(function ($query) {
                        return $query->whereHas('purchase', function ($q) {
                            $q->where('Ref', 'LIKE', '%' . $this->request->input('search') . '%');
                        });
                    })
                    ->orWhere(function ($query) {
                        $query->whereHas('warehouse', function ($q) {
                            $q->where('name', 'LIKE', '%' . $this->request->input('search') . '%');
                        });
                    });
            });
        }

        return $PurchaseReturnQuery;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Reference',
            'Purchase Reference',
            'Warehouse Name',
            'Provider Name',
            'Status',
            'Grand Total',
            'Paid Amount',
            'Due',
            'Payment Status',
        ];
    }

    public function map($purchase): array
    {
        return [
            $purchase->id,
            $purchase->Ref,
            $purchase->purchase->Ref,
            $purchase->warehouse->name ?? 'deleted',
            $purchase->provider->name ?? 'deleted',
            $purchase->statut,
            $purchase->GrandTotal,
            $purchase->paid_amount,
            $purchase->GrandTotal - $purchase->paid_amount,
            $purchase->payment_statut,
        ];
    }
}
