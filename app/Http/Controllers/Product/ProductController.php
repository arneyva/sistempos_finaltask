<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductWarehouse;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        try {
            DB::beginTransaction();
            // rules produk utama
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
            // memasukan data ke produk utama
            $productValue = new Product();
            $productValue->type = $request['type'];
            if ($request['type'] == 'is_single') {
                $productValue->cost = $request['cost'];
                $productValue->price = $request['price'];
                $productValue->is_variant = 0;
            } else {
                $productValue->cost = 0;
                $productValue->price = 0;
                $productValue->is_variant = 1;
            }
            $productValue->name = $request['name'];
            $productValue->code = $request['code'];
            $productValue->Type_barcode = 'CODE128';
            $productValue->tax_method = 'Exclusive';
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
            // handle rules produk bervariant
            if ($request->type == 'is_variant') {
                $productRules['variants'] = [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        // check if array is not empty
                        if (empty($value)) {
                            $fail('The variants array is required.');

                            return;
                        }
                        // check for duplicate codes in variants array
                        $variants = json_decode($request->variants, true);
                        if ($variants) {
                            foreach ($variants as $variant) {
                                if (! array_key_exists('text', $variant) || empty($variant['text'])) {
                                    $fail('Variant Name cannot be empty.');

                                    return;
                                } elseif (! array_key_exists('code', $variant) || empty($variant['code'])) {
                                    $fail('Variant code cannot be empty.');

                                    return;
                                } elseif (! array_key_exists('cost', $variant) || empty($variant['cost'])) {
                                    $fail('Variant cost cannot be empty.');

                                    return;
                                } elseif (! array_key_exists('price', $variant) || empty($variant['price'])) {
                                    $fail('Variant price cannot be empty.');

                                    return;
                                }
                            }
                        } else {
                            $fail('The variants data is invalid.');

                            return;
                        }
                        //check if variant name empty
                        $names = array_column($variants, 'text');
                        if ($names) {
                            foreach ($names as $name) {
                                if (empty($name)) {
                                    $fail('Variant Name cannot be empty.');

                                    return;
                                }
                            }
                        } else {
                            $fail('Variant Name cannot be empty.');

                            return;
                        }
                        //check if variant cost empty
                        $all_cost = array_column($variants, 'cost');
                        if ($all_cost) {
                            foreach ($all_cost as $cost) {
                                if (empty($cost)) {
                                    $fail('Variant Cost cannot be empty.');

                                    return;
                                }
                            }
                        } else {
                            $fail('Variant Cost cannot be empty.');

                            return;
                        }
                        //check if variant price empty
                        $all_price = array_column($variants, 'price');
                        if ($all_price) {
                            foreach ($all_price as $price) {
                                if (empty($price)) {
                                    $fail('Variant Price cannot be empty.');

                                    return;
                                }
                            }
                        } else {
                            $fail('Variant Price cannot be empty.');

                            return;
                        }
                        //check if code empty
                        $codes = array_column($variants, 'code');
                        if ($codes) {
                            foreach ($codes as $code) {
                                if (empty($code)) {
                                    $fail('Variant code cannot be empty.');

                                    return;
                                }
                            }
                        } else {
                            $fail('Variant code cannot be empty.');

                            return;
                        }
                        //check if code Duplicate
                        if (count(array_unique($codes)) !== count($codes)) {
                            $fail('Duplicate codes found in variants array.');

                            return;
                        }
                        // check for duplicate codes in product_variants table
                        $duplicateCodes = DB::table('product_variants')
                            ->whereIn('code', $codes)
                            ->whereNull('deleted_at')
                            ->pluck('code')
                            ->toArray();
                        if (! empty($duplicateCodes)) {
                            $fail('This code : '.implode(', ', $duplicateCodes).' already used');
                        }
                        // check for duplicate codes in products table
                        $duplicateCodes_products = DB::table('products')
                            ->whereIn('code', $codes)
                            ->whereNull('deleted_at')
                            ->pluck('code')
                            ->toArray();
                        if (! empty($duplicateCodes_products)) {
                            $fail('This code : '.implode(', ', $duplicateCodes_products).' already used');
                        }
                    },
                ];
            }
            // Store Variants Product
            if ($request['type'] == 'is_variant') {
                $variants = json_decode($request->variants);

                foreach ($variants as $variant) {
                    $Product_variants_data[] = [
                        'product_id' => $productValue->id,
                        'name' => $variant->name,
                        'cost' => $variant->cost,
                        'price' => $variant->price,
                        'code' => $variant->code,
                    ];
                }
                ProductVariant::insert($Product_variants_data);
            }
            // proses managament stock di outlet/warehouse
            $warehouse = Warehouse::where('deleted_at', null)->pluck('id')->toArray();
            $productVariants = ProductVariant::where('product_id', $productValue->id)->whereNull('deleted_at')->get();
            foreach ($warehouse as $warehouse) {
                // handle product variants
                if ($productValue->is_variant == 1) {
                    foreach ($productVariants as $productVariant) {
                        $product_warehouse[] = [
                            'product_id' => $productValue->id,
                            'warehouse_id' => $warehouse,
                            'product_variant_id' => $productVariant->id,
                            'manage_stock' => 1,

                        ];
                    }
                } else {
                    $product_warehouse[] = [
                        'product_id' => $productValue->id,
                        'warehouse_id' => $warehouse,
                        'manage_stock' => 1,

                    ];
                }
            }
            ProductWarehouse::insert($product_warehouse);
            DB::commit();

            return redirect()->route('product.index')->with('success', 'Product created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
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
