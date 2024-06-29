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
        dd(json_decode($request->input('products'), true));
        request()->validate([
            'date' => 'required|date',
            'supplier' => 'required',
        ]);
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
}
