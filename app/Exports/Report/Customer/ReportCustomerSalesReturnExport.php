<?php

namespace App\Exports\Report\Customer;

use App\Models\Sale;
use App\Models\SaleReturn;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportCustomerSalesReturnExport implements FromQuery, WithHeadings, WithMapping
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
        $saleReturnsQuery = SaleReturn::where('deleted_at', '=', null)
            ->where('client_id', $this->id)
            ->with('sale', 'client', 'warehouse')
            ->latest();

        if ($this->request->filled('search')) {
            $saleReturnsQuery->where(function ($query) {
                $query->orWhere('Ref', 'LIKE', '%' . $this->request->input('search') . '%')
                    ->orWhere('statut', 'LIKE', '%' . $this->request->input('search') . '%')
                    ->orWhere('payment_statut', 'LIKE', '%' . $this->request->input('search') . '%')
                    ->orWhereHas('client', function ($q) {
                        $q->where('name', 'LIKE', '%' . $this->request->input('search') . '%');
                    })
                    ->orWhereHas('sale', function ($q) {
                        $q->where('Ref', 'LIKE', '%' . $this->request->input('search') . '%');
                    })
                    ->orWhereHas('warehouse', function ($q) {
                        $q->where('name', 'LIKE', '%' . $this->request->input('search') . '%');
                    });
            });
        }

        return $saleReturnsQuery;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Client Name',
            'Date',
            'Reference',
            'Sale Reference',
            'Warehouse Name',
            'Status',
            'Payment Status',
            'Grand Total',
            'Created At',
            'Updated At',
            'Deleted At',
        ];
    }

    public function map($saleReturn): array
    {
        return [
            $saleReturn->id,
            $saleReturn->client->name ?? 'deleted',
            $saleReturn->date,
            $saleReturn->Ref,
            $saleReturn->sale->Ref ?? 'deleted',
            $saleReturn->warehouse->name ?? 'deleted',
            $saleReturn->statut,
            $saleReturn->payment_statut,
            $saleReturn->GrandTotal,
            $saleReturn->created_at ?? 'null',
            $saleReturn->updated_at ?? 'null',
            $saleReturn->deleted_at ?? 'null',
        ];
    }
}
