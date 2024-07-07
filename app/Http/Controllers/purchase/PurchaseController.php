<?php

namespace App\Http\Controllers\purchase;

use App\Http\Controllers\Controller;
use Config;
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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;

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
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'supplier' => 'required',
            'location' => 'required',
            'address' => 'required',
            'email' => 'required|email',
            'statut' => 'required',
            'payment_method' => 'required',
            'payment_term' => 'required',
            'down_payment' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                // Jika validasi gagal, kembalikan pesan error
                return response()->json([
                    'errors' => $validator->errors()->all()
                ]);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        if ($request->input('products_with_variant')== "{}" && $request->input('products') == "{}") {
            if ($request->ajax()) {
                // Jika validasi gagal, kembalikan pesan error
                return response()->json([
                    'errors' => trans('Fill the product you want to purchase')
                ]);
            }
            return back()->with('error', 'Fill the product you want to purchase');
        };
        if ($request->input('products_with_variant') == null && $request->input('products') == null) {
            if ($request->ajax()) {
                // Jika validasi gagal, kembalikan pesan error
                return response()->json([
                    'errors' => trans('Fill the product you want to purchase')
                ]);
            }
            return back()->with('error', 'Fill the product you want to purchase');
        };
        if ($request->input('products_with_variant') == "{}" && $request->input('products') == null) {
            if ($request->ajax()) {
                // Jika validasi gagal, kembalikan pesan error
                return response()->json([
                    'errors' => trans('Fill the product you want to purchase')
                ]);
            }
            return back()->with('error', 'Fill the product you want to purchase');
        };
        if ($request->input('products_with_variant') == null && $request->input('products') == "{}") {
            if ($request->ajax()) {
                // Jika validasi gagal, kembalikan pesan error
                return response()->json([
                    'errors' => trans('Fill the product you want to purchase')
                ]);
            }
            return back()->with('error', 'Fill the product you want to purchase');
        };

        do {
            $ean13 = $this->generateEAN13();
        } while (Purchase::where('barcode', $ean13)->exists());
        
        $purchase_Ref = $this->getNumberOrder();
        $purchase_email_pin = $this->getPin();
        
        if ($request->send == 'send_email') {
            //nama instansi supplier
            $email['supplier_name'] = Provider::where('id', $request->supplier)->first()->name;
            //alamat instansi supplier
            $email['supplier_adresse'] = Provider::where('id', $request->supplier)->first()->adresse;
            //tanggal purchase
                $date=$request->date;
                // Ubah tanggal menjadi instance Carbon
                $carbonDate = Carbon::parse($date);
                // Format tanggal
            $email['date'] = $carbonDate->translatedFormat('d, F Y');
            //ref purchase
            $email['purchase_ref']=$purchase_Ref;

            //cek company setting, karena ini berpengaruh dengan sistem mailing
            $settings = Setting::where('deleted_at', '=', null)->first();
            //jika setting tidak ditemukan
            if (!$settings) {
                return response()->json([
                    "error" => trans("Youe haven't set up your company settings")
                ]);
            }
            //jika email company belum diset
            if (!$settings->email) {
                return response()->json([
                    "error" => trans("Youe haven't set up your company email")
                ]);
            }
            //jika app password dari email company belum di set
            if (!$settings->server_password) {
                return response()->json([
                    "error" => trans("Youe haven't set up your company email app password")
                ]);
            }

            //nama company
            $email['company_name']=$settings->CompanyName;
            if (!$email['company_name']) {
                return response()->json([
                    "error" => trans("Youe haven't set up your company name")
                ]);
            }
            //pin email
            $email['email_pin']=$purchase_email_pin;
            //status purchase
            $email['status']=$request->statut;

            //subject email
            if ($request->statut == 'pending') {
                $email['subject']= $purchase_Ref." New Order waiting to be confirmed ";
            }
            elseif ($request->statut == 'ordered') {
                $email['subject']= $purchase_Ref." Verify delivery when finished processing the goods";
            }
            elseif ($request->statut == 'shipped') {
                $email['subject']= $purchase_Ref." This is email for affirmation about purchase shipping";
            }
            elseif ($request->statut == 'arrived') {
                $email['subject']= $purchase_Ref." Purchase has arrive";
            }
            elseif ($request->statut == 'complete') {
                $email['subject']= $purchase_Ref." Purchase is completed";
            };

            //set mailing
            $this->Set_config_mail();

            //set unik url agar tidak semua tidak bisa mengakses halaman redirect
            $email['url'] = URL::signedRoute('edit.supplier', ['Ref' => $email['purchase_ref']]);
            //kirim email ke email sesuai di purchase
            Mail::to($request->email)->send(new purchaseMail($email));
        }

        Purchase::create([
                'user_id' => $user->id,
                'Ref' => $purchase_Ref,
                'date' => $request->date,
                'email' => $request->email,
                'provider_id' => $request->supplier,
                'warehouse_id' => $request->location,
                'tax_rate' => $request->tax ?? 0,
                'TaxNet' => $request->order_tax_input ?? 0,
                'discount' => $request->discount ?? 0,
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
                'email_pin' => $purchase_email_pin
            
        ]);

        //ambil purchase
        $purchase = Purchase::latest()->first();

        //isi purchase detail dengan produk2 dan stoknya
        if ($request->input('products') != null && $request->input('products') !== "{}") {
            // Ambil array ID user dari request
            $productsToInput = json_decode($request->input('products'), true);
    
            foreach ($productsToInput as $product=>$qty) {

                //temukan purchase unit
                $product_purchase_unit = Product::findOrFail($product)->unit_purchase_id;
                //temukan cost
                $cost = ProductVariant::findOrFail($product)->cost;

                $qty=(float)$qty;

                PurchaseDetail::create([
                    'cost' => $cost,
                    'purchase_unit_id' => $product_purchase_unit,
                    'purchase_id' => $purchase->id,
                    'product_id' => $product,
                    'total' => $cost*$qty,
                    'quantity' => $qty,
                ]);
            }
        }
        //jika produk bervarian isi purchase detail lewat request products_with_variant
        if ($request->input('products_with_variant') != null && $request->input('products_with_variant') !== "{}") {
            // Ambil array ID product dari request
            $productsToInput = json_decode($request->input('products_with_variant'), true);

            foreach ($productsToInput as $product=>$qty) {
                //temukan produk id
                $product_id = ProductVariant::findOrFail($product)->product_id;
                $product_purchase_unit = Product::findOrFail($product_id)->unit_purchase_id;
                //temukan cost
                $cost = ProductVariant::findOrFail($product)->cost;

                $qty=(float)$qty;

                PurchaseDetail::create([
                    'cost' => $cost,
                    'purchase_unit_id' => $product_purchase_unit,
                    'purchase_id' => $purchase->id,
                    'product_id' => $product_id,
                    'product_variant_id' => $product,
                    'total' => $cost*$qty,
                    'quantity' => $qty,
                ]);
            }
        };
        if ($request->ajax()) {
            // Jika validasi berhasil, kembalikan pesan sukses
            return response()->json([
                'success' => trans('Email sended and purchase created')
            ]);
        }
        return redirect()->route('people.users.index')->with('success', 'Purchase berhasil ditambahkan');
    }

    public function show(String $id) {

    }

    public function edit(String $id) {

    }
    public function editSupplier(String $Ref) {
        $purchase = Purchase::where('Ref', $Ref)->first();
        $warehouse = Warehouse::where('id', $purchase->warehouse_id)->first();
        $products = PurchaseDetail::where('purchase_id', $purchase->id)->get();
        $supplier = Provider::where('id', $purchase->provider_id)->first();
        $settings = Setting::where('deleted_at', '=', null)->first();

        return view('templates.purchase.supplier.edit', [
            'products' => $products,
            'purchase' => $purchase,
            'supplier' => $supplier,
            'settings' => $settings,
            'warehouse' => $warehouse,
        ]);
    }

    public function update(Request $request, String $id) {

    }
    
    public function updateSupplier(Request $request, String $id) {
        $purchase = Purchase::findOrFail($id);

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

    public function generateEAN13()
    {
        $code = '';
        for ($i = 0; $i < 12; $i++) {
            $code .= rand(0, 9);
        }

        $code .= $this->calculateCheckDigit($code);

        return $code;
    }

    public function calculateCheckDigit($code)
    {
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += ($i % 2 === 0) ? $code[$i] * 1 : $code[$i] * 3;
        }

        $remainder = $sum % 10;
        return ($remainder === 0) ? 0 : 10 - $remainder;
    }

    public function generateBarcode($code)
    {
        $generator = new BarcodeGeneratorPNG();
        return $generator->getBarcode($code, $generator::TYPE_EAN_13);
    }

    public function getNumberOrder()
    {
        $last = Purchase::latest()->first();
        if ($last) {
            $item = $last->Ref;
            $nwMsg = explode('_', $item);
            $inMsg = $nwMsg[1] + 1;

            // Periksa jika panjang string kurang dari 4
            if (strlen($inMsg) < 4) {
                // Tambahkan nol di depan hingga panjangnya menjadi 4
                $variabelDiformat = str_pad($inMsg, 4, '0', STR_PAD_LEFT);
            } else {
                // Jika sudah 4 digit atau lebih, tidak perlu menambahkan nol
                $variabelDiformat = $inMsg;
            }

            $code = $nwMsg[0].'_'.$variabelDiformat;
        } else {
            $code = 'PRC_0001';
        }

        return $code;
    }

    // Set config mail
    public function Set_config_mail()
    {
        $settings = Setting::where('deleted_at', '=', null)->first();
        $config = array(
            'driver' => 'smtp',
            'host' => 'smtp.gmail.com',
            'port' => '587',
            'from' => array('address' => $settings->email, 'name' => $settings->CompanyName),
            'encryption' => 'tls',
            'username' => $settings->email,
            'password' => $settings->server_password,
            'sendmail' => '/usr/sbin/sendmail -bs',
            'pretend' => false,
            'stream' => [
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ],
        );
        Config::set('mail', $config);
        
    }

    public function getPin()
    {
        $isUnique = false;
        $uniqueCode = '';

        while (! $isUnique) {
            // Generate a random number between 0 and 999999, then pad it with zeros to ensure it is 6 digits
            $randomCode = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // Check if the code is unique (assuming 'pin' is the column where the unique codes are stored)
            $codeExists = Purchase::where('email_pin', $randomCode)->exists();

            if (! $codeExists) {
                $isUnique = true;
                $uniqueCode = $randomCode;
            }
        }
        // Here, you have a unique $uniqueCode which is a 6-digit number, including leading zeros
        return $uniqueCode;
    }
}
