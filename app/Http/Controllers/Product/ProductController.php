<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('templates.product.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $category = Category::query()->get();
        $brand = Brand::query()->get();
        $unit = Unit::query()->get();

        return view('templates.product.create', [
            'category' => $category,
            'brand' => $brand,
            'unit' => $unit,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $productRules = [
            'type' => 'required',
            'code' => [
                'required',
                Rule::unique(Product::class, 'code')->whereNull('deleted_at'),
                Rule::unique(ProductVariant::class, 'code')->whereNull('deleted_at'),
            ],
            'name' => [
                'required',
                Rule::unique(Product::class, 'name')->whereNull('deleted_at'),
            ],
            'cost' => Rule::requiredIf($request->type == 'is_single'),
            'price' => Rule::requiredIf($request->type == 'is_single'),
            'unit_id' => 'required',

        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('templates.product.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
