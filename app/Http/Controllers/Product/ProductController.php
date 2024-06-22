<?php

namespace App\Http\Controllers\Product;

use App\Exports\ProductsExport;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductWarehouse;
use App\Models\Unit;
use App\Models\UserWarehouse;
use App\Models\Warehouse;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user_auth = auth()->user();
        $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');
        $productsQuery = Product::with('unit', 'category', 'brand')->where('deleted_at', '=', null)->latest();
        $categories = Category::where('deleted_at', '=', null)->get(['id', 'name']);
        $brands = Brand::where('deleted_at', '=', null)->get(['id', 'name']);
        //   filtering Query
        if ($request->filled('code')) {
            $productsQuery->where('code', 'like', '%' . $request->input('code') . '%');
        }

        if ($request->filled('name')) {
            $productsQuery->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->filled('category_id')) {
            $productsQuery->where('category_id', '=', $request->input('category_id'));
        }
        if ($request->filled('brand_id')) {
            $productsQuery->where('brand_id', '=', $request->input('brand_id'));
        }
        // proses penyimpanan data product
        $products = $productsQuery->paginate($request->input('limit', 5))->appends($request->except('page'));
        $items = [];
        foreach ($products as $product) {
            $item['id'] = $product->id;
            $item['code'] = $product->code;
            $item['category'] = $product['category']->name;
            $item['brand'] = $product['brand']->name;
            $item['TaxNet'] = $product->TaxNet;
            // untuk product single
            if ($product->type == 'is_single') {
                $item['name'] = $product->name;
                $item['type'] = 'Single Product';
                $item['cost'] = 'Rp ' . number_format($product->cost, 2, ',', '.');
                $item['price'] = 'Rp ' . number_format($product->price, 2, ',', '.');
                $item['unit'] = $product['unit']->ShortName;
                // handle jumlah barang dan stock alert
                if ($user_auth->hasRole(['superadmin', 'inventaris'])) {
                    $product_warehouse_total_qty = ProductWarehouse::where('product_id', $product->id)->where('deleted_at', '=', null)->sum('qty');
                    $item['quantity'] = $product_warehouse_total_qty . ' ' . $product['unit']->ShortName;
                } else {
                    $product_warehouse_total_qty = ProductWarehouse::where('product_id', $product->id)->where('deleted_at', '=', null)->where('warehouse_id', $warehouses_id)->sum('qty');
                    $product_warehouse_stock_alert = ProductWarehouse::where('product_id', $product->id)->where('deleted_at', '=', null)->where('warehouse_id', $warehouses_id)->sum('stock_alert');
                    $item['quantity'] = $product_warehouse_total_qty . ' ' . $product['unit']->ShortName;
                    $item['stock_alert'] = $product_warehouse_stock_alert . ' ' . $product['unit']->ShortName;
                }
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
                // handle jumlah barang dan stock alert
                if ($user_auth->hasRole(['superadmin', 'inventaris'])) {
                    $product_warehouse_total_qty = ProductWarehouse::where('product_id', $product->id)->where('deleted_at', '=', null)->sum('qty');
                    $item['quantity'] = $product_warehouse_total_qty . ' ' . $product['unit']->ShortName;
                } else {
                    $product_warehouse_total_qty = ProductWarehouse::where('product_id', $product->id)->where('deleted_at', '=', null)->where('warehouse_id', $warehouses_id)->sum('qty');
                    $product_warehouse_stock_alert = ProductWarehouse::where('product_id', $product->id)->where('deleted_at', '=', null)->where('warehouse_id', $warehouses_id)->sum('stock_alert');
                    $item['quantity'] = $product_warehouse_total_qty . ' ' . $product['unit']->ShortName;
                    $item['stock_alert'] = $product_warehouse_stock_alert . ' ' . $product['unit']->ShortName;
                }
            }
            $items[] = $item;
        }
        return view('templates.product.index', [
            'items' => $items,
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }

    public function exportToPDF(Request $request)
    {
        // ambil data product 
        $productsQuery = Product::with('unit', 'category', 'brand')->where('deleted_at', '=', null);
        //ambil data berdasarkan filter
        if ($request->filled('code')) {
            $productsQuery->where('code', 'like', '%' . $request->input('code') . '%');
        }
        if ($request->filled('name')) {
            $productsQuery->where('name', 'like', '%' . $request->input('name') . '%');
        }
        if ($request->filled('category_id')) {
            $productsQuery->where('category_id', '=', $request->input('category_id'));
        }
        if ($request->filled('brand_id')) {
            $productsQuery->where('brand_id', '=', $request->input('brand_id'));
        }
        // mendapatkan data product hasil filter
        $products = $productsQuery->get();
        // Generate PDF
        $pdf = Pdf::loadView('export.product', compact('products')); //mengirimkan data ke blade
        // penamaan file
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        return $pdf->download("products_{$timestamp}.pdf");
    }

    public function export(Request $request)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "products_{$timestamp}.xlsx";

        return Excel::download(new ProductsExport($request), $filename);
    }

    public function check_code_exist($code)
    {
        $check_code = Product::where('code', $code)->first();
        if ($check_code) {
            $this->generate_random_code($code);
        } else {
            return $code;
        }
    }

    private function generateUniqueCategoryCode()
    {
        return 'CAT-IMPORT-' . Str::random(4);
    }

    public function import_products(Request $request)
    {
        $file_upload = $request->file('products'); // Menyimpan data file yang diupload
        // Pengecekan tipe file
        $ext = pathinfo($file_upload->getClientOriginalName(), PATHINFO_EXTENSION); // Mendapatkan ekstensi file
        if ($ext != 'csv') {
            return redirect()->back()->with('error', 'must be in csv format'); // Mengembalikan pesan error jika bukan CSV
        } else {
            $data = [];
            $rowcount = 0;
            if (($handle = fopen($file_upload, 'r')) !== false) { // Membuka file CSV
                $max_line_length = defined('MAX_LINE_LENGTH') ? MAX_LINE_LENGTH : 10000; // Mendefinisikan panjang baris maksimal
                $header = fgetcsv($handle, $max_line_length, ';'); // Membaca header dari file CSV
                $header_colcount = count($header); // Menghitung jumlah kolom pada header
                while (($row = fgetcsv($handle, $max_line_length, ';')) !== false) {
                    $row_colcount = count($row); // Menghitung jumlah kolom pada baris
                    if ($row_colcount == $header_colcount) {
                        $entry = array_combine($header, $row); // Menggabungkan header dengan baris untuk membuat array asosiatif
                        $data[] = $entry; // Menambahkan data ke array data
                    } else {
                        return null; // Mengembalikan null jika jumlah kolom tidak cocok
                    }
                    $rowcount++;
                }
                fclose($handle); // Menutup file CSV
            } else {
                return null; // Mengembalikan null jika file tidak bisa dibuka
            }
            $warehouses = Warehouse::where('deleted_at', null)->pluck('id')->toArray(); // Mengambil ID warehouse yang tidak terhapus
            $validator = validator()->make($data, [
                '*.name' => 'required', // Validasi nama harus ada
                '*.code' => 'required|unique:products', // Validasi kode harus unik
            ]);

            if ($validator->fails()) {
                // Validasi gagal
                return response()->json([
                    'msg' => 'Validation failed', // Pesan error
                    'errors' => $validator->errors(), // Error detail
                    'status' => false,
                ]);
            }

            try {
                \DB::transaction(function () use ($data, $warehouses) {
                    //-- Membuat Produk Baru
                    foreach ($data as $key => $value) {
                        $category = Category::where('deleted_at', null)->firstOrCreate(
                            ['name' => $value['category']],
                            ['code' => $this->generateUniqueCategoryCode()] // Membuat kategori baru jika tidak ada
                        );
                        $category_id = $category->id;
                        $unit = Unit::where(['ShortName' => $value['unit']])
                            ->orWhere(['name' => $value['unit']])
                            ->where('deleted_at', null)->first(); // Mendapatkan unit berdasarkan nama atau shortname
                        $unit_id = $unit->id;

                        if ($value['brand'] != 'N/A' && $value['brand'] != '') {
                            $brand = Brand::where('deleted_at', null)->firstOrCreate(['name' => $value['brand']]); // Membuat atau mendapatkan brand
                            $brand_id = $brand->id;
                        } else {
                            $brand_id = null; // Jika brand tidak ada atau N/A
                        }
                        $Product = new Product; // Membuat objek produk baru
                        $Product->name = $value['name'] == '' ? null : $value['name'];
                        $Product->code = $this->check_code_exist($value['code']); // Memeriksa kode produk
                        $Product->Type_barcode = 'CODE128';
                        $Product->type = 'is_single';
                        $Product->price = $value['price'];
                        $Product->cost = $value['cost'];
                        $Product->category_id = $category_id;
                        $Product->brand_id = $brand_id;
                        $Product->TaxNet = 0;
                        $Product->tax_method = 'Exclusive';
                        $Product->note = $value['note'] ? $value['note'] : '';
                        $Product->unit_id = $unit_id;
                        $Product->unit_sale_id = $unit_id;
                        $Product->unit_purchase_id = $unit_id;
                        $Product->is_variant = 0;
                        $Product->image = 'no-image.png';
                        $Product->save(); // Menyimpan produk ke database

                        if ($warehouses) {
                            foreach ($warehouses as $warehouse) {
                                $product_warehouse[] = [
                                    'product_id' => $Product->id,
                                    'warehouse_id' => $warehouse, // Menyimpan produk ke warehouse terkait
                                ];
                            }
                        }
                    }
                    if ($warehouses) {
                        ProductWarehouse::insert($product_warehouse); // Memasukkan data ke tabel product_warehouse
                    }
                }, 10);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Invalid data format'); // Menangkap exception dan mengembalikan pesan error
            }
        }
        return redirect()->route('product.index')->with('success', 'Product Imported successfully'); // Mengembalikan pesan sukses
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // menyiapkan data sub modul product
        $category = Category::query()->get();
        $brand = Brand::query()->get();
        $unit = Unit::query()->where('base_unit', null)->get();
        // cek hak otoritas user
        if (Auth::user()->can('create product')) {
            return view('templates.product.create', [
                'category' => $category,
                'brand' => $brand,
                'unit' => $unit,
            ]);
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
            // menggunakan session agar jika gagal data lama bisa digunakan
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
            // Prose store data produk
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
            // Proses Store Product Variants 
            if ($request['type'] == 'is_variant') {
                $variants = json_decode($request->variants); // konversi string JSON menjadi objek atau array
                $errors = [];
                foreach ($variants as $variant) {
                    if (ProductVariant::where('code', $variant->code)->exists()) {
                        $errors[] = 'The code ' . $variant->code . ' has already been taken.';
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
                if (!empty($errors)) {
                    return redirect()->back()->withErrors(['variants' => $errors])->withInput();
                }
                ProductVariant::insert($Product_variants_data); //memasukan data ke database
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
            DB::commit(); //jika tidak terjadi masalah dari awal sampai akhir maka commit
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
        // mendapatkan data product berdasrkan id
        $product = Product::where('deleted_at', '=', null)->findOrFail($id);
        $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
        // proses penyimpanan data untuk nanti digunakan di blade
        $item['id'] = $product->id;
        $item['type'] = $product->type;
        $item['code'] = $product->code;
        $item['Type_barcode'] = $product->Type_barcode;
        $item['name'] = $product->name;
        $item['cost'] =  'Rp ' . number_format($product->cost, 2, ',', '.');
        $item['tax'] = $product->TaxNet;
        $item['price'] = 'Rp ' . number_format($product->price, 2, ',', '.');
        $item['cateogry'] = $product['category']->name;
        $item['brand'] = $product['brand']->name ?? 'N/D';
        $item['unit'] = $product['unit']->ShortName;
        // handle type product single
        if ($product->type == 'is_single') {
            $item['type_name'] = 'Single';
            $item['unit'] = $product['unit']->ShortName;
        } else {
            $item['type_name'] = 'Variant';
            $item['unit'] = $product['unit']->ShortName;
        }
        // handle type product variant
        if ($product->is_variant) {
            $item['is_variant'] = 'Yes';
            $productVariants = ProductVariant::where('product_id', $product->id)->where('deleted_at', '=', null)->get();
            foreach ($productVariants as $variant) {
                $ProductVariant['code'] = $variant->code;
                $ProductVariant['name'] = $variant->name;
                $ProductVariant['cost'] = 'Rp ' . number_format($variant->cost, 2, ',', '.');
                $ProductVariant['price'] = 'Rp ' . number_format($variant->price, 2, ',', '.');
                // hitung kuantitas produk dan tampilkan stock alert
                $item['products_variants_data'][] = $ProductVariant;
                foreach ($warehouses as $warehouse) {
                    $product_warehouse = DB::table('product_warehouse')
                        ->where('product_id', $id)
                        ->where('deleted_at', '=', null)
                        ->where('warehouse_id', $warehouse->id)
                        ->where('product_variant_id', $variant->id)
                        ->select(DB::raw('SUM(product_warehouse.qty) AS sum, stock_alert'))
                        ->first();
                    $war_var['mag'] = $warehouse->name;
                    $war_var['variant'] = $variant->name;
                    $war_var['qte'] = $product_warehouse->sum;
                    $war_var['stock_alert'] = $product_warehouse->stock_alert;
                    $item['CountQTY_variants'][] = $war_var;
                }
            }
        } else {
            $item['is_variant'] = 'No';
            $item['CountQTY_variants'] = [];
        }
        // hitung kuantitas produk dan tampilkan stock alert bertipe single
        foreach ($warehouses as $warehouse) {
            $product_warehouse_data = DB::table('product_warehouse')
                ->where('product_id', $id)
                ->where('deleted_at', '=', null)
                ->where('warehouse_id', $warehouse->id)
                ->select(DB::raw('SUM(product_warehouse.qty) AS sum, stock_alert'))
                ->first();

            $war['mag'] = $warehouse->name;
            $war['qty'] = $product_warehouse_data->sum;
            $war['stock_alert'] = $product_warehouse_data->stock_alert; // Tambahkan stock_alert
            $item['CountQTY'][] = $war;
        }

        $data[] = $item;

        return view('templates.product.show', [
            'data' => $data,
        ]);
    }

    public function updateAlertStock(Request $request)
    {
        $product_id = $request->input('product_id');
        $stock_alerts = $request->input('stock_alert');

        // Mengambil informasi produk
        $product = Product::findOrFail($product_id);

        // Jika produk adalah tipe variant
        if ($product->is_variant) {
            foreach ($stock_alerts as $variant_name => $warehouses) {
                foreach ($warehouses as $warehouse_name => $stock_alert) {
                    $variant = ProductVariant::where('name', $variant_name)->first();
                    $warehouseModel = Warehouse::where('name', $warehouse_name)->first();

                    if ($variant && $warehouseModel) {
                        DB::table('product_warehouse')
                            ->where('product_id', $product_id)
                            ->where('product_variant_id', $variant->id)
                            ->where('warehouse_id', $warehouseModel->id)
                            ->update(['stock_alert' => $stock_alert]);
                    }
                }
            }
        } else {
            // Jika produk bukan tipe variant
            foreach ($stock_alerts as $warehouse_name => $stock_alert) {
                $warehouseModel = Warehouse::where('name', $warehouse_name)->first();

                if ($warehouseModel) {
                    DB::table('product_warehouse')
                        ->where('product_id', $product_id)
                        ->whereNull('product_variant_id') // Untuk produk bukan variant, product_variant_id = NULL
                        ->where('warehouse_id', $warehouseModel->id)
                        ->update(['stock_alert' => $stock_alert]);
                }
            }
        }

        return redirect()->route('product.index')->with('success', 'Stock alert updated successfully.');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $Product = Product::where('deleted_at', '=', null)->findOrFail($id); //mendapatkan data product berdasarkan id
        // proses penyimpanan data product yang nanti akan ditampilkan di client
        $item['id'] = $Product->id;
        $item['type'] = $Product->type;
        $item['code'] = $Product->code;
        $item['Type_barcode'] = $Product->Type_barcode;
        $item['name'] = $Product->name;
        if ($Product->category_id) { //logika untuk mengecek apakah data product memiliki category_id
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
        if ($Product->brand_id) { //logika untuk mengecek apakah data product memiliki brand_id
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
        if ($Product->unit_id) { //logika untuk mengecek apakah data product memiliki unit_id
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
        $item['TaxNet'] = $Product->TaxNet;
        $item['note'] = $Product->note ? $Product->note : '';
        $item['images'] = [];
        if ($Product->image != '' && $Product->image != 'no-image.png') { // proses menghandle image
            foreach (explode(',', $Product->image) as $img) {
                $path = public_path() . '/images/products/' . $img;
                if (file_exists($path)) {
                    $itemImg['name'] = $img;
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $itemImg['path'] = 'data:image/' . $type . ';base64,' . base64_encode($data);

                    $item['images'][] = $itemImg;
                }
            }
        } else {
            $item['images'] = [];
        }
        if ($Product->type == 'is_variant') { //proses penyimpanan dan menghandle yang bertipe variant
            $item['is_variant'] = true;
            $productsVariants = ProductVariant::where('product_id', $id)
                ->where('deleted_at', null)
                ->get(); //mendapatkan data variant
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
        try {
            // rules data product
            $productRules = $request->validate([
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
                'type' => 'required',
                'unit_id' => Rule::requiredIf($request->type != 'is_service'),
                'cost' => Rule::requiredIf($request->type == 'is_single'),
                'price' => Rule::requiredIf($request->type != 'is_variant'),
            ]);
            // berikan rules untuk type produk ber tipe variant
            if ($request->type == 'is_variant') {
                $productRules['variants'] = [
                    'required',
                    'array',

                ];
            }
            // proses validasi data
            \DB::transaction(function () use ($request, $id) {
                $Product = Product::where('id', $id)
                    ->where('deleted_at', '=', null)
                    ->first();
                //-- proses mengupdate Update Product
                $Product->type = $request['type'];
                $Product->name = $request['name'];
                $Product->code = $request['code'];
                $Product->Type_barcode = 'CODE128';
                $Product->category_id = $request['category_id'];
                $Product->brand_id = $request['brand_id'] == 'null' ? null : $request['brand_id'];
                $Product->TaxNet = $request['TaxNet'];
                $Product->tax_method = 'Exclusive';
                $Product->note = $request['note'];
                //-- update data type single
                if ($request['type'] == 'is_single') {
                    $Product->price = $request['price'];
                    $Product->cost = $request['cost'];
                    $Product->unit_id = $request['unit_id'];
                    $Product->unit_sale_id = $request['unit_sale_id'] ? $request['unit_sale_id'] : $request['unit_id'];
                    $Product->unit_purchase_id = $request['unit_purchase_id'] ? $request['unit_purchase_id'] : $request['unit_id'];
                    $Product->is_variant = 0;
                    //-- update data type variant
                } elseif ($request['type'] == 'is_variant') {
                    $Product->price = 0;
                    $Product->cost = 0;
                    $Product->unit_id = $request['unit_id'];
                    $Product->unit_sale_id = $request['unit_sale_id'] ? $request['unit_sale_id'] : $request['unit_id'];
                    $Product->unit_purchase_id = $request['unit_purchase_id'] ? $request['unit_purchase_id'] : $request['unit_id'];
                    $Product->is_variant = 1;
                } else {
                    $Product->price = $request['price'];
                    $Product->cost = 0;
                    $Product->unit_id = null;
                    $Product->unit_sale_id = null;
                    $Product->unit_purchase_id = null;
                    $Product->is_variant = 0;
                }
                $Product->is_imei = $request['is_imei'] == 'true' ? 1 : 0;
                $Product->not_selling = $request['not_selling'] == 'true' ? 1 : 0;
                $warehouses = Warehouse::where('deleted_at', null)->pluck('id')->toArray();
                // Update data varian 
                if ($request->variants) {
                    // Update data varian lama
                    foreach ($request->variants as $variantId => $variantData) {
                        $productVariant = ProductVariant::findOrFail($variantId);
                        $productVariant->name = $variantData['name'];
                        $productVariant->code = $variantData['code'];
                        $productVariant->cost = $variantData['cost'];
                        $productVariant->price = $variantData['price'];
                        $productVariant->save();
                    }
                    $existingVariantIds = collect($request->variants)->keys();
                    // jika variant lama ada yang dihapus
                    $oldVariants = ProductVariant::where('product_id', $Product->id)
                        ->whereNotIn('id', $existingVariantIds)
                        ->get();
                    foreach ($oldVariants as $oldVariant) {
                        ProductWarehouse::where('product_variant_id', $oldVariant->id)->delete();
                        $oldVariant->delete();
                    }
                }
                // Array untuk handle eror
                $errors = [];
                // Menambah data varian baru
                if ($request->new_variants) {
                    $newVariants = json_decode($request->new_variants, true);
                    $existingVariants = ProductVariant::where('product_id', $Product->id)->get();
                    foreach ($newVariants as $variantData) {
                        // cek apakah ada duplikat nama atau code
                        $duplicate = $existingVariants->first(function ($variant) use ($variantData) {
                            return $variant->name == $variantData['name'] || $variant->code == $variantData['code'];
                        });
                        if ($duplicate) {
                            $errors[] = "Duplicate variant found: Name '{$variantData['name']}' or Code '{$variantData['code']}' already exists."; //pesan eror
                            continue; // Skip saving this variant
                        }

                        // save varian baru jika tidak ada eror
                        $newVariant = ProductVariant::create([
                            'product_id' => $Product->id,
                            'name' => $variantData['name'],
                            'code' => $variantData['code'],
                            'cost' => $variantData['cost'],
                            'price' => $variantData['price'],
                        ]);
                        $product_warehouse = []; //menyimpan data di warehouse
                        foreach ($warehouses as $warehouse) {
                            $product_warehouse[] = [
                                'product_id' => $Product->id,
                                'warehouse_id' => $warehouse,
                                'product_variant_id' => $newVariant->id,
                                'manage_stock' => 1,
                            ];
                        }
                        ProductWarehouse::insert($product_warehouse);
                    }
                    if (!empty($errors)) {
                        return redirect()->back()->withErrors($errors)->withInput();
                    }
                } else {
                    return redirect()->route('product.edit', $id)->with('error', 'No new variants provided');
                }

                if ($request['images'] === null) {
                    if ($Product->image !== null) {
                        foreach (explode(',', $Product->image) as $img) {
                            $pathIMG = public_path() . '/images/products/' . $img;
                            if (file_exists($pathIMG)) {
                                if ($img != 'no-image.png') {
                                    @unlink($pathIMG);
                                }
                            }
                        }
                    }
                    $filename = 'no-image.png';
                }
                $Product->image = $filename;
                $Product->save();
            }, 10);

            return redirect()->route('product.index')->with('success', 'Product updated successfully');
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
    public function destroy($id)
    {
        if (Auth::user()->hasAnyRole(['superadmin', 'inventaris'])) {
            $product = Product::find($id);

            if (!$product) {
                return redirect()->back()->with('error', 'Product not found.');
            }
            // handle untuk mencegah pernghapusan
            if ($product->warehouse()->exists()) {
                return redirect()->back()->with('error', 'Product cannot be deleted because it is already used in another data.');
            }
            $product->delete();
            return redirect()->route('product.index')->with('success', 'Product deleted successfully');
        } else {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk melakukan tindakan ini');
        }
    }
}
