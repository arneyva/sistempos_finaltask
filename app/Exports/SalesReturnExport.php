<?php

namespace App\Exports;

use App\Models\SaleReturn;
use App\Models\UserWarehouse;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesReturnExport implements FromQuery, WithHeadings, WithMapping
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
            $salereturnQuery = SaleReturn::query()->with(['sale', 'facture', 'client', 'warehouse'])->where('deleted_at', '=', null)->latest();
        } else {
            $salereturnQuery = SaleReturn::query()->with(['sale', 'facture', 'client', 'warehouse'])->where('deleted_at', '=', null)->where('warehouse_id', $warehouses_id)->latest();
        }
        // Terapkan filter berdasarkan parameter yang diterima dari request
        if ($this->request->has('date') && $this->request->filled('date')) {
            $salereturnQuery->whereDate('date', '=', $this->request->input('date'));
        }

        if ($this->request->has('Ref') && $this->request->filled('Ref')) {
            $salereturnQuery->where('Ref', 'like', '%' . $this->request->input('Ref') . '%');
        }

        if ($this->request->has('warehouse_id') && $this->request->filled('warehouse_id')) {
            $salereturnQuery->where('warehouse_id', '=', $this->request->input('warehouse_id'));
        }
        if ($this->request->has('client_id') && $this->request->filled('client_id')) {
            $salereturnQuery->where('client_id', '=', $this->request->input('client_id'));
        }
        if ($this->request->has('statut') && $this->request->filled('statut')) {
            $salereturnQuery->where('statut', '=', $this->request->input('statut'));
        }
        // Lakukan sorting sesuai request jika diperlukan
        if ($this->request->has('SortField') && $this->request->has('SortType')) {
            $sortField = $this->request->input('SortField');
            $sortType = $this->request->input('SortType');
            $salereturnQuery->orderBy($sortField, $sortType);
        }
        return $salereturnQuery;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Reference',
            'Customer',
            'Warehouse',
            'Sale Reference',
            'Status',
            'Grand Total',
            'Payment Status',
            'notes'
        ];
    }

    public function map($SaleReturn): array
    {
        return [
            $SaleReturn->date,
            $SaleReturn->Ref,
            $SaleReturn->client->name ?? 'deleted',
            $SaleReturn->warehouse->name ?? 'deleted',
            $SaleReturn->sale->Ref,
            $SaleReturn->statut,
            $SaleReturn->GrandTotal,
            $SaleReturn->payment_statut,
            $SaleReturn->notes
        ];
    }
}
