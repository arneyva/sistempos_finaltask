<?php

namespace App\Http\Controllers\pos;

use Config;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\PaymentSale;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductWarehouse;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Membership;
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
use App\Mail\customerMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\URL;
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

    public function store(Request $request) {

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

        $staffId=$request->staff;
        $staff=User::findOrFail($staffId);
        $warehouse = $staff->warehouses->first();

        Sale::create ([
            'date' => Carbon::today('WIB'),
            'Ref' => $request->ref,
            'is_pos' => 1,
            'client_id' => $request->clientId ?? 1,
            'GrandTotal' => $request->order_total_input,
            'TaxNet' => $request->order_tax_input,
            'tax_rate' => 10,
            'warehouse_id' => $warehouse->id,
            'user_id' => $staffId,
            'statut' => "pending",
            'discount' => $request->discount_client,
            'paid_amount' => 0,
        ]);

        //ambil purchase
        $sale = Sale::latest()->first();

        //isi purchase detail dengan produk2 dan stoknya
        if ($request->input('products') != null && $request->input('products') !== "{}") {
            // Ambil array ID user dari request
            $productsToInput = json_decode($request->input('products'), true);
    
            foreach ($productsToInput as $product=>$qty) {
 
                //temukan purchase unit
                $product_sale_unit = Product::findOrFail($product)->unit_sale_id;
                //temukan price
                $price = Product::findOrFail($product)->price;
 
                $qty=(float)$qty;
 
                SaleDetail::create([
                    'date' => Carbon::today('WIB'),
                    'sale_id' => $sale->id,
                    'sale_unit_id' => $product_sale_unit,
                    'quantity' => $qty,
                    'product_id' => $product,
                    'total' => $price*$qty,
                    'price' => $price,
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
                $product_sale_unit = Product::findOrFail($product_id)->unit_purchase_id;
                //temukan cost
                $price = ProductVariant::findOrFail($product)->cost;
 
                $qty=(float)$qty;
 
                SaleDetail::create([
                    'date' => Carbon::today('WIB'),
                    'sale_id' => $sale->id,
                    'sale_unit_id' => $product_sale_unit,
                    'quantity' => $qty,
                    'product_id' => $product_id,
                    'product_variant_id' => $product,
                    'total' => $price*$qty,
                    'price' => $price,
                ]);
            }
        };

        if ($request->pending == "false") {
            $order_id=$request->order_id;

            if ($order_id !== "undefined") {
                // berarti pakai midtrans
                $order_detail = $this->getTransactionDetail($order_id);
                
                if ($order_detail) {
                    PaymentSale::create([
                        'sale_id' => $sale->id,
                        'date' => Carbon::today('WIB'),
                        'montant' => $order_detail['gross_amount'], // ambil gross_amount dari detail
                        'Ref' => $order_id,
                        'change' => 0,
                        'Reglement' => $order_detail['payment_type'], // ambil payment_type dari detail
                        'user_id' => $staffId,
                        'status' => 'completed',
                    ]);
                    $sale->update(array(
                        'statut' => "completed",
                        'paid_amount' => $order_detail['gross_amount'],
                        'payment_statut' => 'paid',
                        'payment_method' => $order_detail['payment_type'],
                    ));

                        // Ambil detail penjualan dengan eager loading produk
                        $saleDetails = SaleDetail::with('product')->where('sale_id', $sale->id)->get();

                        // Kumpulkan data untuk respons
                        $response = $saleDetails->map(function ($detail) {
                            return [
                                'quantity' => $detail->quantity,
                                'price' => $detail->price,
                                'total' => $detail->total,
                                'product_name' => $detail->product->name
                            ];
                        });

                        return response()->json([
                            'success' => 'Transaction success',
                            'products' => $response,
                        ]);
                } else {
                    // Tangani kasus di mana detail transaksi tidak ditemukan
                    return response()->json(['error' => 'Transaction not found'], 404);
                };
            } else {
                //berarti tidak pakai midtrans atau cash
                PaymentSale::create([
                    'sale_id' => $sale->id,
                    'date' => Carbon::today('WIB'),
                    'montant' => $request->paying_amount, // ambil gross_amount dari detail
                    'Ref' => $order_id,
                    'change' => $request->change_return,
                    'Reglement' => 'cash', // ambil payment_type dari detail
                    'user_id' => $staffId,
                    'status' => 'completed',
                ]);
                $sale->update(array(
                    'statut' => "completed",
                    'paid_amount' => $request->paying_amount,
                    'payment_statut' => 'paid',
                    'payment_method' => 'cash',
                ));

                // Ambil detail penjualan dengan eager loading produk
                $saleDetails = SaleDetail::with('product')->where('sale_id', $sale->id)->get();

                // Kumpulkan data untuk respons
                $response = $saleDetails->map(function ($detail) {
                    return [
                        'quantity' => $detail->quantity,
                        'price' => $detail->price,
                        'total' => $detail->total,
                        'product_name' => $detail->product->name
                    ];
                });

                return response()->json([
                    'success' => 'Transaction success',
                    'products' => $response,
                ]);
            };
        } else {
            //berarti masih pending dan langsung di return saja
            return response()->json(['success' => 'Transaction pending']);
        };
    }

    public function edit(String $id) {
        $sale = Sale::where('id', $id)->first();
        $products_selected = SaleDetail::where('sale_id', $sale->id)->get();
 
        // Ambil semua produk
        $products = Product::all();
 
        //ambil user akun ini 
        $user=Auth::user();
        //ambil warehousenya
        $warehouse = $user->warehouses->first();

        // Tanggal hari ini
        $today = Carbon::today()->toDateString();

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
        return view('templates.cashier.edit', [
            'products' => $allProduct,
            'products_selected' => $products_selected,
            'clients' => $clients,
            'warehouse' => $warehouse,
            'staff' => $staff,
            'user' => $user,
            'sales' => $sales,
            'currentSale' => $sale,
        ]);
    }

    public function update(Request $request, String $id) {
        $sale = Sale::findOrFail($id);
        if (!$sale) {
            if ($request->ajax()) {
                // Jika validasi berhasil, kembalikan pesan sukses
                return response()->json([
                    'error' => trans('The sale was deleted')
                ]);
            }
        };

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

        $staffId=$request->staff;
        $staff=User::findOrFail($staffId);
        $warehouse = $staff->warehouses->first();


        //delete product yang sudah ada
        SaleDetail::where('sale_id', $sale->id)->delete();

        //isi purchase detail dengan produk2 dan stoknya
        if ($request->input('products') != null && $request->input('products') !== "{}") {
            // Ambil array ID user dari request
            $productsToInput = json_decode($request->input('products'), true);
    
            foreach ($productsToInput as $product=>$qty) {
 
                //temukan purchase unit
                $product_sale_unit = Product::findOrFail($product)->unit_sale_id;
                //temukan price
                $price = Product::findOrFail($product)->price;
 
                $qty=(float)$qty;
 
                SaleDetail::create([
                    'date' => Carbon::today('WIB'),
                    'sale_id' => $sale->id,
                    'sale_unit_id' => $product_sale_unit,
                    'quantity' => $qty,
                    'product_id' => $product,
                    'total' => $price*$qty,
                    'price' => $price,
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
                $product_sale_unit = Product::findOrFail($product_id)->unit_purchase_id;
                //temukan cost
                $price = ProductVariant::findOrFail($product)->cost;
 
                $qty=(float)$qty;
 
                SaleDetail::create([
                    'date' => Carbon::today('WIB'),
                    'sale_id' => $sale->id,
                    'sale_unit_id' => $product_sale_unit,
                    'quantity' => $qty,
                    'product_id' => $product_id,
                    'product_variant_id' => $product,
                    'total' => $price*$qty,
                    'price' => $price,
                ]);
            }
        };

        if ($request->pending == "false") {
            $order_id=$request->order_id;

            if ($order_id !== "undefined") {
                // berarti pakai midtrans
                $order_detail = $this->getTransactionDetail($order_id);
                
                if ($order_detail) {
                    PaymentSale::create([
                        'sale_id' => $sale->id,
                        'date' => Carbon::today('WIB'),
                        'montant' => $order_detail['gross_amount'], // ambil gross_amount dari detail
                        'Ref' => $order_id,
                        'change' => 0,
                        'Reglement' => $order_detail['payment_type'], // ambil payment_type dari detail
                        'user_id' => $staffId,
                        'status' => 'completed',
                    ]);
                    $sale->update(array(
                        'statut' => "completed",
                        'paid_amount' => $order_detail['gross_amount'],
                        'payment_statut' => 'paid',
                        'payment_method' => $order_detail['payment_type'],
                    ));

                    // Ambil detail penjualan dengan eager loading produk
                    $saleDetails = SaleDetail::with('product')->where('sale_id', $sale->id)->get();

                    // Kumpulkan data untuk respons
                    $response = $saleDetails->map(function ($detail) {
                        return [
                            'quantity' => $detail->quantity,
                            'price' => $detail->price,
                            'total' => $detail->total,
                            'product_name' => $detail->product->name
                        ];
                    });

                    return response()->json([
                        'success' => 'Transaction success',
                        'products' => $response,
                    ]);

                    return response()->json(['success' => 'Transaction success']);
                } else {
                    // Tangani kasus di mana detail transaksi tidak ditemukan
                    return response()->json(['error' => 'Transaction not found'], 404);
                };
            } else {
                //berarti tidak pakai midtrans atau cash
                PaymentSale::create([
                    'sale_id' => $sale->id,
                    'date' => Carbon::today('WIB'),
                    'montant' => $request->paying_amount, // ambil gross_amount dari detail
                    'Ref' => "INV-".$sale->Ref,
                    'change' => $request->change_return,
                    'Reglement' => 'cash', // ambil payment_type dari detail
                    'user_id' => $staffId,
                    'status' => 'completed',
                ]);
                $sale->update(array(
                    'statut' => "completed",
                    'paid_amount' => $request->paying_amount,
                    'payment_statut' => 'paid',
                    'payment_method' => 'cash',
                ));

                // Ambil detail penjualan dengan eager loading produk
                $saleDetails = SaleDetail::with('product')->where('sale_id', $sale->id)->get();

                // Kumpulkan data untuk respons
                $response = $saleDetails->map(function ($detail) {
                    return [
                        'quantity' => $detail->quantity,
                        'price' => $detail->price,
                        'total' => $detail->total,
                        'product_name' => $detail->product->name
                    ];
                });

                return response()->json([
                    'success' => 'Transaction success',
                    'products' => $response,
                ]);

                return response()->json(['success' => 'Transaction success']);
            };
        } else {
            //berarti masih pending dan langsung di return saja
            return response()->json(['success' => 'Transaction success']);
        };
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

    public function getCustomer(String $email) {
        $customer = Client::where('email', $email)->first();

        if (!$customer) {
            return response()->json([
                'error' => trans('client not found')
            ]);
        }

        //ambil settingan membership
        $membership_term = Membership::latest()->first();
        $one_score_equal = $membership_term->one_score_equal;

        $discount_client=$one_score_equal * $customer->score;

        return response()->json([
            "customer" => $customer,
            "discount_client" => $discount_client,
        ]);
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

    public function sendEmail (String $email_client) {
        $email = [];
        $customer = Client::where('email', $email_client)->first();

        if (!$customer) {
            return response()->json([
                'error' => trans('client not found')
            ]);
        }
        $email['customer_name'] = $customer->name;

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

        //subject email
        $email['subject']= $email['customer_name'].", Claim your discount for shopping at ".$email['company_name'];

        //set mailing
        $this->Set_config_mail();

        
        //set unik url agar tidak semua tidak bisa mengakses halaman redirect
        $email['url'] = URL::signedRoute('client.landing', ['id' => $customer->id]);
        //kirim email ke email sesuai di purchase
        Mail::to($customer->email)->send(new customerMail($email));

        return response()->json([
            "success" => trans('Email Sended'),
        ]);
    }
    
    public function clientLanding(String $id) {
        $client = Client::findOrFail($id);

        $score =  $client->score;

        $membership_term = Membership::latest()->first();
        $one_score_equal = $membership_term->one_score_equal;

        $discount_client=$one_score_equal * $score;

        //set unik url agar tidak semua tidak bisa mengupdate
        $update_url = URL::signedRoute('client.redeem', ['id' => $client->id]);

        return view('templates.people.customer.redeem', [
            'score' => $score,
            'client' => $client,
            'discount' => $discount_client,
            'update_url' => $update_url,
        ]);
    }

    public function clientRedeem(String $id) {
        $client = Client::findOrFail($id);

        $score =  $client->score;

        if ($client->is_poin_activated == 1) {
            return redirect()->back()->with('error', 'You already redeem your score');
        };
        if ($score < 1) {
            return redirect()->back()->with('error', 'Make some purchase first');
        };


        $client->update(array(
            'is_poin_activated' => 1,
        ));

        return redirect()->back()->with('success', 'Redeem Sucessful');
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
}
