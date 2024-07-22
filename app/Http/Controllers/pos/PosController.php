<?php

namespace App\Http\Controllers\pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\PaymentSale;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductWarehouse;
use App\Models\Sale;
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
use Illuminate\Support\Facades\Log;


class PosController extends Controller
{
    public function create(Request $request) {
        //ambil user akun ini 
        $user=Auth::user();
        //ambil warehousenya
        $warehouse = $user->warehouses->first();

        // Tanggal hari ini
        $today = Carbon::today()->toDateString();

        // kode sale
        $Ref = $this->getNumberOrder();

        //untuk data staff yang akan melakukan kasir
        $staff = User::role('staff')
                //harus yang warehousenya sesuai dengan akun tersebut jadi satu akun bisa dipakai banyak kasir yang bekerja
                ->whereHas('warehouses', function ($query) use ($warehouse) {
                    $query->where('id', $warehouse->id);
                })
                //untuk mengurangi biar nggak kelamaan nyarinya soalnya seatiap transaksi dipilh terus, saring ke user sedang masuk
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
        // Ambil semua sale sesuai warehouse yang dilakukan lewat kasir
        $sales = Sale::where('warehouse_id', $warehouse->id)
                        ->where('is_pos', 1)
                        ->get();

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
            'user' => $user,
            'sales' => $sales,
            'ref' => $Ref,
        ]);
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

    public function getNumberOrder()
    {
        $last = Sale::latest()->first();
        if ($last) {
            $item = $last->Ref;
            $nwMsg = explode('_', $item);
            $inMsg = $nwMsg[1];
 
            // Periksa jika panjang string kurang dari 4
            if (strlen($inMsg) < 4) {
                // Tambahkan nol di depan hingga panjangnya menjadi 4
                $variabelDiformat = str_pad($inMsg, 4, '0', STR_PAD_LEFT);
            } else {
                // Jika panjang string lebih dari 4, ambil 4 karakter pertama
                $variabelDiformat = substr($inMsg, 0, 4);
            }

            // Tambahkan 1 setelah pemformatan
            $variabelDiformat = (int)$variabelDiformat + 1;

            // Format kembali menjadi string dengan panjang 4 karakter
            $variabelDiformat = str_pad($variabelDiformat, 4, '0', STR_PAD_LEFT);
 
            $code = $nwMsg[0].'_'.$variabelDiformat;
        } else {
            $code = 'SL_0001';
        }
 
        return $code;
    }
}
