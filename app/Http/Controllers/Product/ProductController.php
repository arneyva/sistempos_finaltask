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
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $product = Product::query()->latest()->get();

    //     return view('templates.product.index', [
    //         'product' => $product,
    //     ]);
    // }
    public function index()
    {
        // ambil data product utama
        $products = Product::with('unit', 'category', 'brand')->where('deleted_at', '=', null)->get();
        //ambil data
        $items = [];
        foreach ($products as $product) {
            $item['id'] = $product->id;
            $item['code'] = $product->code;
            $item['category'] = $product['category']->name;
            $item['brand'] = $product['brand']->name;
            // untuk product single
            if ($product->type == 'is_single') {
                $item['name'] = $product->name;
                $item['type'] = 'Single Product';
                $item['cost'] = $product->cost;
                $item['price'] = $product->price;
                $item['unit'] = $product['unit']->ShortName;
                // handle jumlah barang
                $product_warehouse_total_qty = ProductWarehouse::where('product_id', $product->id)->where('deleted_at', '=', null)->sum('qty');
                $item['quantity'] = $product_warehouse_total_qty.' '.$product['unit']->ShortName;
            } elseif ($product->type == 'is_variant') {
                //untuk product variant
                $item['type'] = 'Variant Product';
                $item['unit'] = $product['unit']->ShortName;
                $product_variant_data = ProductVariant::where('product_id', $product->id)
                    ->where('deleted_at', '=', null)
                    ->get();
                $variant_costs = [];
                $variant_price = [];
                $variant_name = [];
                foreach ($product_variant_data as $product_variant) {
                    $variant_costs[] = $product_variant->cost;
                    $variant_price[] = $product_variant->price;
                    $variant_name[] = $product_variant->name;
                }
                $item['cost'] = $variant_costs;
                $item['price'] = $variant_price;
                $item['name'] = $variant_name;
                // handle jumlah barang
                $product_warehouse_total_qty = ProductWarehouse::where('product_id', $product->id)->where('deleted_at', '=', null)->sum('qty');
                $item['quantity'] = $product_warehouse_total_qty.' '.$product['unit']->ShortName;
            }
            $items[] = $item;
        }

        // dd($items);
        return view('templates.product.index', [
            'items' => $items,
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
            // session jika gagal
            Session::flash('name', $request->name);
            Session::flash('code', $request->code);
            Session::flash('cost', $request->cost);
            Session::flash('price', $request->price);
            Session::flash('TaxNet', $request->TaxNet);
            Session::flash('note', $request->note);
            // rules produk utama
            $productRules = $request->validate([
                'type' => 'required',
                'code' => [
                    'required',
                    Rule::unique('products')->where(function ($query) {
                        return $query->where('deleted_at', '=', null);
                    }),
                    Rule::unique('product_variants')->where(function ($query) {
                        return $query->where('deleted_at', '=', null);
                    }),
                ],
                'name' => [
                    'required',
                    Rule::unique(Product::class, 'name')->whereNull('deleted_at'),
                ],
                'cost' => [
                    Rule::requiredIf($request->type == 'is_single'),
                    'numeric', 'regex:/^\d+(\.\d{1,2})?$/',
                ],
                'price' => [
                    Rule::requiredIf($request->type == 'is_single'),
                    'numeric', 'regex:/^\d+(\.\d{1,2})?$/',
                ],
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
                'TaxNet' => 'required|numeric',
                'image' => 'nullable|image|mimes:jpeg,png,jpg',
                'note' => 'nullable',
                'is_imei' => 'nullable',
                'not_selling' => 'nullable',
            ]);
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
                ];
            }
            // Store Variants Product
            if ($request['type'] == 'is_variant') {
                $variants = json_decode($request->variants);
                $errors = [];

                foreach ($variants as $variant) {
                    if (ProductVariant::where('code', $variant->code)->exists()) {
                        $errors[] = 'The code '.$variant->code.' has already been taken.';
                    } else {
                        $Product_variants_data[] = [
                            'product_id' => $productValue->id,
                            'name' => $variant->name,
                            'cost' => $variant->cost,
                            'price' => $variant->price,
                            'code' => $variant->code,
                        ];
                    }
                }

                if (! empty($errors)) {
                    return redirect()->back()->withErrors(['variants' => $errors])->withInput();
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
