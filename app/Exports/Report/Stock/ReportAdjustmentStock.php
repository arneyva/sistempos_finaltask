<?php
namespace App\Exports\Report\Stock;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\AdjustmentDetail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportAdjustmentStock implements FromQuery, WithHeadings, WithMapping, ShouldQueue
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
        $adjustment_details_dataQuery = AdjustmentDetail::with('product', 'adjustment', 'adjustment.warehouse')
            ->where('product_id', $this->productId)
            ->latest();

        if ($this->request->filled('search')) {
            $adjustment_details_dataQuery->where(function ($query) {
                $query->orWhereHas('adjustment.warehouse', function ($q) {
                    $q->where('name', 'LIKE', '%' . $this->request->input('search') . '%');
                })
                ->orWhereHas('adjustment', function ($q) {
                    $q->where('Ref', 'LIKE', '%' . $this->request->input('search') . '%');
                })
                ->orWhereHas('product', function ($q) {
                    $q->where('name', 'LIKE', '%' . $this->request->input('search') . '%');
                });
            });
        }

        return $adjustment_details_dataQuery;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Reference',
            'Warehouse Name',
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

        $adjustment = $detail->adjustment;
        $warehouse = $adjustment ? $adjustment->warehouse : null;

        return [
            $adjustment ? $adjustment->date : '',
            $adjustment ? $adjustment->Ref : '',
            $warehouse ? $warehouse->name : '',
            $product_name,
        ];
    }
}
