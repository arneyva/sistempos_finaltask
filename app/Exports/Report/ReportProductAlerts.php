<?

namespace App\Exports\Report;

use App\Models\ProductWarehouse;
use App\Models\ProductVariant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportProductAlerts implements FromQuery, WithHeadings, WithMapping, ShouldQueue
{
    protected $request;
    protected $user_auth;
    protected $warehouses_id;

    public function __construct(Request $request, $user_auth, $warehouses_id)
    {
        $this->request = $request;
        $this->user_auth = $user_auth;
        $this->warehouses_id = $warehouses_id;
    }

    public function query()
    {
        $products_alertsQuery = ProductWarehouse::join('products', 'product_warehouse.product_id', '=', 'products.id')
            ->join('warehouses', 'product_warehouse.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('product_variants', 'product_warehouse.product_variant_id', '=', 'product_variants.id')
            ->select(
                'products.name as product_name',
                'products.code as product_code',
                'warehouses.name as warehouse_name',
                'product_warehouse.qty',
                'products.stock_alert',
                'product_variants.name as variant_name',
                'product_variants.code as variant_code'
            )
            ->whereRaw('qty <= stock_alert')
            ->when($this->request->filled('warehouse_id'), function ($query) {
                return $query->where('warehouse_id', $this->request->input('warehouse_id'));
            });

        return $products_alertsQuery;
    }

    public function headings(): array
    {
        return [
            'Code',
            'Product Name',
            'Warehouse/Outlet',
            'Quantity',
            'Stock Alert',
        ];
    }

    public function map($product_alert): array
    {
        $code = $product_alert->variant_code ? $product_alert->variant_code : $product_alert->product_code;
        $name = $product_alert->variant_name ? $product_alert->product_name . ' ~ ' . $product_alert->variant_name : $product_alert->product_name;

        return [
            $code,
            $name,
            $product_alert->warehouse_name,
            $product_alert->qty,
            $product_alert->stock_alert,
        ];
    }
}
