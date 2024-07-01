<?php

namespace App\Http\Controllers\purchase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Provider;
use App\Models\PaymentSale;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductWarehouse;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetails;
use App\Models\PaymentPurchase;
use App\Models\Setting;
use App\Models\Unit;
use App\Models\UserWarehouse;
use App\Models\Warehouse;
use Carbon\Carbon;
use App\Mail\PurchaseMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Picqer\Barcode\BarcodeGeneratorPNG;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:superadmin|inventaris');
    }

    public function index() {
        $orderBy = 'created_at';
        $order = 'desc';

        $show = request('show') ?? '10';

        
    }

    public function create() {
        //ambil gudang utama
        $warehouse = Warehouse::findOrFail(1);
        //ambil supplier
        $suppliers=Provider::all();
        // Ambil semua produk
        $products = Product::all();
        //ambil data produk warehouse
        $productWarehouse = ProductWarehouse::where('warehouse_id', 1)->get();

        // Ambil semua purchase yang berstatus pending, ordered, shipped, atau arrived dan milik warehouse tertentu
        $purchases = Purchase::where('warehouse_id', 1)
                                ->whereIn('statut', ['pending', 'ordered', 'shipped', 'arrived'])
                                ->pluck('id');
        // Ambil detail purchase dari purchase yang sudah diambil sebelumnya
        $purchaseDetails = PurchaseDetail::whereIn('purchase_id', $purchases)->get();


        // Gabungkan data produk dengan 
        $allProduct = $products->map(function($product) use ($purchaseDetails, $productWarehouse) {
            $quantityOnOrder = $purchaseDetails->where('product_id', $product->id)->sum('quantity');
            // Ambil quantity available dari tabel product_warehouse
            $quantityAvailable = $productWarehouse->where('product_id', $product->id)->sum('qty');
            // Ambil nama varian dari tabel product_variants
            $variants = ProductVariant::where('product_id', $product->id)->get()->map(function($variant) use ($purchaseDetails, $productWarehouse) {
                $variantOnOrder = $purchaseDetails->where('product_variant_id', $variant->id)->sum('quantity');
                // Ambil quantity available dari tabel product_warehouse
                $variantAvailable = $productWarehouse->where('product_variant_id', $variant->id)->sum('qty');

                return [
                    'variantData' => $variant,
                    'variantOnOrder' => $variantOnOrder,
                    'variantAvailable' => $variantAvailable,
                ];
            });
            
            return [
                'productData' => $product,
                'quantity_on_order' => $quantityOnOrder,
                'quantity_available' => $quantityAvailable,
                'variant' => $variants
            ];
        });
        // Return view dengan data yang sudah digabungkan
        return view('templates.purchase.create', [
            'products' => $allProduct,
            'suppliers' => $suppliers,
            'warehouse' => $warehouse,
        ]);
    }

    public function store(Request $request) {

        $user = Auth::user();
        request()->validate([
            'date' => 'required|date',
            'supplier' => 'required',
            'location' => 'required',
            'address' => 'required',
            'email' => 'required',
            'statut' => 'required',
        ]);
        
        if ($request->input('products_with_variant') && $request->input('products') == "{}") {
            return back()->with('error', 'Fill the product you want to purchase');
        };
        if ($request->input('products_with_variant') && $request->input('products') == null) {
            return back()->with('error', 'Fill the product you want to purchase');
        };
        if ($request->input('products_with_variant') == "{}" && $request->input('products') == null) {
            return back()->with('error', 'Fill the product you want to purchase');
        };
        if ($request->input('products_with_variant') == null && $request->input('products') == "{}") {
            return back()->with('error', 'Fill the product you want to purchase');
        };

        do {
            $ean13 = generateEAN13();
        } while (Purchase::where('barcode', $ean13)->exists());

        Purchase::create([
            
                'user_id' => $user->id,
                'Ref' => $this->getNumberOrder(),
                'date' => $request->date,
                'provider_id' => $request->supplier,
                'warehouse_id' => $request->location,
                'tax_rate' => $request->tax,
                'TaxNet' => $request->order_tax_input,
                'discount' => $request->discount,
                'GrandTotal' => $request->order_total_input,
                'subtotal' => $request->order_subtotal_input,
                'statut' => $request->statut,
                'payment_statut' => 'unpaid',
                'notes' => $request->notes,
                'payment_method' => $request->payment_method,
                'payment_term' => $request->payment_term,
                'down_payment' => $request->down_payment,
                'req_arrive_date' => $request->req_arrive_date,
                'barcode' => $ean13,
            
        ]);

        if ($request->input('products') != null) {
            // Ambil array ID user dari request
            $productsToInput = json_decode($request->input('products'), true);
    
            foreach ($productsToInput as $product=>$qty) {
                PurchaseDetail::create([
            
                    'user_id' => $user->id,
                    'Ref' => $this->getNumberOrder(),
                    'date' => $request->date,
                    'provider_id' => $request->supplier,
                    'warehouse_id' => $request->location,
                    'tax_rate' => $request->tax,
                    'TaxNet' => $request->order_tax_input,
                    'discount' => $request->discount,
                    'GrandTotal' => $request->order_total_input,
                    'subtotal' => $request->order_subtotal_input,
                    'statut' => $request->statut,
                    'payment_statut' => 'unpaid',
                    'notes' => $request->notes,
                    'payment_method' => $request->payment_method,
                    'payment_term' => $request->payment_term,
                    'down_payment' => $request->down_payment,
                    'req_arrive_date' => $request->req_arrive_date,
                    'barcode' => $ean13,
                
            ]);
            }
        }

        
    }

    public function show(String $id) {

    }

    public function edit(String $id) {

    }

    public function update(Request $request, String $id) {

    }

    public function destroy(Request $request, String $id) {

    }

    public function getFromScanner(String $code) {
        //product
        $product = Product::where('code', $code)->first();
        $product_variant = ProductVariant::where('code', $code)->first();
        if ($product_variant) {
            return response()->json([
                "id" => $product_variant->id,
                "product_id" => $product_variant->product_id
            ]);
        } elseif ($product) {
            return response()->json([
                "id" => $product->id
            ]);
        } else {
            return response()->json([
                "error" => trans("product not found")
            ]);
        }
    }

    public function getSupplier(String $id) {
        //supplier
        $supplier = Provider::findOrFail($id);
        if ($supplier) {
            return response()->json($supplier);
        } else {
            return response()->json([
                "error" => trans("product not found")
            ]);
        }
    }

    function generateEAN13()
    {
        $code = '';
        for ($i = 0; $i < 12; $i++) {
            $code .= rand(0, 9);
        }

        $code .= calculateCheckDigit($code);

        return $code;
    }

    function calculateCheckDigit($code)
    {
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += ($i % 2 === 0) ? $code[$i] * 1 : $code[$i] * 3;
        }

        $remainder = $sum % 10;
        return ($remainder === 0) ? 0 : 10 - $remainder;
    }

    function generateBarcode($code)
    {
        $generator = new BarcodeGeneratorPNG();
        return $generator->getBarcode($code, $generator::TYPE_EAN_13);
    }

    public function getNumberOrder()
    {
        $last = Expense::latest()->first();
        if ($last) {
            $item = $last->Ref;
            $nwMsg = explode('_', $item);
            $inMsg = $nwMsg[1] + 1;

            // Konversi variabel ke string untuk menghitung panjangnya
            $variabelString = (string) $inMsg;
            // Periksa jika panjang string kurang dari 4
            if (strlen($variabelString) < 4) {
                // Tambahkan nol di depan hingga panjangnya menjadi 4
                $variabelDiformat = str_pad($variabelString, 4, '0', STR_PAD_LEFT);
            } else {
                // Jika sudah 4 digit atau lebih, tidak perlu menambahkan nol
                $variabelDiformat = $variabelString;
            }

            $code = $nwMsg[0].'_'.$variabelDiformat;
        } else {
            $code = 'PRC_0001';
        }

        return $code;
    }
}
