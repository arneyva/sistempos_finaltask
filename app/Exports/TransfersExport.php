<?php

namespace App\Exports;

use App\Models\Transfer;
use App\Models\UserWarehouse;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransfersExport implements FromQuery, WithHeadings, WithMapping
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
            $TransferQuery = Transfer::query()->with(['from_warehouse', 'to_warehouse', 'details'])->where('deleted_at', '=', null)->latest();
        } else {
            $TransferQuery = Transfer::query()->with(['from_warehouse', 'to_warehouse', 'details'])->where('deleted_at', '=', null)->where('to_warehouse_id', $warehouses_id)->latest();
        }
        // Terapkan filter berdasarkan parameter yang diterima dari request
        if ($this->request->has('date') && $this->request->filled('date')) {
            $TransferQuery->whereDate('date', '=', $this->request->input('date'));
        }

        if ($this->request->has('Ref') && $this->request->filled('Ref')) {
            $TransferQuery->where('Ref', 'like', '%'.$this->request->input('Ref').'%');
        }

        if ($this->request->has('from_warehouse_id') && $this->request->filled('from_warehouse_id')) {
            $TransferQuery->where('from_warehouse_id', '=', $this->request->input('from_warehouse_id'));
        }
        if ($this->request->has('to_warehouse_id') && $this->request->filled('to_warehouse_id')) {
            $TransferQuery->where('to_warehouse_id', '=', $this->request->input('to_warehouse_id'));
        }
        if ($this->request->has('statut') && $this->request->filled('statut')) {
            $TransferQuery->where('statut', '=', $this->request->input('statut'));
        }

        // Lakukan sorting sesuai request jika diperlukan
        if ($this->request->has('SortField') && $this->request->has('SortType')) {
            $sortField = $this->request->input('SortField');
            $sortType = $this->request->input('SortType');
            $TransferQuery->orderBy($sortField, $sortType);
        }

        return $TransferQuery;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Reference',
            'From Warehouse',
            'To Warehouse',
            'Total Items',
            'Grand Total',
            'Notes',
            'Status',
        ];
    }

    public function map($transfer): array
    {
        return [
            $transfer->date,
            $transfer->Ref,
            $transfer->from_warehouse->name ?? 'deleted',
            $transfer->to_warehouse->name ?? 'deleted',
            $transfer->items,
            $transfer->GrandTotal,
            $transfer->notes,
            $transfer->statut,
        ];
    }
}
