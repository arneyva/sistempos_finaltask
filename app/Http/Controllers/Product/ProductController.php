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
        $product = Product::query()->latest()->get();

        return view('templates.product.index', [
            'product' => $product,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $category = Category::query()->get();
        $brand = Brand::query()->get();
        $unit = Unit::query()->where('base_unit', null)->get();

        return view('templates.product.create', [
            'category' => $category,
            'brand' => $brand,
            'unit' => $unit,
        ]);
    }

    public function getUnits(Request $request, $id)
    {
        $productUnit = Unit::findOrFail($id);
        $relatedUnits = Unit::where('base_unit', $productUnit->id)->orWhere('id', $productUnit->id)->get();

        return response()->json([
            'related_units' => $relatedUnits,
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
            'category_id' => [
                'required',
                Rule::exists(Category::class, 'id'),
            ],
            'brand_id' => [
                'required',
                Rule::exists(Brand::class, 'id'),
            ],
            'unit_id' => 'required',
            'unit_sale_id' => 'required',
            'unit_purchase_id' => 'required',
            'TaxNet' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
            'note' => 'nullable',
            'is_imei' => 'required',
            'not_selling' => 'required',
        ];
        $productValue = new Product();
        $productValue->type = $request['type'];
        if ($request['type'] == 'is_single') {
            $productValue->cost = $request['cost'];
            $productValue->price = $request['price'];
        } else {
            $productValue->cost = 0;
            $productValue->price = 0;
        }
        $productValue->name = $request['name'];
        $productValue->code = $request['code'];
        $productValue->Type_barcode = 'CODE128';
        $productValue->category_id = $request['category_id'];
        $productValue->brand_id = $request['brand_id'];
        $productValue->unit_id = $request['unit_id'];
        $productValue->unit_purchase_id = $request['unit_purchase_id'];
        $productValue->unit_sale_id = $request['unit_sale_id'];
        $productValue->TaxNet = $request['TaxNet'];
        $productValue->note = $request['note'];
        $productValue->is_imei = $request->has('is_imei') ? 1 : 0;
        $productValue->not_selling = $request->has('not_selling') ? 1 : 0;

        $productValue->save();

        return redirect()->route('product.index')->with('success', 'Product created successfully');
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
