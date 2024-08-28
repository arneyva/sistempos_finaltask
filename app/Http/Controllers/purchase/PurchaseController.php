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
use Illuminate\Support\Facades\Log;
 
class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:superadmin|inventaris')->except(['editSupplier','updateSupplier','editreturSupplier','updatereturSupplier','Midtrans']);
    }
 
    public function index() {
        return view('templates.purchase.index', [
            'purchases' => Purchase::query()->paginate(10)->withQueryString(),
        ]);
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
            //ambil purchase tiap unit berapa qty
            $operator=$product->unitPurchase->operator_value;
 
            $quantityOnOrder = $purchaseDetails->where('product_id', $product->id)->sum('quantity');
            // Ambil quantity available dari tabel product_warehouse
            $quantityAvailable = $productWarehouse->where('product_id', $product->id)->sum('qty');
            //jadikan qty available sesuai dengan purchase unit
            $quantityAvailableFinal = intdiv($quantityAvailable, $operator);
            //jika ada sisa qty base unit, juga direkam
            $quantityRemainder = $quantityAvailable % $operator;
            // Ambil nama varian dari tabel product_variants
            $variants = ProductVariant::where('product_id', $product->id)->get()->map(function($variant) use ($purchaseDetails, $productWarehouse, $operator) {
                $variantOnOrder = $purchaseDetails->where('product_variant_id', $variant->id)->sum('quantity');
                // Ambil quantity available dari tabel product_warehouse
                $variantAvailable = $productWarehouse->where('product_variant_id', $variant->id)->sum('qty');
                //jadikan qty available sesuai dengan purchase unit
                $variantAvailableFinal = intdiv($variantAvailable, $operator);
                //jika ada sisa qty base unit, juga direkam
                $variantRemainder = $variantAvailable % $operator;
 
                return [
                    'variantData' => $variant,
                    'variantOnOrder' => $variantOnOrder,
                    'variantAvailable' => $variantAvailableFinal,
                    'variantRemainder' => $variantRemainder,
                ];
            });
            
            return [
                'productData' => $product,
                'quantity_on_order' => $quantityOnOrder,
                'quantity_available' => $quantityAvailableFinal,
                'quantityRemainder' => $quantityRemainder,
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
                    'error' => $validator->errors()->all()
                ]);
            }
 
            return redirect()->back()->withErrors($validator)->withInput();
        }
 
        if ($request->input('products_with_variant')== "{}" && $request->input('products') == "{}") {
            if ($request->ajax()) {
                // Jika validasi gagal, kembalikan pesan error
                return response()->json([
                    'error' => trans('Fill the product you want to purchase')
                ]);
            }
            return back()->with('error', 'Fill the product you want to purchase');
        };
        if ($request->input('products_with_variant') == null && $request->input('products') == null) {
            if ($request->ajax()) {
                // Jika validasi gagal, kembalikan pesan error
                return response()->json([
                    'error' => trans('Fill the product you want to purchase')
                ]);
            }
            return back()->with('error', 'Fill the product you want to purchase');
        };
        if ($request->input('products_with_variant') == "{}" && $request->input('products') == null) {
            if ($request->ajax()) {
                // Jika validasi gagal, kembalikan pesan error
                return response()->json([
                    'error' => trans('Fill the product you want to purchase')
                ]);
            }
            return back()->with('error', 'Fill the product you want to purchase');
        };
        if ($request->input('products_with_variant') == null && $request->input('products') == "{}") {
            if ($request->ajax()) {
                // Jika validasi gagal, kembalikan pesan error
                return response()->json([
                    'error' => trans('Fill the product you want to purchase')
                ]);
            }
            return back()->with('error', 'Fill the product you want to purchase');
        };
 
        do {
            $ean13 = $this->generateEAN13();
        } while (Purchase::where('barcode', $ean13)->exists());
        
        $purchase_Ref = $this->getNumberOrder();
        
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
                    'error' => trans("Youe haven't set up your company settings")
                ]);
            }
            //jika email company belum diset
            if (!$settings->email) {
                return response()->json([
                    'error' => trans("Youe haven't set up your company email")
                ]);
            }
            //jika app password dari email company belum di set
            if (!$settings->server_password) {
                return response()->json([
                    'error' => trans("Youe haven't set up your company email app password")
                ]);
            }
 
            //nama company
            $email['company_name']=$settings->CompanyName;
            if (!$email['company_name']) {
                return response()->json([
                    'error' => trans("Youe haven't set up your company name")
                ]);
            }
 
            //status purchase
            $email['status']=$request->statut;
 
            //subject email
            if ($request->statut == 'pending') {
                $email['subject']= $purchase_Ref." New Order waiting to be confirmed ";
            }
            elseif ($request->statut == 'canceled') {
                $email['subject']= $purchase_Ref." The Purchase Order has been canceled ";
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
            elseif ($request->statut == 'completed') {
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
                'address' => $request->address,
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
                'down_payment_rate' => $request->down_payment,
                'down_payment_net' => $request->order_down_payment_input,
                'req_arrive_date' => $request->req_arrive_date,
                'barcode' => $ean13,            
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
                $cost = Product::findOrFail($product)->cost;
 
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
        return redirect()->route('purchases.index')->with('success', 'Purchase berhasil ditambahkan');
    }
 
    public function show(String $id) {
 
    }
 
    public function edit(String $id) {
        $purchase = Purchase::where('id', $id)->first();
        $returpurchase = PurchaseReturn::where('purchase_id', $id)->first();
        $products_selected = PurchaseDetail::where('purchase_id', $purchase->id)->get();
        $payments = PaymentPurchase::where('purchase_id', $purchase->id)->get();
 
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
            //ambil purchase tiap unit berapa qty
            $operator=$product->unitPurchase->operator_value;
 
            $quantityOnOrder = $purchaseDetails->where('product_id', $product->id)->sum('quantity');
            // Ambil quantity available dari tabel product_warehouse
            $quantityAvailable = $productWarehouse->where('product_id', $product->id)->sum('qty');
            //jadikan qty available sesuai dengan purchase unit
            $quantityAvailableFinal = intdiv($quantityAvailable, $operator);
            //jika ada sisa qty base unit, juga direkam
            $quantityRemainder = $quantityAvailable % $operator;
            // Ambil nama varian dari tabel product_variants
            $variants = ProductVariant::where('product_id', $product->id)->get()->map(function($variant) use ($purchaseDetails, $productWarehouse, $operator) {
                $variantOnOrder = $purchaseDetails->where('product_variant_id', $variant->id)->sum('quantity');
                // Ambil quantity available dari tabel product_warehouse
                $variantAvailable = $productWarehouse->where('product_variant_id', $variant->id)->sum('qty');
                //jadikan qty available sesuai dengan purchase unit
                $variantAvailableFinal = intdiv($variantAvailable, $operator);
                //jika ada sisa qty base unit, juga direkam
                $variantRemainder = $variantAvailable % $operator;
 
                return [
                    'variantData' => $variant,
                    'variantOnOrder' => $variantOnOrder,
                    'variantAvailable' => $variantAvailableFinal,
                    'variantRemainder' => $variantRemainder,
                ];
            });
            
            return [
                'productData' => $product,
                'quantity_on_order' => $quantityOnOrder,
                'quantity_available' => $quantityAvailableFinal,
                'quantityRemainder' => $quantityRemainder,
                'variant' => $variants
            ];
        });
 
        $supplier = Provider::where('id', $purchase->provider_id)->first();
 
        $print_url= URL::signedRoute('edit.supplier', ['Ref' => $purchase->Ref]);

        //ambil
        $payment_not_return = PaymentPurchase::whereNull('purchase_return_id')
                                                ->where('purchase_id', $purchase->id)
                                                ->get();
        $payment_return = PaymentPurchase::whereNotNull('purchase_return_id')
                                                ->where('purchase_id', $purchase->id)
                                                ->get();

        $total_paid = 0;
        if ($payment_not_return->isNotEmpty()) { // Mengecek apakah ada data di collection
            foreach ($payment_not_return as $payment) {
                $total_paid += $payment->montant; // Akumulasi total pembayaran
            }
        }
        $total_return_paid = 0;
        if ($payment_return->isNotEmpty()) { // Mengecek apakah ada data di collection
            foreach ($payment_return as $payment) {
                $total_return_paid += $payment->montant; // Akumulasi total pembayaran
            }
        }
 
        $total_balance = $purchase->GrandTotal - $total_paid - ($returpurchase->GrandTotal ?? 0) + $total_return_paid;
        Log::info('Request data:', [$purchase->GrandTotal]);

        if ($returpurchase) {
            $products_returned = PurchaseReturnDetails::where('purchase_return_id', $returpurchase->id)->get();
        } else {
            $products_returned = $products_selected->where('status', '');
        };

        if (!$returpurchase) {
            return view('templates.purchase.edit', [
                'products_selected' => $products_selected,
                'products_returned' => $products_returned,
                'products' => $allProduct,
                'purchase' => $purchase,
                'supplier' => $supplier,
                'total_balance' => $total_balance,
                'total_paid' => $total_paid,
                'total_return_paid' => $total_return_paid,
                'print_url' => $print_url,
                'payments' => $payments,
            ]);
        } else {
            return view('templates.purchase.edit', [
                'products_selected' => $products_selected,
                'products_returned' => $products_returned,
                'returpurchase' => $returpurchase,
                'products' => $allProduct,
                'purchase' => $purchase,
                'supplier' => $supplier,
                'total_balance' => $total_balance,
                'total_return_paid' => $total_return_paid,
                'total_paid' => $total_paid,
                'print_url' => $print_url,
                'payments' => $payments,
            ]);
        };
    }
 
    public function update(Request $request, String $id) {
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

        Log::info('Request data:', [$request->all()]);
 
        if ($validator->fails()) {
            if ($request->ajax()) {
                // Jika validasi gagal, kembalikan pesan error
                return response()->json([
                    'error' => $validator->errors()->all()
                ]);
            }
 
            return redirect()->back()->withErrors($validator)->withInput();
        }
 
        $purchase = Purchase::findOrFail($id);
        if (!$purchase) {
            if ($request->ajax()) {
                // Jika validasi berhasil, kembalikan pesan sukses
                return response()->json([
                    'error' => trans('The purchase was deleted')
                ]);
            }
        }
        
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
            $email['purchase_ref']=$purchase->Ref;
 
            //cek company setting, karena ini berpengaruh dengan sistem mailing
            $settings = Setting::where('deleted_at', '=', null)->first();
            //jika setting tidak ditemukan
            if (!$settings) {
                return response()->json([
                    'error' => trans("Youe haven't set up your company settings")
                ]);
            }
            //jika email company belum diset
            if (!$settings->email) {
                return response()->json([
                    'error' => trans("Youe haven't set up your company email")
                ]);
            }
            //jika app password dari email company belum di set
            if (!$settings->server_password) {
                return response()->json([
                    'error' => trans("Youe haven't set up your company email app password")
                ]);
            }
 
            //nama company
            $email['company_name']=$settings->CompanyName;
            if (!$email['company_name']) {
                return response()->json([
                    'error' => trans("Youe haven't set up your company name")
                ]);
            }
 
            //status purchase
            $email['status']=$request->statut;
 
            //subject email
            if ($request->statut == 'pending') {
                $email['subject']= $purchase->Ref." New Order waiting to be confirmed ";
            }
            elseif ($request->statut == 'canceled') {
                $email['subject']= $purchase->Ref." The Purchase Order has been canceled ";
            }
            elseif ($request->statut == 'ordered') {
                $email['subject']= $purchase->Ref." Verify delivery when finished processing the goods";
            }
            elseif ($request->statut == 'shipped') {
                $email['subject']= $purchase->Ref." This is email for affirmation about purchase shipping";
            }
            elseif ($request->statut == 'arrived') {
                $email['subject']= $purchase->Ref." Purchase has arrive";
            }
            elseif ($request->statut == 'completed') {
                $email['subject']= $purchase->Ref." Purchase is completed";
            };
 
            //set mailing
            $this->Set_config_mail();
 
            //set unik url agar tidak semua tidak bisa mengakses halaman redirect
            $email['url'] = URL::signedRoute('edit.supplier', ['Ref' => $email['purchase_ref']]);
            //kirim email ke email sesuai di purchase
            Mail::to($request->email)->send(new purchaseMail($email));
        }

        $current = $purchase->delivery_file;
        if ($request->delivery_file != null) {
 
            $file = $request->file('delivery_file');
            
            $path = public_path().'/hopeui/html/assets/files/purchases/deliveries/';
            $filename = rand(11111111, 99999999).$file->getClientOriginalName();
 
            $file->move(public_path('/hopeui/html/assets/files/purchases/deliveries/'), $filename);
 
            $currentFile = $path.$current;
            if (file_exists($currentFile)) {
                @unlink($currentFile);
            }
 
        } else {
            $filename = $current;
        }
 
        if($purchase->statut == 'pending'|| $purchase->statut == 'ordered'|| $purchase->statut == 'canceled'|| $purchase->statut == 'refused') {
            //delete product yang sudah ada
            $products = PurchaseDetail::where('purchase_id', $purchase->id)->delete();
 
            //isi purchase detail dengan produk2 dan stoknya
            if ($request->input('products') != null && $request->input('products') !== "{}") {
                // Ambil array ID user dari request
                $productsToInput = json_decode($request->input('products'), true);
        
                foreach ($productsToInput as $product=>$qty) {
 
                    //temukan purchase unit
                    $product_purchase_unit = Product::findOrFail($product)->unit_purchase_id;
                    //temukan cost
                    $cost = Product::findOrFail($product)->cost;
 
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
        };
        
        $change_statuses = PurchaseDetail::where('purchase_id', $purchase->id)->get();
        foreach ($change_statuses as $change_status) {
            $change_status->update(array(
                'status' => '',
            ));
        };

        //isi purchase detail dengan produk2 dan stoknya
        if ($request->input('products_checked') != null && $request->input('products_checked') !== "{}") {
            // Ambil array ID user dari request
            $productsToInput = json_decode($request->input('products_checked'), true);
    
            foreach ($productsToInput as $product=>$qty) {

                $purchase_details = PurchaseDetail::where('purchase_id', $purchase->id)
                                                        ->whereNull('product_variant_id')
                                                        ->where('product_id', $product)
                                                        ->get();

                foreach ($purchase_details as $purchase_detail) {
                    $purchase_detail->update(array(
                        'status' => "passed",
                    ));
                };
            };
        }
        //jika produk bervarian isi purchase detail lewat request products_with_variant
        if ($request->input('products_with_variant_checked') != null && $request->input('products_with_variant_checked') !== "{}") {
            // Ambil array ID product dari request
            $productsToInput = json_decode($request->input('products_with_variant_checked'), true);

            foreach ($productsToInput as $product_variant_id=>$qty) {

                $purchase_details = PurchaseDetail::where('purchase_id', $purchase->id)->where('product_variant_id', $product_variant_id)->get();

                foreach ($purchase_details as $purchase_detail) {
                    $purchase_detail->update(array(
                        'status' => "passed",
                    ));
                };
            };
        };
 
        $purchase->update(array(
            'user_id' => $user->id,
            'date' => $request->date,
            'email' => $request->email,
            'address' => $request->address,
            'provider_id' => $request->supplier,
            'warehouse_id' => $request->location,
            'tax_rate' => $request->tax ?? 0,
            'TaxNet' => $request->order_tax_input ?? 0,
            'discount' => $request->discount ?? 0,
            'GrandTotal' => $request->order_total_input,
            'subtotal' => $request->order_subtotal_input,
            'statut' => $request->statut,
            'notes' => $request->notes,
            'payment_method' => $request->payment_method,
            'payment_term' => $request->payment_term,
            'down_payment_rate' => $request->down_payment,
            'down_payment_net' => $request->order_down_payment_input,
            'req_arrive_date' => $request->req_arrive_date,
            'supplier_bank_account' => $request->supplier_bank_account ?? null,
            'supplier_ewalet' => $request->supplier_ewalet ?? null,
            'supplier_notes' => $request->supplier_notes,
            'courier' => $request->courier,
            'shipment_number' => $request->shipment_number ?? null,
            'shipment_cost' => $request->shipment_cost,
            'delivery_file' => $filename ?? null,
            'estimate_arrive_date' => $request->est_arrive_date,
            'driver_contact' => $request->driver_phone ?? null,
        ));
 
        if ($request->ajax()) {
            // Jika validasi berhasil, kembalikan pesan sukses
            return response()->json([
                'success' => trans('Email sended and purchase edited')
            ]);
        }
        return back()->with('success', 'Purchase berhasil diedit');
    }
 
    public function editSupplier(String $Ref) {
        $purchase = Purchase::where('Ref', $Ref)->first();
        $products = PurchaseDetail::where('purchase_id', $purchase->id)->get();
        $supplier = Provider::where('id', $purchase->provider_id)->first();
        $settings = Setting::where('deleted_at', '=', null)->first();
 
        $barcode_img= 'data:image/png;base64,' . base64_encode($this->generateBarcode($purchase->barcode));
 
        //set unik url agar tidak semua tidak bisa mengupdate
        $update_url = URL::signedRoute('update.supplier', ['id' => $purchase->id]);
 
        return view('templates.purchase.supplier.edit', [
            'products' => $products,
            'purchase' => $purchase,
            'supplier' => $supplier,
            'settings' => $settings,
            'update_url' => $update_url,
            'barcode_img' => $barcode_img,
        ]);
    }

    public function makeReturn(Request $request, String $id) {
        $user = Auth::user();
        $purchase = Purchase::find($id);

        $allqty = json_decode($request->input('allQty'), true);
        

 
        do {
            $ean13 = $this->generateEAN13();
        } while (PurchaseReturn::where('barcode', $ean13)->exists() || 
        Purchase::where('barcode', $ean13)->exists());
        
        $returpurchase_Ref = $this->returNumberOrder();
        $purchase_Ref = $purchase->Ref;
        
        if ($request->send == 'send_email') {
            //nama instansi supplier
            $email['supplier_name'] = Provider::where('id', $purchase->provider_id)->first()->name;
            //alamat instansi supplier
            $email['supplier_adresse'] = Provider::where('id', $purchase->provider_id)->first()->adresse;
            //tanggal purchase
                $date=$request->returdate;
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
                    'error' => trans("Youe haven't set up your company settings")
                ]);
            }
            //jika email company belum diset
            if (!$settings->email) {
                return response()->json([
                    'error' => trans("Youe haven't set up your company email")
                ]);
            }
            //jika app password dari email company belum di set
            if (!$settings->server_password) {
                return response()->json([
                    'error' => trans("Youe haven't set up your company email app password")
                ]);
            }
 
            //nama company
            $email['company_name']=$settings->CompanyName;
            if (!$email['company_name']) {
                return response()->json([
                    'error' => trans("Youe haven't set up your company name")
                ]);
            }
 
            //status purchase
            $email['status']='pending';

            $email['subject']= $purchase_Ref." Products Return Requested ";
 
            //set mailing
            $this->Set_config_mail();
 
            //set unik url agar tidak semua tidak bisa mengakses halaman redirect
            $email['url'] = URL::signedRoute('edit.retur.supplier', ['Ref' => $email['purchase_ref']]);
            //kirim email ke email sesuai di purchase
            Mail::to($purchase->email)->send(new purchaseMail($email));
        }

        if ($request->retur_proof != null) {
 
            $file = $request->file('retur_proof');
            
            $filename = rand(11111111, 99999999).$file->getClientOriginalName();
 
            $file->move(public_path('/hopeui/html/assets/files/purchases/return/'), $filename);
 
        };
 
        PurchaseReturn::create([
                'user_id' => $user->id,
                'Ref' => $returpurchase_Ref,
                'date' => $request->returdate,
                'address' => $request->returaddress,
                'provider_id' => $purchase->provider_id,
                'purchase_id' => $id,
                'warehouse_id' => $purchase->warehouse_id,
                'tax_rate' => $request->tax ?? 0,
                'TaxNet' => $request->order_tax_input ?? 0,
                'discount' => $request->discount ?? 0,
                'GrandTotal' => abs($request->returorder_total_input),
                'subtotal' => abs($request->returorder_subtotal_input),
                'statut' => 'pending',
                'payment_statut' => 'unpaid',
                'retur_proof' => $filename ?? null,
                'notes' => $request->returnotes,
                'payment_method' => $request->returpayment_method,
                'request_req_arrive_date' => $request->retur_requestreq_arrive_date,
                'request_address' => $request->retur_requestaddress,
                'barcode' => $ean13,            
                'retur_proof' => $request->retur_proof,            
                'shipment_number' => $request->returshipment_number,            
                'shipment_cost' => $request->returshipment_cost,            
                'driver_contact' => $request->returdriver_phone,            
                'courier' => $request->returcourier,            
        ]);

 
        //ambil purchase
        $returpurchase = PurchaseReturn::latest()->first();

        $qty_return_total = 0;
        $qty_unpassed_total = 0;
        $qty_request_total = 0;
        
        foreach ($allqty as $product) {
            
            if ($product['isVariant'] == "true") {
                //berartti produknya varian
                $cost = ProductVariant::findOrFail($product['id'])->cost;
                //temukan produk id
                $product_id = ProductVariant::findOrFail($product['id'])->product_id;
                $product_purchase_unit = Product::findOrFail($product_id)->unit_purchase_id;
                PurchaseReturnDetails::create([
                    'cost' => $cost,
                    'purchase_unit_id' => $product_purchase_unit,
                    'purchase_return_id' => $returpurchase->id,
                    'product_id' => $product_id,
                    'product_variant_id' => $product['id'],
                    'total' => abs($product['retursubtotal']),
                    'qty_return' => $product['returnQty'],
                    'qty_unpassed' => $product['unpassedQty'],
                    'qty_request' => $product['requestQty'],
                ]);
            } else {
                
                //berarti produknya ndak varian
                //temukan purchase unit
                $product_purchase_unit = Product::findOrFail($product['id'])->unit_purchase_id;
                //temukan cost
                $cost = Product::findOrFail($product['id'])->cost;
 
                PurchaseReturnDetails::create([
                    'cost' => $cost,
                    'purchase_unit_id' => $product_purchase_unit,
                    'purchase_return_id' => $returpurchase->id,
                    'product_id' => $product['id'],
                    'total' => abs($product['retursubtotal']),
                    'qty_return' => $product['returnQty'],
                    'qty_unpassed' => $product['unpassedQty'],
                    'qty_request' => $product['requestQty'],
                ]);
            };
            $qty_return_total += $product['returnQty'];
            $qty_unpassed_total += $product['unpassedQty'];
            $qty_request_total += $product['requestQty'];
        };
        $returpurchase->update(array(
            'qty_unpassed_total' => $qty_unpassed_total,
            'qty_return_total' => $qty_return_total,
            'qty_request_total' => $qty_request_total,
        ));
 
        
        if ($request->ajax()) {
            // Jika validasi berhasil, kembalikan pesan sukses
            if ($request->send == 'send_email') {
                return response()->json([
                    'success' => trans('Email Sended and Return Created')
                ]);
            };
            return response()->json([
                'success' => trans('Return Created')
            ]);
        }
    }

    public function updateReturn(Request $request, String $id) {
        $user = Auth::user();
        $allqty = json_decode($request->input('allQty'), true);
 
        $returpurchase = PurchaseReturn::findOrFail($id);
        if (!$returpurchase) {
            if ($request->ajax()) {
                // Jika validasi berhasil, kembalikan pesan sukses
                return response()->json([
                    'error' => trans('The purchase was deleted')
                ]);
            }
        }
        $purchase = Purchase::findOrFail($returpurchase->purchase_id);
        
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
            $email['purchase_ref']=$purchase->Ref;
 
            //cek company setting, karena ini berpengaruh dengan sistem mailing
            $settings = Setting::where('deleted_at', '=', null)->first();
            //jika setting tidak ditemukan
            if (!$settings) {
                return response()->json([
                    'error' => trans("Youe haven't set up your company settings")
                ]);
            }
            //jika email company belum diset
            if (!$settings->email) {
                return response()->json([
                    'error' => trans("Youe haven't set up your company email")
                ]);
            }
            //jika app password dari email company belum di set
            if (!$settings->server_password) {
                return response()->json([
                    'error' => trans("Youe haven't set up your company email app password")
                ]);
            }
 
            //nama company
            $email['company_name']=$settings->CompanyName;
            if (!$email['company_name']) {
                return response()->json([
                    'error' => trans("Youe haven't set up your company name")
                ]);
            }
 
            //status purchase
            $email['status']=$request->returstatut;
 
            //subject email
            if ($request->returstatut == 'pending') {
                $email['subject']= $purchase->Ref." New Order waiting to be confirmed ";
            }
            elseif ($request->returstatut == 'canceled') {
                $email['subject']= $purchase->Ref." The Purchase Order has been canceled ";
            }
            elseif ($request->returstatut == 'ordered') {
                $email['subject']= $purchase->Ref." Verify delivery when finished processing the goods";
            }
            elseif ($request->returstatut == 'shipped') {
                $email['subject']= $purchase->Ref." This is email for affirmation about purchase shipping";
            }
            elseif ($request->returstatut == 'arrived') {
                $email['subject']= $purchase->Ref." Purchase has arrive";
            }
            elseif ($request->returstatut == 'completed') {
                $email['subject']= $purchase->Ref." Purchase is completed";
            };
 
            //set mailing
            $this->Set_config_mail();
 
            //set unik url agar tidak semua tidak bisa mengakses halaman redirect
            $email['url'] = URL::signedRoute('edit.retur.supplier', ['Ref' => $email['purchase_ref']]);
            //kirim email ke email sesuai di purchase
            Mail::to($purchase->email)->send(new returpurchaseMail($email));
        }

        $current = $returpurchase->retur_proof;
        if ($request->retur_proof != null) {
 
            $file = $request->file('retur_proof');
            
            $path = public_path().'/hopeui/html/assets/files/purchases/return/';
            $filename = rand(11111111, 99999999).$file->getClientOriginalName();
 
            $file->move(public_path('/hopeui/html/assets/files/purchases/return/'), $filename);
 
            $currentFile = $path.$current;
            if (file_exists($currentFile)) {
                @unlink($currentFile);
            }
 
        } else {
            $filename = $current;
        };

        $current_file = $returpurchase->request_delivery_file;
        if ($request->retur_requestdelivery_file != null) {
 
            $file = $request->file('retur_requestdelivery_file');
            
            $path = public_path().'/hopeui/html/assets/files/purchases/deliveries/';
            $delivery_file = rand(11111111, 99999999).$file->getClientOriginalName();
 
            $file->move(public_path('/hopeui/html/assets/files/purchases/deliveries/'), $delivery_file);
 
            $currentFile = $path.$current_file;
            if (file_exists($currentFile)) {
                @unlink($currentFile);
            }
 
        } else {
            $delivery_file = $current_file;
        };

        PurchaseReturnDetails::where('purchase_return_id', $returpurchase->id)->delete();

        $qty_return_total = 0;
        $qty_unpassed_total = 0;
        $qty_request_total = 0;

        foreach ($allqty as $product) {
            if ($product['isVariant'] == 'true') {
                //berartti produknya varian
                $cost = ProductVariant::findOrFail($product['id'])->cost;
                $product_purchase_unit = Product::findOrFail($product['id'])->unit_purchase_id;
                $product_id = ProductVariant::findOrFail($product['id'])->product_id;
                PurchaseReturnDetails::create([
                    'cost' => $cost,
                    'purchase_unit_id' => $product_purchase_unit,
                    'purchase_return_id' => $returpurchase->id,
                    'product_id' => $product,
                    'product_variant_id' => $product['id'],
                    'total' => abs($product['retursubtotal']),
                    'qty_return' => $product['returnQty'],
                    'qty_unpassed' => $product['unpassedQty'],
                    'qty_request' => $product['requestQty'],
                ]);
            }else{
                //berarti produknya ndak varian
                //temukan purchase unit
                $product_purchase_unit = Product::findOrFail($product['id'])->unit_purchase_id;
                //temukan cost
                $cost = Product::findOrFail($product['id'])->cost;
 
                PurchaseReturnDetails::create([
                    'cost' => $cost,
                    'purchase_unit_id' => $product_purchase_unit,
                    'purchase_return_id' => $returpurchase->id,
                    'product_id' => $product['id'],
                    'total' => abs($product['retursubtotal']),
                    'qty_return' => $product['returnQty'],
                    'qty_unpassed' => $product['unpassedQty'],
                    'qty_request' => $product['requestQty'],
                ]);
            };
            $qty_return_total += $product['returnQty'];
            $qty_unpassed_total += $product['unpassedQty'];
            $qty_request_total += $product['requestQty'];
        };

        $returpurchase->update(array(
            'user_id' => $user->id,
            'date' => $request->returdate,
            'address' => $request->returaddress,
            'tax_rate' => $request->tax ?? 0,
            'TaxNet' => $request->order_tax_input ?? 0,
            'discount' => $request->discount ?? 0,
            'GrandTotal' => abs($request->returorder_total_input),
            'subtotal' => abs($request->returorder_subtotal_input),
            'statut' => $request->returstatut,
            'notes' => $request->returnotes,
            'payment_method' => $request->returpayment_method,
            'request_req_arrive_date' => $request->retur_requestreq_arrive_date,
            'supplier_bank_account' => $request->retursupplier_bank_account ?? null,
            'supplier_ewalet' => $request->retursupplier_ewalet ?? null,
            'supplier_notes' => $request->retursupplier_notes,
            'courier' => $request->returcourier,
            'shipment_number' => $request->returshipment_number ?? null,
            'shipment_cost' => $request->returshipment_cost,
            'retur_proof' => $filename ?? null,
            'estimate_arrive_date' => $request->est_arrive_date,
            'driver_contact' => $request->returdriver_phone ?? null,
            'request_address' => $request->retur_requestaddress,
            'request_req_arrive_date' => $request->retur_requestreq_arrive_date,
            'request_courier' => $request->retur_requestcourier,
            'request_shipment_number' => $request->retur_requestshipment_number,
            'request_driver_contact' => $request->retur_requestdriver_phone,
            'request_shipment_cost' => $request->retur_requestshipment_cost,
            'request_estimate_arrive_date' => $request->retur_requestest_arrive_date,
            'request_delivery_file' => $delivery_file ?? null,
            'qty_unpassed_total' => $qty_unpassed_total,
            'qty_return_total' => $qty_return_total,
            'qty_request_total' => $qty_request_total,
        ));
 
        if ($request->ajax()) {
            // Jika validasi berhasil, kembalikan pesan sukses
            if ($request->send == 'send_email') {
                return response()->json([
                    'success' => trans('Email sended and purchase edited')
                ]);
            };
            return response()->json([
                'success' => trans('Purchase edited')
            ]);
        }
    }

    public function editreturSupplier(String $Ref) {
        $purchase_instance = Purchase::where('Ref', $Ref)->first();
        $purchase = PurchaseReturn::where('purchase_id', $purchase_instance->id)->first();
        $products = PurchaseReturnDetails::where('purchase_return_id', $purchase->id)->get();
        $supplier = Provider::where('id', $purchase->provider_id)->first();
        $settings = Setting::where('deleted_at', '=', null)->first();

        $payment_not_return = PaymentPurchase::whereNull('purchase_return_id')
                                                ->where('purchase_id', $purchase_instance->id)
                                                ->get();
        $payment_return = PaymentPurchase::whereNotNull('purchase_return_id')
                                                ->where('purchase_id', $purchase_instance->id)
                                                ->get();

        $total_paid = 0;
        if ($payment_not_return->isNotEmpty()) { // Mengecek apakah ada data di collection
            foreach ($payment_not_return as $payment) {
                $total_paid += $payment->montant; // Akumulasi total pembayaran
            }
        }
        $total_return_paid = 0;
        if ($payment_return->isNotEmpty()) { // Mengecek apakah ada data di collection
            foreach ($payment_return as $payment) {
                $total_return_paid += $payment->montant; // Akumulasi total pembayaran
            }
        }
 
        $total_balance = $purchase_instance->GrandTotal - $total_paid - $purchase->GrandTotal + $total_return_paid;

        if ($total_balance >= 0) {
            $supplier_must_pay = 0;
        } else {
            $supplier_must_pay = abs($total_balance);
        };
 
        $barcode_img= 'data:image/png;base64,' . base64_encode($this->generateBarcode($purchase->barcode));
 
        //set unik url agar tidak semua tidak bisa mengupdate
        $update_url = URL::signedRoute('update.retur.supplier', ['id' => $purchase->id]);
 
        return view('templates.purchase.supplier.returedit', [
            'products' => $products,
            'purchase' => $purchase,
            'purchase_instance' => $purchase_instance,
            'total_paid' => $total_paid,
            'total_return_paid' => $total_return_paid,
            'supplier_must_pay' => $supplier_must_pay,
            'supplier' => $supplier,
            'settings' => $settings,
            'update_url' => $update_url,
            'barcode_img' => $barcode_img,
        ]);
    }

    public function updatereturSupplier(Request $request, String $id) 
    {
        $purchase = PurchaseReturn::findOrFail($id);
        $purchase_instance = Purchase::where('id', $purchase->purchase_id)->first();
        if (!$purchase) {
            return response()->json([
                'error' => trans('The purchase was deleted')
            ]);
        }

        if ($request->ispay == 'pay') {
            // berarti pakai midtrans
            $order_detail = $this->getTransactionDetail($request->payment_id);
                
            if ($order_detail) {
                PaymentPurchase::create([
                    'purchase_id' => $purchase_instance->id,
                    'purchase_return_id' => $purchase->id,
                    'date' => Carbon::today('WIB'),
                    'montant' => $order_detail['gross_amount'], // ambil gross_amount dari detail
                    'Ref' => $request->payment_id,
                    'Reglement' => $order_detail['payment_type'], // ambil payment_type dari detail
                ]);
                $purchase->update(array(
                    'paid_amount' => $order_detail['gross_amount'],
                    'payment_method' => $order_detail['payment_type'],
                ));
                $purchase_instance->update(array(
                    'payment_statut' => 'return paid',
                ));

                return response()->json(['success' => 'Transaction success']);
            } else {
                // Tangani kasus di mana detail transaksi tidak ditemukan
                return response()->json(['error' => 'Transaction not found'], 404);
            };
        }
 
        $current = $purchase->request_delivery_file;
        if ($request->delivery_file != null) {
 
            $file = $request->file('delivery_file');
            
            $path = public_path().'/hopeui/html/assets/files/purchases/deliveries/';
            $filename = rand(11111111, 99999999).$file->getClientOriginalName();
 
            $file->move(public_path('/hopeui/html/assets/files/purchases/deliveries/'), $filename);
 
            $currentFile = $path.$current;
            if (file_exists($currentFile)) {
                @unlink($currentFile);
            }
 
        } else {
            $filename = $current;
        }

        $purchase->update(array(
            'request_req_arrive_date' => $request->req_arrive_date,
            'request_driver_contact' => $request->driver_phone,
            'request_shipment_number' => $request->shipment_number,
            'request_shipment_cost' => $request->shipment_cost,
            'request_estimate_arrive_date' => $request->est_arrive_date,
            'shipment_cost' => $request->returshipment_cost,
            'driver_contact' => $request->returdriver_phone,
            'shipment_number' => $request->returshipment_number,
            'request_courier' => $request->courier,
            'payment_method' => $request->payment_method,
            'courier' => $request->returcourier,
            'request_delivery_file' => $filename ?? null,
        ));
 
 
        if ( $purchase->statut == "pending") {
 
            if ($request->response == "accept" ) {
                if ($purchase->qty_request_total > 0) {
                    $purchase->update(array(
                        'statut' => 'ordered'
                    ));
                } else {
                    $purchase->update(array(
                        'statut' => 'completed'
                    ));
                };
                
                return response()->json([
                    'success' => trans('Return Confirmed')
                ]);
            } else {
                $purchase->update(array(
                    'statut' => 'refused'
                ));
                
                return response()->json([
                'success' => trans('Order Refused')
            ]);
            }
        }
 
        elseif ( $purchase->statut == "ordered") {
 
            if ($request->response == "accept" ) {
                $purchase->update(array(
                    'statut' => 'shipped'
                ));
                
                return response()->json([
                    'success' => trans('Return shipping Confirmed')
                ]);
            } else {
                $purchase->update(array(
                    'statut' => 'refused'
                ));
                
                return response()->json([
                    'success' => trans('Order Canceled')
                ]);
            }
        }
 
        else {
            return response()->json([
                'error' => trans('Purchase not available to edit')
            ]);
        };
    }
 
    public function getTransactionDetail($order_id) {
        $curl = curl_init();
    
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.sandbox.midtrans.com/v2/" . $order_id . "/status",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authorization: Basic U0ItTWlkLXNlcnZlci1KYnJKUjlVZzBDXzlicnJwM2xzNzNKcHY6"
            ],
        ]);
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
    
        curl_close($curl);
    
        if ($err) {
            return null;
        } else {
            return json_decode($response, true); // Kembalikan response sebagai array
        }
    }


    public function Midtrans(Request $request) {
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;
        $params = [
            'transaction_details' => [
                'order_id' => 'INV-' . uniqid(),
                'gross_amount' => $request->GrandTotal,
            ],
        ];
        $snapToken = \Midtrans\Snap::getSnapToken($params);
        return response()->json([
            "token" => $snapToken,
        ]);
    }

    public function updateSupplier(Request $request, String $id) {
 
        $purchase = Purchase::findOrFail($id);
        if (!$purchase) {
            return response()->json([
                'error' => trans('The purchase was deleted')
            ]);
        }
 
        $current = $purchase->delivery_file;
        if ($request->delivery_file != null) {
 
            $file = $request->file('delivery_file');
            
            $path = public_path().'/hopeui/html/assets/files/purchases/deliveries/';
            $filename = rand(11111111, 99999999).$file->getClientOriginalName();
 
            $file->move(public_path('/hopeui/html/assets/files/purchases/deliveries/'), $filename);
 
            $currentFile = $path.$current;
            if (file_exists($currentFile)) {
                @unlink($currentFile);
            }
 
        } else {
            $filename = $current;
        }
 
        $purchase->update(array(
            'tax_rate' => $request->tax ?? 0,
            'TaxNet' => $request->order_tax_input ?? 0,
            'discount' => $request->discount ?? 0,
            'GrandTotal' => $request->order_total_input,
            'payment_method' => $request->payment_method,
            'payment_term' => $request->payment_term,
            'down_payment_rate' => $request->down_payment,
            'down_payment_net' => $request->order_down_payment_input,
            'supplier_bank_account' => $request->supplier_bank_account ?? null,
            'supplier_ewalet' => $request->supplier_ewalet ?? null,
            'supplier_notes' => $request->supplier_notes,
            'courier' => $request->courier,
            'shipment_number' => $request->shipment_number ?? null,
            'shipment_cost' => $request->shipment_cost,
            'delivery_file' => $filename ?? null,
            'estimate_arrive_date' => $request->est_arrive_date,
            'driver_contact' => $request->driver_phone ?? null,
        ));
 
        if ( $purchase->statut == "pending") {
 
            if ($request->response == "accept" ) {
                $purchase->update(array(
                    'statut' => 'ordered'
                ));
                
                return response()->json([
                    'success' => trans('Order Confirmed')
                ]);
            } else {
                $purchase->update(array(
                    'statut' => 'refused'
                ));
                
                return response()->json([
                'success' => trans('Order Refused')
            ]);
            }
        }
 
        elseif ( $purchase->statut == "ordered") {
 
            if ($request->response == "accept" ) {
                $purchase->update(array(
                    'statut' => 'shipped'
                ));
                
                return response()->json([
                    'success' => trans('Order shipping Confirmed')
                ]);
            } else {
                $purchase->update(array(
                    'statut' => 'refused'
                ));
                
                return response()->json([
                    'success' => trans('Order Canceled')
                ]);
            }
        }
 
        else {
            return response()->json([
                'error' => trans('Purchase not available to edit')
            ]);
        }
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
                'error' => trans("product not found")
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
                'error' => trans("product not found")
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

    public function returNumberOrder()
    {
        $last = PurchaseReturn::latest()->first();
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
            $code = 'PRT_0001';
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

 
    public function download($id)
    {
        $purchase = Purchase::find($id);
        $filePath = public_path('/hopeui/html/assets/files/purchases/deliveries/'.$purchase->delivery_file);
 
        return response()->download($filePath);
    }

    public function makePayment(Request $request, String $id)
    {
        $purchase = Purchase::find($id);
        $purchase_return = PurchaseReturn::where('purchase_id', $purchase->id);
        if (! $purchase) {
            return back()->with('warning', 'Data tidak ditemukan');
        }

        if ($request->payment_proof != null) {

            $file = $request->file('payment_proof');
            
            $filename = rand(11111111, 99999999).$file->getClientOriginalName();
 
            $file->move(public_path('/hopeui/html/assets/files/purchases/payment/'), $filename);

        }

        //ambil
        $payment_not_return = PaymentPurchase::whereNull('purchase_return_id')
                                                ->where('purchase_id', $purchase->id)
                                                ->get();

        $total_paid = 0;
        if ($payment_not_return->isNotEmpty()) { // Mengecek apakah ada data di collection
            foreach ($payment_not_return as $payment) {
                $total_paid += $payment->montant; // Akumulasi total pembayaran
            }
        }

        $payment_return = PaymentPurchase::whereNotNull('purchase_return_id')
                                                ->where('purchase_id', $purchase->id)
                                                ->get();
        $total_return_paid = 0;
        if ($payment_return->isNotEmpty()) { // Mengecek apakah ada data di collection
            foreach ($payment_return as $payment) {
                $total_return_paid += $payment->montant; // Akumulasi total pembayaran
            }
        }
 
        $total_balance = $purchase->GrandTotal - $total_paid - ($purchase_return->GrandTotal ?? 0) + $total_return_paid;

        if ($total_balance >= 0) {
            $supplier_must_pay = 0;
        } else {
            $supplier_must_pay = abs($total_balance);
        };


        if ($supplier_must_pay !== 0) {
            PaymentPurchase::create([
                'purchase_id' => $purchase->id,
                'purchase_return_id' => $purchase_return->id,
                'date' => Carbon::today('WIB'),
                'montant' => $request->paying_amount, // ambil gross_amount dari detail
                'Ref' => "INV-".$purchase->Ref,
                'Reglement' => 'cash', // ambil payment_type dari detail
                'user_id' => Auth::user()->id,
                'payment_proof' => $filename ?? null,
                'notes' => $request->payment_note,
            ]);

            $payment_return = PaymentPurchase::whereNotNull('purchase_return_id')
                                                ->where('purchase_id', $purchase->id)
                                                ->get();
            $total_return_paid = 0;
            if ($payment_return->isNotEmpty()) { // Mengecek apakah ada data di collection
                foreach ($payment_return as $payment) {
                    $total_return_paid += $payment->montant; // Akumulasi total pembayaran
                }
            }

            $purchase_return->update(array(
                'paid_amount' => $total_return_paid,
                'payment_method' => 'cash',
            ));

            if ($total_return_paid < $supplier_must_pay) {
                $purchase->update(array(
                    'payment_statut' => 'return unpaid',
                ));
            } else {
                $purchase->update(array(
                    'payment_statut' => 'return paid',
                ));
            };
        } else {
            
            PaymentPurchase::create([
                'purchase_id' => $purchase->id,
                'date' => Carbon::today('WIB'),
                'montant' => $request->paying_amount, // ambil gross_amount dari detail
                'Ref' => "INV-".$purchase->Ref,
                'Reglement' => $purchase->payment_method, // ambil payment_type dari detail
                'user_id' => Auth::user()->id,
                'payment_proof' => $filename ?? null,
                'notes' => $request->payment_note,
            ]);

            //ambil
            $payment_not_return = PaymentPurchase::whereNull('purchase_return_id')
                                                    ->where('purchase_id', $purchase->id)
                                                    ->get();

            $total_paid = 0;
            if ($payment_not_return->isNotEmpty()) { // Mengecek apakah ada data di collection
                foreach ($payment_not_return as $payment) {
                    $total_paid += $payment->montant; // Akumulasi total pembayaran
                }
            }

            $purchase->update(array(
                'paid_amount' => $total_paid,
            ));

            if ($total_paid == $purchase->GrandTotal) {
                $purchase->update(array(
                    'payment_statut' => 'paid',
                ));
            } else if ($total_paid >= $purchase->down_payment_net) {
                $purchase->update(array(
                    'payment_statut' => 'DP paid',
                ));
            } else {
                $purchase->update(array(
                    'payment_statut' => 'DP unfulfilled',
                ));
            };
        }

        return back()->with(['success' => 'Transaction success']);
    }

    public function makePayout() {
        $banks = ['bni', 'bri', 'permata', 'mandiri', 'bca'];
        $ewalets = ['ovo', 'gopay'];

        if (in_array($purchase->payment_method, $banks)) {
            $account=$purchase->supplier_bank_account;
        } else if (in_array($purchase->payment_method, $ewalets)){
            $account=$purchase->supplier_ewalet;
        };

        $curl = curl_init();

        curl_setopt_array($curl, [
        CURLOPT_URL => "https://app.sandbox.midtrans.com/iris/api/v1/payouts",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode([
            'payouts' => [
                "beneficiary_name" => "Permata Simulator A",
                "beneficiary_account" => $account,
                "beneficiary_bank" => $purchase->payment_method,
                "beneficiary_email" => $purchase->email,
                "amount" => $request->total_pay,
                "notes" => $request->payment_note
            ]
        ]),
        CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authorization: Basic U0ItTWlkLXNlcnZlci1KYnJKUjlVZzBDXzlicnJwM2xzNzNKcHY6",
            "content-type: application/json",
        ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        dd($response);
    }
}