<?php
namespace App\Exports\Report\Stock;

use App\Models\Product;
use App\Models\ProductWarehouse;
use App\Models\UserWarehouse;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportProductStock implements FromQuery, WithHeadings, WithMapping, ShouldQueue
{
    protected $request;
    protected $user_auth;

    public function __construct(Request $request, $user_auth)
    {
        $this->request = $request;
        $this->user_auth = $user_auth;
    }

    public function query()
    {
        $products_dataQuery = Product::with('unit', 'category')
            ->where('deleted_at', '=', null)->latest();

        if ($this->request->filled('search')) {
            $products_dataQuery->where(function ($query) {
                $query->where('products.name', 'LIKE', '%' . $this->request->input('search') . '%')
                    ->orWhere('products.code', 'LIKE', '%' . $this->request->input('search') . '%')
                    ->orWhere(function ($query) {
                        $query->whereHas('category', function ($q) {
                            $q->where('name', 'LIKE', '%' . $this->request->input('search') . '%');
                        });
                    });
            });
        }

        return $products_dataQuery;
    }

    public function headings(): array
    {
        return [
            'Code',
            'Product Name',
            'Category',
            'Quantity',
        ];
    }

    public function map($product): array
    {
        $user_auth = $this->user_auth;
        $request = $this->request;

        if ($product->type != 'is_service') {
            $current_stock_query = ProductWarehouse::where('product_id', $product->id)
                ->where('deleted_at', '=', null);

            if ($user_auth->hasRole('staff')) {
                $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');
                $current_stock_query->whereIn('warehouse_id', $warehouses_id);
            } else {
                if ($request->filled('warehouse_id')) {
                    $current_stock_query->where('warehouse_id', $request->warehouse_id);
                }
            }

            $current_stock = $current_stock_query->sum('qty');
            $quantity = $current_stock . ' ' . $product->unit->ShortName;
        } else {
            $quantity = '---';
        }

        return [
            $product->code,
            $product->name,
            $product->category->name,
            $quantity,
        ];
    }
}
