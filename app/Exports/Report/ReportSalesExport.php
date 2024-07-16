<?php

namespace App\Exports\Report;

use App\Models\Sale;
use App\Models\UserWarehouse;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class ReportSalesExport implements FromQuery, WithHeadings, WithMapping, ShouldQueue
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $user_auth = auth()->user();
        $saleQuery = Sale::select('sales.*')
            ->with('facture', 'client', 'warehouse')
            ->join('clients', 'sales.client_id', '=', 'clients.id')
            ->where('sales.deleted_at', '=', null)->latest();

        if ($this->request->filled('from_date') && $this->request->filled('to_date')) {
            $saleQuery->whereBetween('sales.date', [$this->request->from_date, $this->request->to_date]);
        }
        if ($this->request->filled('search')) {
            $saleQuery->where(function ($query) {
                $query->where('Ref', 'LIKE', '%' . $this->request->input('search') . '%')
                    ->orWhere('statut', 'LIKE', '%' . $this->request->input('search') . '%')
                    ->orWhere('GrandTotal', $this->request->input('search'))
                    ->orWhere('payment_statut', 'like', '%' . $this->request->input('search') . '%')
                    ->orWhere('shipping_status', 'like', '%' . $this->request->input('search') . '%')
                    ->orWhere(function ($query) {
                        $query->whereHas('client', function ($q) {
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
            $saleQuery->where('warehouse_id', '=', $this->request->input('warehouse_id'));
        }
        if ($this->request->filled('client_id')) {
            $saleQuery->where('client_id', '=', $this->request->input('client_id'));
        }
        if ($this->request->filled('statut')) {
            $saleQuery->where('statut', '=', $this->request->input('statut'));
        }
        if ($this->request->filled('payment_statut')) {
            $saleQuery->where('payment_statut', '=', $this->request->input('payment_statut'));
        }
        // Filter khusus untuk staff berdasarkan gudang
        if ($user_auth->hasRole('staff')) {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');
            $saleQuery->whereIn('warehouse_id', $warehouses_id);
        }

        return $saleQuery;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Date',
            'Reference',
            'Status',
            'Discount',
            'Shipping',
            'Warehouse Name',
            'Client Name',
            'Client Email',
            'Client Phone',
            'Grand Total',
            'Paid Amount',
            'Due',
            'Payment Status',
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->id,
            $sale->date,
            $sale->Ref,
            $sale->statut,
            $sale->discount ?? 0,
            $sale->shipping ?? 0,
            $sale->warehouse->name,
            $sale->client->name,
            $sale->client->email,
            $sale->client->phone,
            $sale->GrandTotal ?? 0,
            $sale->paid_amount ?? 0,
            $sale->GrandTotal - $sale->paid_amount ?? 0,
            $sale->payment_statut ?? 'Unpaid',
        ];
    }
}
