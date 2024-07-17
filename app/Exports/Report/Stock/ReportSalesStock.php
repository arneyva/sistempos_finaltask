<?php
namespace App\Exports\Report\Stock;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SaleDetail;
use App\Models\Unit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportSalesStock implements FromQuery, WithHeadings, WithMapping, ShouldQueue
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
        $sale_details_dataQuery = SaleDetail::with('product', 'sale', 'sale.client', 'sale.warehouse')
            ->where('product_id', $this->productId)
            ->latest();

        if ($this->request->filled('search')) {
            $sale_details_dataQuery->where(function ($query) {
                $query->orWhereHas('sale.client', function ($q) {
                    $q->where('name', 'LIKE', '%' . $this->request->input('search') . '%');
                })
                    ->orWhereHas('sale.warehouse', function ($q) {
                        $q->where('name', 'LIKE', '%' . $this->request->input('search') . '%');
                    })
                    ->orWhereHas('sale', function ($q) {
                        $q->where('Ref', 'LIKE', '%' . $this->request->input('search') . '%')
                            ->orWhere('statut', 'LIKE', '%' . $this->request->input('search') . '%')
                            ->orWhere('payment_statut', 'LIKE', '%' . $this->request->input('search') . '%')
                            ->orWhere('payment_method', 'LIKE', '%' . $this->request->input('search') . '%')
                            ->orWhere('shipping_status', 'LIKE', '%' . $this->request->input('search') . '%');
                    })
                    ->orWhereHas('product', function ($q) {
                        $q->where('name', 'LIKE', '%' . $this->request->input('search') . '%');
                    });
            });
        }

        return $sale_details_dataQuery;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Reference',
            'Sale ID',
            'Client Name',
            'Unit Sale',
            'Warehouse Name',
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

        $sale = $detail->sale;
        $client = $sale ? $sale->client : null;
        $warehouse = $sale ? $sale->warehouse : null;

        return [
            $detail->date ?? '',
            $sale ? $sale->Ref : '',
            $sale ? $sale->id : '',
            $client ? $client->name : '',
            $unit ? $unit->ShortName : '',
            $warehouse ? $warehouse->name : '',
            $detail->quantity . ' ' . ($unit ? $unit->ShortName : ''),
            $detail->total ?? 0,
            $product_name,
        ];
    }
}
