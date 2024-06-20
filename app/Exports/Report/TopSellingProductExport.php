<?php

namespace App\Exports\Report;

use App\Models\SaleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TopSellingProductExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $productsQuery = SaleDetail::join('products', 'sale_details.product_id', '=', 'products.id')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->whereNull('sales.deleted_at')
            ->select(
                'products.name as name',
                'products.code as code',
                DB::raw('COUNT(sale_details.id) as total_sales'),
                DB::raw('SUM(sale_details.total) as total')
            )
            ->groupBy('products.name', 'products.code')
            ->orderBy('products.name', 'asc');
        if ($this->request->filled('search')) {
            $searchTerm = '%' . $this->request->input('search') . '%';
            $productsQuery->where(function ($query) use ($searchTerm) {
                $query->where('products.name', 'LIKE', $searchTerm)
                    ->orWhere('products.code', 'LIKE', $searchTerm);
            });
        }
        $this->getQuery($productsQuery);
        return $productsQuery;
    }
    public function headings(): array
    {
        return [
            'Name',
            'Code',
            'Total Sales',
            'Total',
        ];
    }

    public function map($product): array
    {
        return [
            $product->name,
            $product->code,
            $product->total_sales,
            $product->total,
        ];
    }

    // Metode untuk menampilkan query yang dihasilkan
    private function getQuery($query)
    {
        $sql = $query->toSql();
        $bindings = $query->getBindings();
        \Log::debug('Query: ' . vsprintf(str_replace('?', "'%s'", $sql), $bindings));
    }

    // Metode untuk menampilkan data yang diteruskan dari request
    private function getRequestData()
    {
        \Log::debug('Request Data: ' . json_encode($this->request->all()));
    }
}
