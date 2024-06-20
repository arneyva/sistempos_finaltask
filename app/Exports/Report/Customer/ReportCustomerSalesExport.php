<?php

namespace App\Exports\Report\Customer;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportCustomerSalesExport implements FromQuery, WithHeadings, WithMapping
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
        // Inisialisasi query dengan menggunakan model Sale
        $salesQuery = Sale::where('deleted_at', '=', null)
            ->where('client_id', $this->id)
            ->with('client', 'warehouse')
            ->latest();

        // Lakukan pencarian jika diperlukan
        if ($this->request->filled('search')) {
            $salesQuery->where(function ($query) {
                $query->orWhere('Ref', 'like', '%' . $this->request->input('search') . '%')
                    ->orWhere('statut', 'like', '%' . $this->request->input('search') . '%')
                    ->orWhere('payment_statut', 'like', '%' . $this->request->input('search') . '%')
                    ->orWhere('payment_method', 'like', '%' . $this->request->input('search') . '%')
                    ->orWhere('shipping_status', 'like', '%' . $this->request->input('search') . '%')
                    ->orWhere(function ($query) {
                        $query->whereHas('client', function ($q) {
                            $q->where('name', 'LIKE', '%' . $this->request->input('search') . '%');
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
            'Client Name',
            'Date',
            'Reference',
            'Warehouse Name',
            'Status',
            'Payment Status',
            'Payment Method',
            'Shipping Status',
            'Grand Total',
            'Created At',
            'Updated At',
            'Deleted At',
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->id,
            $sale->client->name ?? 'deleted',
            $sale->date,
            $sale->Ref,
            $sale->warehouse->name ?? 'deleted',
            $sale->statut,
            $sale->payment_statut,
            $sale->payment_method,
            $sale->shipping_status,
            $sale->GrandTotal,
            $sale->created_at ?? 'null',
            $sale->updated_at ?? 'null',
            $sale->deleted_at ?? 'null',
        ];
    }
}
