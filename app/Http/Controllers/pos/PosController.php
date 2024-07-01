<?php

namespace App\Http\Controllers\pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
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
use App\Models\User;
use App\Models\UserWarehouse;
use App\Models\Warehouse;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Mail\PurchaseMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Spatie\Permission\Models\Role;

class PosController extends Controller
{
    public function create() {
        
        $user=Auth::user();
        $warehouse = $user->warehouses->first();

        // Tanggal hari ini
        $today = Carbon::today()->toDateString();

        $staff = User::role('staff')
                ->whereHas('warehouses', function ($query) use ($warehouse) {
                    $query->where('id', $warehouse->id);
                })
                ->whereHas('attendances', function ($query) use ($today) {
                    $query->where('date', $today)
                            ->whereNull('clock_out')
                            ->where('status', 'present');
                })
                ->get();

        //ambil supplier
        $clients=Client::all();
        // Ambil semua produk
        $products = Product::all();

        // Gabungkan data produk dengan 
        $allProduct = $products->map(function($product) {
            // Ambil nama varian dari tabel product_variants
            $variants = ProductVariant::where('product_id', $product->id)->get()->map(function($variant) {
                return [
                    'variantData' => $variant,
                ];
            });
            
            return [
                'productData' => $product,
                'variant' => $variants
            ];
        });
        // Return view dengan data yang sudah digabungkan
        return view('templates.cashier.create', [
            'products' => $allProduct,
            'clients' => $clients,
            'warehouse' => $warehouse,
            'staff' => $staff,
        ]);
    }
}
