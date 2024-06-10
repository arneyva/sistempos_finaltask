<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\UserWarehouse;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesExport implements FromQuery, WithHeadings, WithMapping
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
            $SaleQuery = Sale::query()->with(['user', 'warehouse', 'client', 'paymentSales'])->where('deleted_at', '=', null)->latest();
        } else {
            $SaleQuery = Sale::query()->with(['user', 'warehouse', 'client', 'paymentSales'])->where('deleted_at', '=', null)->where('to_warehouse_id', $warehouses_id)->latest();
        }
        // Terapkan filter berdasarkan parameter yang diterima dari request
        if ($this->request->has('date') && $this->request->filled('date')) {
            $SaleQuery->whereDate('date', '=', $this->request->input('date'));
        }

        if ($this->request->has('Ref') && $this->request->filled('Ref')) {
            $SaleQuery->where('Ref', 'like', '%' . $this->request->input('Ref') . '%');
        }

        if ($this->request->has('warehouse_id') && $this->request->filled('warehouse_id')) {
            $SaleQuery->where('warehouse_id', '=', $this->request->input('warehouse_id'));
        }
        if ($this->request->has('client_id') && $this->request->filled('client_id')) {
            $SaleQuery->where('client_id', '=', $this->request->input('client_id'));
        }
        if ($this->request->has('statut') && $this->request->filled('statut')) {
            $SaleQuery->where('statut', '=', $this->request->input('statut'));
        }
        if ($this->request->has('payment_statut') && $this->request->filled('payment_statut')) {
            $SaleQuery->where('payment_statut', '=', $this->request->input('payment_statut'));
        }
        if ($this->request->has('shipping_status') && $this->request->filled('shipping_status')) {
            $SaleQuery->where('shipping_status', '=', $this->request->input('shipping_status'));
        }

        // Lakukan sorting sesuai request jika diperlukan
        if ($this->request->has('SortField') && $this->request->has('SortType')) {
            $sortField = $this->request->input('SortField');
            $sortType = $this->request->input('SortType');
            $SaleQuery->orderBy($sortField, $sortType);
        }
        return $SaleQuery;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Reference',
            'Added By',
            'Customer',
            'Warehouse',
            'Status',
            'Grand Total',
            'Payment Status',
            'Shipping Status',
            'notes'
        ];
    }

    public function map($Sale): array
    {
        return [
            $Sale->date,
            $Sale->Ref,
            $Sale->user->username ?? 'deleted',
            $Sale->client->name ?? 'deleted',
            $Sale->warehouse->name ?? 'deleted',
            $Sale->statut,
            $Sale->GrandTotal,
            $Sale->payment_statut,
            $Sale->shipping_status ?? 'Without shiiping',
            $Sale->notes
        ];
    }
}
