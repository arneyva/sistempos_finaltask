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


        if ($this->request->has('code') && $this->request->filled('code')) {
            $productsQuery->where('code', 'like', '%' . $this->request->input('code') . '%');
        }
        if ($this->request->has('name') && $this->request->filled('name')) {
            $productsQuery->where('name', 'like', '%' . $this->request->input('name') . '%');
        }
        if ($this->request->has('category_id') && $this->request->filled('category_id')) {
            $productsQuery->where('category_id', '=', $this->request->input('category_id'));
        }
        if ($this->request->has('brand_id') && $this->request->filled('brand_id')) {
            $productsQuery->where('brand_id', '=', $this->request->input('brand_id'));
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
            $productsQuery->where('Ref', 'like', '%' . $search . '%');
        }

        return $productsQuery;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Type Products',
            'Code',
            'Name',
            'Cost',
            'Price',
            'Category',
            'Brand',
            'Unit',
            'TaxNet',
            'Note',
            'IsActive',
        ];
    }

    public function map($products): array
    {
        return [
            $products->id,
            $products->type,
            $products->code,
            $products->name,
            $products->cost,
            $products->price,
            $products->category->name,
            $products->brand->name,
            $products->unit->name,
            $products->TaxNet,
            $products->note,
            $products->is_active,
        ];
    }
}
