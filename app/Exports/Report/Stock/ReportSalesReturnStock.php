<?php
namespace App\Exports\Report\Stock;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SaleReturnDetails;
use App\Models\Unit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportSalesReturnStock implements FromQuery, WithHeadings, WithMapping, ShouldQueue
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
        $sale_return_details_dataQuery = SaleReturnDetails::with('product', 'SaleReturn', 'SaleReturn.client', 'SaleReturn.warehouse')
            ->where('product_id', $this->productId)
            ->where('quantity', '>', 0)
            ->latest();

        if ($this->request->filled('search')) {
            $sale_return_details_dataQuery->where(function ($query) {
                $query->orWhereHas('SaleReturn.client', function ($q) {
                    $q->where('name', 'LIKE', '%' . $this->request->input('search') . '%');
                })
                ->orWhereHas('SaleReturn', function ($q) {
                    $q->where('Ref', 'LIKE', '%' . $this->request->input('search') . '%');
                })
                ->orWhereHas('product', function ($q) {
                    $q->where('name', 'LIKE', '%' . $this->request->input('search') . '%');
                });
            });
        }

        return $sale_return_details_dataQuery;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Reference',
            'Return Sale ID',
            'Client Name',
            'Warehouse Name',
            'Unit Sale',
            'Quantity',
            'Total',
            'Product Name',
        ];
    }

    public function map($detail): array
    {
        $unit = null;

        if ($detail->sale_unit_id !== null) {
            $unit = Unit::where('id', $detail->sale_unit_id)->first();
        } else {
            $product_unit_sale_id = Product::with('unitSale')
                ->where('id', $detail->product_id)
                ->first();

            if ($product_unit_sale_id && $product_unit_sale_id->unitSale) {
                $unit = Unit::where('id', $product_unit_sale_id->unitSale->id)->first();
            }
        }

        $product_name = $detail->product->name;
        if ($detail->product_variant_id) {
            $productVariant = ProductVariant::where('product_id', $detail->product_id)
                ->where('id', $detail->product_variant_id)->first();
            if ($productVariant) {
                $product_name = '[' . $productVariant->name . ']' . $detail->product->name;
            }
        }

        $saleReturn = $detail->SaleReturn;
        $client = $saleReturn ? $saleReturn->client : null;
        $warehouse = $saleReturn ? $saleReturn->warehouse : null;

        return [
            $saleReturn ? $saleReturn->date : '',
            $saleReturn ? $saleReturn->Ref : '',
            $saleReturn ? $saleReturn->id : '',
            $client ? $client->name : '',
            $unit ? $unit->ShortName : '',
            $warehouse ? $warehouse->name : '',
            $detail->quantity . ' ' . ($unit ? $unit->ShortName : ''),
            $detail->total ?? 0,
            $product_name,
        ];
    }
}
