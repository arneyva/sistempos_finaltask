<?php
namespace App\Exports\Report\Stock;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\TransferDetail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportTransferStock implements FromQuery, WithHeadings, WithMapping, ShouldQueue
{
    protected $request;
    protected $productId;

    public function __construct($request, $productId)
    {
        $this->request = $request;
        $this->productId = $productId;
    }

    public function query()
    {
        $transfer_details_dataQuery = TransferDetail::with('product', 'transfer', 'transfer.from_warehouse', 'transfer.to_warehouse')
            ->where('product_id', $this->productId)
            ->latest();

        if ($this->request->filled('search')) {
            $transfer_details_dataQuery->where(function ($query) {
                $query->orWhereHas('transfer.from_warehouse', function ($q) {
                    $q->where('name', 'LIKE', '%' . $this->request->input('search') . '%');
                })
                ->orWhereHas('transfer.to_warehouse', function ($q) {
                    $q->where('name', 'LIKE', '%' . $this->request->input('search') . '%');
                })
                ->orWhereHas('transfer', function ($q) {
                    $q->where('Ref', 'LIKE', '%' . $this->request->input('search') . '%');
                })
                ->orWhereHas('product', function ($q) {
                    $q->where('name', 'LIKE', '%' . $this->request->input('search') . '%');
                });
            });
        }

        return $transfer_details_dataQuery;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Reference',
            'From Warehouse',
            'To Warehouse',
            'Product Name',
        ];
    }

    public function map($detail): array
    {
        $product_name = $detail->product->name;
        if ($detail->product_variant_id) {
            $productVariant = ProductVariant::where('product_id', $detail->product_id)
                ->where('id', $detail->product_variant_id)->first();
            if ($productVariant) {
                $product_name = '[' . $productVariant->name . ']' . $detail->product->name;
            }
        }

        $transfer = $detail->transfer;
        $from_warehouse = $transfer ? $transfer->from_warehouse : null;
        $to_warehouse = $transfer ? $transfer->to_warehouse : null;

        return [
            $transfer ? $transfer->date : '',
            $transfer ? $transfer->Ref : '',
            $from_warehouse ? $from_warehouse->name : '',
            $to_warehouse ? $to_warehouse->name : '',
            $product_name,
        ];
    }
}
