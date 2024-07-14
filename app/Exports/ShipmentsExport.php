<?php

namespace App\Exports;

use App\Models\Shipment;
use App\Models\UserWarehouse;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ShipmentsExport implements FromQuery, WithHeadings, WithMapping
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
        
        if ($user_auth->hasRole(['superadmin', 'inventaris'])) {
            $shipmentQuery = Shipment::query()->with(['sale', 'sale.client', 'sale.warehouse'])->whereNull('deleted_at')->latest();
        } else {
            $shipmentQuery = Shipment::with(['sale', 'sale.client', 'sale.warehouse'])
                ->whereNull('deleted_at')
                ->whereHas('sale', function ($query) use ($warehouses_id) {
                    $query->whereIn('warehouse_id', $warehouses_id);
                })
                ->latest();
        }

        if ($this->request->filled('date')) {
            $shipmentQuery->whereDate('date', '=', $this->request->input('date'));
        }
        if ($this->request->filled('Ref')) {
            $shipmentQuery->where('Ref', 'like', '%'.$this->request->input('Ref').'%');
        }

        if ($this->request->filled('warehouse_id')) {
            $warehouse_id = $this->request->input('warehouse_id');
            $shipmentQuery->whereHas('sale', function ($query) use ($warehouse_id) {
                $query->where('warehouse_id', '=', $warehouse_id);
            });
        }

        if ($this->request->filled('status')) {
            $shipmentQuery->where('status', '=', $this->request->input('status'));
        }

        return $shipmentQuery;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Date',
            'Reference',
            'Status',
            'Client',
            'Warehouse',
            'Total Amount',
        ];
    }

    public function map($shipment): array
    {
        return [
            $shipment->id,
            $shipment->date,
            $shipment->Ref,
            $shipment->status,
            optional($shipment->sale->client)->name,
            optional($shipment->sale->warehouse)->name,
            $shipment->sale->shipping,
        ];
    }
}
