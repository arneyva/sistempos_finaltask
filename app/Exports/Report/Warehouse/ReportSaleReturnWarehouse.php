<?php
namespace App\Exports\Report\Warehouse;

use App\Models\SaleReturn;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportSaleReturnWarehouse implements FromQuery, WithHeadings, WithMapping, ShouldQueue
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $saleReturnQuery = SaleReturn::where('deleted_at', '=', null)
            ->with('sale', 'client', 'warehouse');

        if ($this->request->filled('warehouse_id')) {
            $saleReturnQuery->where('warehouse_id', '=', $this->request->input('warehouse_id'));
        }

        if ($this->request->filled('search')) {
            $saleReturnQuery->where(function ($query) {
                $query->where('Ref', 'LIKE', '%' . $this->request->input('search') . '%')
                    ->orWhere(function ($query) {
                        $query->whereHas('sale', function ($q) {
                            $q->where('Ref', 'LIKE', '%' . $this->request->input('search') . '%');
                        });
                    })
                    ->orWhere(function ($query) {
                        $query->whereHas('client', function ($q) {
                            $q->where('name', 'LIKE', '%' . $this->request->input('search') . '%');
                        });
                    });
            });
        }

        return $saleReturnQuery;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Warehouse Name',
            'Reference',
            'Status',
            'Client Name',
            'Sale Reference',
            'Sale ID',
            'Grand Total',
            'Paid Amount',
            'Due',
            'Payment Status',
        ];
    }

    public function map($saleReturn): array
    {
        return [
            $saleReturn->id,
            $saleReturn->warehouse->name,
            $saleReturn->Ref,
            $saleReturn->statut,
            $saleReturn->client->name,
            $saleReturn->sale ? $saleReturn->sale->Ref : '---',
            $saleReturn->sale ? $saleReturn->sale->id : null,
            $saleReturn->GrandTotal,
            $saleReturn->paid_amount,
            $saleReturn->GrandTotal - $saleReturn->paid_amount,
            $saleReturn->payment_statut,
        ];
    }
}
