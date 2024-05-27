<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        // Inisialisasi query dengan menggunakan model products
        $productsQuery = Product::with(['unit', 'category', 'brand'])->where('deleted_at', '=', null);

        // Terapkan filter berdasarkan parameter yang diterima dari request
        if ($this->request->has('date') && $this->request->filled('date')) {
            $productsQuery->whereDate('date', '=', $this->request->input('date'));
        }

        if ($this->request->has('Ref') && $this->request->filled('Ref')) {
            $productsQuery->where('Ref', 'like', '%'.$this->request->input('Ref').'%');
        }

        if ($this->request->has('warehouse_id') && $this->request->filled('warehouse_id')) {
            $productsQuery->where('warehouse_id', '=', $this->request->input('warehouse_id'));
        }

        // Lakukan sorting sesuai request jika diperlukan
        if ($this->request->has('SortField') && $this->request->has('SortType')) {
            $sortField = $this->request->input('SortField');
            $sortType = $this->request->input('SortType');
            $productsQuery->orderBy($sortField, $sortType);
        }

        // Lakukan pencarian jika diperlukan
        if ($this->request->has('search') && $this->request->filled('search')) {
            $search = $this->request->input('search');
            $productsQuery->where('Ref', 'like', '%'.$search.'%');
        }

        return $productsQuery;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Added By',
            'Date',
            'Reference',
            'Warehouse Name',
            'Total Items',
            'Notes',
            'Created At',
            'Updated At',
            'Deleted At',
        ];
    }

    public function map($products): array
    {
        return [
            $products->id,
            $products->user->firstname,
            $products->date,
            $products->Ref,
            $products->warehouse->name ?? 'deleted',
            $products->items,
            $products->notes,
            $products->created_at ?? 'null',
            $products->updated_at ?? 'null',
            $products->deleted_at ?? 'null',
        ];
    }
}
