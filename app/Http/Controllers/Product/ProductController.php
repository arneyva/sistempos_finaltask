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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // ambil data product utama
        $products = Product::with('unit', 'category', 'brand')->where('deleted_at', '=', null)->latest()->paginate(10);
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
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // dd(Auth::user()->getRoleNames());

        $category = Category::query()->get();
        $brand = Brand::query()->get();
        $unit = Unit::query()->where('base_unit', null)->get();
        if (Auth::user()->can('create product')) {
            return view('templates.product.create', [
                'category' => $category,
                'brand' => $brand,
                'unit' => $unit,
            ]);   // code...
        } else {
            return redirect()->back()->with('errorzz', 'You are not authorized to create product');
        }
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
        $product = Product::where('deleted_at', '=', null)->findOrFail($id);
        // belom  pakai spatie
        $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
        $item['id'] = $product->id;
        $item['type'] = $product->type;
        $item['code'] = $product->code;
        $item['Type_barcode'] = $product->Type_barcode;
        $item['name'] = $product->name;
        $item['cost'] = $product->cost;
        $item['tax'] = $product->TaxNet;
        $item['price'] = $product->price;
        $item['cateogry'] = $product['category']->name;
        $item['brand'] = $product['brand']->name ?? 'N/D';
        $item['unit'] = $product['unit']->ShortName;
        // type
        if ($product->type == 'is_single') {
            $item['type_name'] = 'Single';
            $item['unit'] = $product['unit']->ShortName;
        } else {
            $item['type_name'] = 'Variant';
            $item['unit'] = $product['unit']->ShortName;
        }
        // is variant
        if ($product->is_variant) {
            $item['is_variant'] = 'Yes';
            $productVariants = ProductVariant::where('product_id', $product->id)->where('deleted_at', '=', null)->get();
            foreach ($productVariants as $variant) {
                $ProductVariant['code'] = $variant->code;
                $ProductVariant['name'] = $variant->name;
                $ProductVariant['cost'] = $variant->cost;
                $ProductVariant['price'] = $variant->price;

                // hitung kuantitas produk
                $item['products_variants_data'][] = $ProductVariant;
                foreach ($warehouses as $warehouse) {
                    $product_warehouse = DB::table('product_warehouse')
                        ->where('product_id', $id)
                        ->where('deleted_at', '=', null)
                        ->where('warehouse_id', $warehouse->id)
                        ->where('product_variant_id', $variant->id)
                        ->select(DB::raw('SUM(product_warehouse.qty) AS sum'))
                        ->first();

                    $war_var['mag'] = $warehouse->name;
                    $war_var['variant'] = $variant->name;
                    $war_var['qte'] = $product_warehouse->sum;
                    $item['CountQTY_variants'][] = $war_var;
                }
            }
        } else {
            $item['is_variant'] = 'No';
            $item['CountQTY_variants'] = [];
        }
        foreach ($warehouses as $warehouse) {
            $product_warehouse_data = DB::table('product_warehouse')
                ->where('product_id', $id)
                ->where('deleted_at', '=', null)
                ->where('warehouse_id', $warehouse->id)
                ->select(DB::raw('SUM(product_warehouse.qty) AS sum'))
                ->first();

            $war['mag'] = $warehouse->name;
            $war['qty'] = $product_warehouse_data->sum;
            $item['CountQTY'][] = $war;
        }
        $data[] = $item;

        // dd($data);
        return view('templates.product.show', [
            'data' => $data,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // $this->authorizeForUser($request->user('api'), 'update', Product::class);

        $Product = Product::where('deleted_at', '=', null)->findOrFail($id);

        $item['id'] = $Product->id;
        $item['type'] = $Product->type;
        $item['code'] = $Product->code;
        $item['Type_barcode'] = $Product->Type_barcode;
        $item['name'] = $Product->name;
        if ($Product->category_id) {
            if (Category::where('id', $Product->category_id)
                ->where('deleted_at', '=', null)
                ->first()
            ) {
                $item['category_id'] = $Product->category_id;
            } else {
                $item['category_id'] = '';
            }
        } else {
            $item['category_id'] = '';
        }

        if ($Product->brand_id) {
            if (Brand::where('id', $Product->brand_id)
                ->where('deleted_at', '=', null)
                ->first()
            ) {
                $item['brand_id'] = $Product->brand_id;
            } else {
                $item['brand_id'] = '';
            }
        } else {
            $item['brand_id'] = '';
        }

        if ($Product->unit_id) {
            if (Unit::where('id', $Product->unit_id)
                ->where('deleted_at', '=', null)
                ->first()
            ) {
                $item['unit_id'] = $Product->unit_id;
            } else {
                $item['unit_id'] = '';
            }

            if (Unit::where('id', $Product->unit_sale_id)
                ->where('deleted_at', '=', null)
                ->first()
            ) {
                $item['unit_sale_id'] = $Product->unit_sale_id;
            } else {
                $item['unit_sale_id'] = '';
            }

            if (Unit::where('id', $Product->unit_purchase_id)
                ->where('deleted_at', '=', null)
                ->first()
            ) {
                $item['unit_purchase_id'] = $Product->unit_purchase_id;
            } else {
                $item['unit_purchase_id'] = '';
            }
        } else {
            $item['unit_id'] = '';
        }

        $item['tax_method'] = $Product->tax_method;
        $item['price'] = $Product->price;
        $item['cost'] = $Product->cost;
        // $item['stock_alert'] = $Product->stock_alert;
        $item['TaxNet'] = $Product->TaxNet;
        $item['note'] = $Product->note ? $Product->note : '';
        $item['images'] = [];
        if ($Product->image != '' && $Product->image != 'no-image.png') {
            foreach (explode(',', $Product->image) as $img) {
                $path = public_path().'/images/products/'.$img;
                if (file_exists($path)) {
                    $itemImg['name'] = $img;
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $itemImg['path'] = 'data:image/'.$type.';base64,'.base64_encode($data);

                    $item['images'][] = $itemImg;
                }
            }
        } else {
            $item['images'] = [];
        }

        if ($Product->type == 'is_variant') {
            $item['is_variant'] = true;
            $productsVariants = ProductVariant::where('product_id', $id)
                ->where('deleted_at', null)
                ->get();

            $var_id = 0;
            foreach ($productsVariants as $variant) {
                $variant_item['var_id'] = $var_id += 1;
                $variant_item['id'] = $variant->id;
                $variant_item['name'] = $variant->name;
                $variant_item['code'] = $variant->code;
                $variant_item['price'] = $variant->price;
                $variant_item['cost'] = $variant->cost;
                $variant_item['product_id'] = $variant->product_id;
                $item['ProductVariant'][] = $variant_item;
            }
        } else {
            $item['is_variant'] = false;
            $item['ProductVariant'] = [];
        }

        $item['is_imei'] = $Product->is_imei ? true : false;
        $item['not_selling'] = $Product->not_selling ? true : false;

        $data = $item;
        $categories = Category::where('deleted_at', null)->get(['id', 'name']);
        $brands = Brand::where('deleted_at', null)->get(['id', 'name']);

        $product_units = Unit::where('id', $Product->unit_id)
            ->orWhere('base_unit', $Product->unit_id)
            ->where('deleted_at', null)
            ->get();

        $units = Unit::where('deleted_at', null)
            ->where('base_unit', null)
            ->get();

        // return response()->json([
        //     'product' => $data,
        //     'categories' => $categories,
        //     'brands' => $brands,
        //     'units' => $units,
        //     'units_sub' => $product_units,
        // ]);
        // return view('templates.product.show', [
        //     'data' => $data,
        // ]);
        return view('templates.product.edit', [
            'product' => $data,
            'category' => $categories,
            'brand' => $brands,
            'unit' => $units,
            'units_sub' => $product_units,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->authorizeForUser($request->user('api'), 'update', Product::class);
        try {
            // define validation rules for product
            $productRules = [
                'code' => [
                    'required',
                    Rule::unique('products')->ignore($id)->where(function ($query) {
                        return $query->where('deleted_at', '=', null);
                    }),
                    Rule::unique('product_variants')->ignore($id, 'product_id')->where(function ($query) {
                        return $query->where('deleted_at', '=', null);
                    }),
                ],
                'name' => 'required',
                'category_id' => 'required',
                'tax_method' => 'required',
                'type' => 'required',
                'unit_id' => Rule::requiredIf($request->type != 'is_service'),
                'cost' => Rule::requiredIf($request->type == 'is_single'),
                'price' => Rule::requiredIf($request->type != 'is_variant'),
            ];
            // if type is not is_variant, add validation for variants array
            if ($request->type == 'is_variant') {
                $productRules['variants'] = [
                    'required',
                    function ($attribute, $value, $fail) use ($request, $id) {
                        // check if array is not empty
                        if (empty($value)) {
                            $fail('The variants array is required.');

                            return;
                        }
                        // check for duplicate codes in variants array
                        $variants = $request->variants;
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
                            ->where(function ($query) use ($id) {
                                $query->where('product_id', '<>', $id);
                            })
                            ->whereIn('code', $codes)
                            ->whereNull('deleted_at')
                            ->pluck('code')
                            ->toArray();
                        if (! empty($duplicateCodes)) {
                            $fail('This code : '.implode(', ', $duplicateCodes).' already used');
                        }
                        // check for duplicate codes in products table
                        $duplicateCodes_products = DB::table('products')
                            ->where('id', '!=', $id)
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
            // validate the request data
            $validatedData = $request->validate($productRules, [
                'code.unique' => 'Product code already used.',
                'code.required' => 'This field is required',
            ]);
            \DB::transaction(function () use ($request, $id) {
                $Product = Product::where('id', $id)
                    ->where('deleted_at', '=', null)
                    ->first();
                //-- Update Product
                $Product->type = $request['type'];
                $Product->name = $request['name'];
                $Product->code = $request['code'];
                $Product->Type_barcode = $request['Type_barcode'];
                $Product->category_id = $request['category_id'];
                $Product->brand_id = $request['brand_id'] == 'null' ? null : $request['brand_id'];
                $Product->TaxNet = $request['TaxNet'];
                $Product->tax_method = $request['tax_method'];
                $Product->note = $request['note'];
                //-- check if type is_single
                if ($request['type'] == 'is_single') {
                    $Product->price = $request['price'];
                    $Product->cost = $request['cost'];
                    $Product->unit_id = $request['unit_id'];
                    $Product->unit_sale_id = $request['unit_sale_id'] ? $request['unit_sale_id'] : $request['unit_id'];
                    $Product->unit_purchase_id = $request['unit_purchase_id'] ? $request['unit_purchase_id'] : $request['unit_id'];
                    $Product->stock_alert = $request['stock_alert'] ? $request['stock_alert'] : 0;
                    $Product->is_variant = 0;
                    $manage_stock = 1;
                    //-- check if type is_variant
                } elseif ($request['type'] == 'is_variant') {
                    $Product->price = 0;
                    $Product->cost = 0;
                    $Product->unit_id = $request['unit_id'];
                    $Product->unit_sale_id = $request['unit_sale_id'] ? $request['unit_sale_id'] : $request['unit_id'];
                    $Product->unit_purchase_id = $request['unit_purchase_id'] ? $request['unit_purchase_id'] : $request['unit_id'];
                    $Product->stock_alert = $request['stock_alert'] ? $request['stock_alert'] : 0;
                    $Product->is_variant = 1;
                    $manage_stock = 1;
                    //-- check if type is_service
                } else {
                    $Product->price = $request['price'];
                    $Product->cost = 0;
                    $Product->unit_id = null;
                    $Product->unit_sale_id = null;
                    $Product->unit_purchase_id = null;
                    $Product->stock_alert = 0;
                    $Product->is_variant = 0;
                    $manage_stock = 0;
                }
                $Product->is_imei = $request['is_imei'] == 'true' ? 1 : 0;
                $Product->not_selling = $request['not_selling'] == 'true' ? 1 : 0;
                // Store Variants Product
                $oldVariants = ProductVariant::where('product_id', $id)
                    ->where('deleted_at', null)
                    ->get();
                $warehouses = Warehouse::where('deleted_at', null)
                    ->pluck('id')
                    ->toArray();
                if ($request['type'] == 'is_variant') {
                    if ($oldVariants->isNotEmpty()) {
                        $new_variants_id = [];
                        $var = 'id';
                        foreach ($request['variants'] as $new_id) {
                            if (array_key_exists($var, $new_id)) {
                                $new_variants_id[] = $new_id['id'];
                            } else {
                                $new_variants_id[] = 0;
                            }
                        }
                        foreach ($oldVariants as $key => $value) {
                            $old_variants_id[] = $value->id;
                            // Delete Variant
                            if (! in_array($old_variants_id[$key], $new_variants_id)) {
                                $ProductVariant = ProductVariant::findOrFail($value->id);
                                $ProductVariant->deleted_at = Carbon::now();
                                $ProductVariant->save();
                                $ProductWarehouse = product_warehouse::where('product_variant_id', $value->id)
                                    ->update(['deleted_at' => Carbon::now()]);
                            }
                        }
                        foreach ($request['variants'] as $key => $variant) {
                            if (array_key_exists($var, $variant)) {
                                $ProductVariantDT = new ProductVariant;
                                //-- Field Required
                                $ProductVariantDT->product_id = $variant['product_id'];
                                $ProductVariantDT->name = $variant['text'];
                                $ProductVariantDT->price = $variant['price'];
                                $ProductVariantDT->cost = $variant['cost'];
                                $ProductVariantDT->code = $variant['code'];

                                $ProductVariantUP['product_id'] = $variant['product_id'];
                                $ProductVariantUP['code'] = $variant['code'];
                                $ProductVariantUP['name'] = $variant['text'];
                                $ProductVariantUP['price'] = $variant['price'];
                                $ProductVariantUP['cost'] = $variant['cost'];
                            } else {
                                $ProductVariantDT = new ProductVariant;
                                //-- Field Required
                                $ProductVariantDT->product_id = $id;
                                $ProductVariantDT->code = $variant['code'];
                                $ProductVariantDT->name = $variant['text'];
                                $ProductVariantDT->price = $variant['price'];
                                $ProductVariantDT->cost = $variant['cost'];
                                $ProductVariantUP['product_id'] = $id;
                                $ProductVariantUP['code'] = $variant['code'];
                                $ProductVariantUP['name'] = $variant['text'];
                                $ProductVariantUP['price'] = $variant['price'];
                                $ProductVariantUP['cost'] = $variant['cost'];
                                $ProductVariantUP['qty'] = 0.00;
                            }
                            if (! in_array($new_variants_id[$key], $old_variants_id)) {
                                $ProductVariantDT->save();
                                //--Store Product warehouse
                                if ($warehouses) {
                                    $product_warehouse = [];
                                    foreach ($warehouses as $warehouse) {
                                        $product_warehouse[] = [
                                            'product_id' => $id,
                                            'warehouse_id' => $warehouse,
                                            'product_variant_id' => $ProductVariantDT->id,
                                            'manage_stock' => $manage_stock,
                                        ];
                                    }
                                    product_warehouse::insert($product_warehouse);
                                }
                            } else {
                                ProductVariant::where('id', $variant['id'])->update($ProductVariantUP);
                            }
                        }
                    } else {
                        $ProducttWarehouse = product_warehouse::where('product_id', $id)
                            ->update([
                                'deleted_at' => Carbon::now(),
                            ]);
                        foreach ($request['variants'] as $variant) {
                            $product_warehouse_DT = [];
                            $ProductVarDT = new ProductVariant;
                            //-- Field Required
                            $ProductVarDT->product_id = $id;
                            $ProductVarDT->code = $variant['code'];
                            $ProductVarDT->name = $variant['text'];
                            $ProductVarDT->cost = $variant['cost'];
                            $ProductVarDT->price = $variant['price'];
                            $ProductVarDT->save();
                            //-- Store Product warehouse
                            if ($warehouses) {
                                foreach ($warehouses as $warehouse) {
                                    $product_warehouse_DT[] = [
                                        'product_id' => $id,
                                        'warehouse_id' => $warehouse,
                                        'product_variant_id' => $ProductVarDT->id,
                                        'manage_stock' => $manage_stock,
                                    ];
                                }
                                product_warehouse::insert($product_warehouse_DT);
                            }
                        }
                    }
                } else {
                    if ($oldVariants->isNotEmpty()) {
                        foreach ($oldVariants as $old_var) {
                            $var_old = ProductVariant::where('product_id', $old_var['product_id'])
                                ->where('deleted_at', null)
                                ->first();
                            $var_old->deleted_at = Carbon::now();
                            $var_old->save();
                            $ProducttWarehouse = product_warehouse::where('product_variant_id', $old_var['id'])
                                ->update([
                                    'deleted_at' => Carbon::now(),
                                ]);
                        }
                        if ($warehouses) {
                            foreach ($warehouses as $warehouse) {
                                $product_warehouse[] = [
                                    'product_id' => $id,
                                    'warehouse_id' => $warehouse,
                                    'product_variant_id' => null,
                                    'manage_stock' => $manage_stock,
                                ];
                            }
                            product_warehouse::insert($product_warehouse);
                        }
                    }
                }
                if ($request['images'] === null) {
                    if ($Product->image !== null) {
                        foreach (explode(',', $Product->image) as $img) {
                            $pathIMG = public_path().'/images/products/'.$img;
                            if (file_exists($pathIMG)) {
                                if ($img != 'no-image.png') {
                                    @unlink($pathIMG);
                                }
                            }
                        }
                    }
                    $filename = 'no-image.png';
                } else {
                    if ($Product->image !== null) {
                        foreach (explode(',', $Product->image) as $img) {
                            $pathIMG = public_path().'/images/products/'.$img;
                            if (file_exists($pathIMG)) {
                                if ($img != 'no-image.png') {
                                    @unlink($pathIMG);
                                }
                            }
                        }
                    }
                    $files = $request['images'];
                    foreach ($files as $file) {
                        $fileData = ImageResize::createFromString(base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $file['path'])));
                        $fileData->resize(200, 200);
                        $name = rand(11111111, 99999999).$file['name'];
                        $path = public_path().'/images/products/';
                        $success = file_put_contents($path.$name, $fileData);
                        $images[] = $name;
                    }
                    $filename = implode(',', $images);
                }
                $Product->image = $filename;
                $Product->save();
            }, 10);

            return response()->json(['success' => true]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 422,
                'msg' => 'error',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
