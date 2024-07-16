<?php

namespace App\Exports\Report\Warehouse;

use App\Models\Sale;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportSaleWarehouse implements FromQuery, WithHeadings, WithMapping, ShouldQueue
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $salesQuery = Sale::where('deleted_at', '=', null)->with('client', 'warehouse');

        if ($this->request->filled('warehouse_id')) {
            $salesQuery->where('warehouse_id', '=', $this->request->input('warehouse_id'));
        }

        if ($this->request->filled('search')) {
            $salesQuery->where(function ($query) {
                $query->where('Ref', 'like', '%' . $this->request->input('search') . '%')
                    ->orWhere(function ($query) {
                        $query->whereHas('client', function ($q) {
                            $q->where('name', 'like', '%' . $this->request->input('search') . '%');
                        });
                    });
            });
        }

        return $salesQuery;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Date',
            'Reference',
            'Warehouse Name',
            'Client Name',
            'Status',
            'Grand Total',
            'Paid Amount',
            'Due',
            'Payment Status',
            'Shipping Status',
            'Shipping'
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->id,
            $sale->date,
            $sale->Ref,
            $sale->warehouse->name,
            $sale->client->name,
            $sale->statut,
            $sale->GrandTotal,
            $sale->paid_amount,
            $sale->GrandTotal - $sale->paid_amount,
            $sale->payment_statut,
            $sale->shipping_status,
            $sale->shipping,
        ];
    }
}
