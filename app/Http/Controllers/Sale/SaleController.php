<?php

namespace App\Http\Controllers\Sale;

use App\Exports\SalesExport;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Membership;
use App\Models\PaymentSale;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductWarehouse;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\SaleReturn;
use App\Models\Setting;
use App\Models\Unit;
use App\Models\UserWarehouse;
use App\Models\Warehouse;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user_auth = auth()->user();
        $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');
        if ($user_auth->hasRole(['superadmin', 'inventaris'])) {
            $saleQuery = Sale::query()->with(['user', 'warehouse', 'client', 'paymentSales', 'shipment'])->where('deleted_at', '=', null)->latest();
        } else {
            $saleQuery = Sale::query()->with(['user', 'warehouse', 'client', 'paymentSales', 'shipment'])->where('deleted_at', '=', null)->where('warehouse_id', $warehouses_id)->latest();
        }
        if ($request->filled('date')) {
            $saleQuery->whereDate('date', '=', $request->input('date'));
        }
        if ($request->filled('Ref')) {
            $saleQuery->where('Ref', 'like', '%'.$request->input('Ref').'%');
        }

        if ($request->filled('warehouse_id')) {
            $saleQuery->where('warehouse_id', '=', $request->input('warehouse_id'));
        }
        if ($request->filled('client_id')) {
            $saleQuery->where('client_id', '=', $request->input('client_id'));
        }
        if ($request->filled('statut')) {
            $saleQuery->where('statut', '=', $request->input('statut'));
        }
        if ($request->filled('payment_statut')) {
            $saleQuery->where('payment_statut', '=', $request->input('payment_statut'));
        }
        if ($request->filled('shipping_status')) {
            $saleQuery->where('shipping_status', '=', $request->input('shipping_status'));
        }
        $sale = $saleQuery->paginate($request->input('limit', 5))->appends($request->except('page'));
        if ($user_auth->hasAnyRole(['superadmin', 'inventaris'])) {
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
        } else {
            $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
        }
        $client = Client::where('deleted_at', '=', null)->get(['id', 'name']);

        return view('templates.sale.index', [
            'sale' => $sale,
            'warehouse' => $warehouses,
            'client' => $client,
        ]);
    }

    public function export(Request $request)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "Sales_{$timestamp}.xlsx";

        return Excel::download(new SalesExport($request), $filename);
    }

    public function exportToPDF(Request $request)
    {
        $user_auth = auth()->user();
        $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');
        if ($user_auth->hasRole(['superadmin', 'inventaris'])) {
            $SaleQuery = Sale::query()->with(['user', 'warehouse', 'client', 'paymentSales'])->where('deleted_at', '=', null)->latest();
        } else {
            $SaleQuery = Sale::query()->with(['user', 'warehouse', 'client', 'paymentSales'])->where('deleted_at', '=', null)->where('to_warehouse_id', $warehouses_id)->latest();
        }
        // Terapkan filter berdasarkan parameter yang diterima dari request
        if ($request->has('date') && $request->filled('date')) {
            $SaleQuery->whereDate('date', '=', $request->input('date'));
        }

        if ($request->has('Ref') && $request->filled('Ref')) {
            $SaleQuery->where('Ref', 'like', '%'.$request->input('Ref').'%');
        }

        if ($request->has('warehouse_id') && $request->filled('warehouse_id')) {
            $SaleQuery->where('warehouse_id', '=', $request->input('warehouse_id'));
        }

        if ($request->has('client_id') && $request->filled('client_id')) {
            $SaleQuery->where('client_id', '=', $request->input('client_id'));
        }

        if ($request->has('statut') && $request->filled('statut')) {
            $SaleQuery->where('statut', '=', $request->input('statut'));
        }
        if ($request->has('payment_statut') && $request->filled('payment_statut')) {
            $SaleQuery->where('payment_statut', '=', $request->input('payment_statut'));
        }
        if ($request->has('shipping_status') && $request->filled('shipping_status')) {
            $SaleQuery->where('shipping_status', '=', $request->input('shipping_status'));
        }

        // Lakukan sorting sesuai request jika diperlukan
        if ($request->has('SortField') && $request->has('SortType')) {
            $sortField = $request->input('SortField');
            $sortType = $request->input('SortType');
            $SaleQuery->orderBy($sortField, $sortType);
        }

        $sales = $SaleQuery->get();

        // Generate PDF
        $pdf = Pdf::loadView('export.sale', compact('sales'));

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');

        return $pdf->download("sales_{$timestamp}.pdf");
    }

    // public function shipments()
    // {
    //     return view('templates.sale.shipments');
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $warehouse = Warehouse::query()->get();
        $client = Client::query()->get();

        return view('templates.sale.create', ['warehouse' => $warehouse, 'client' => $client]);
    }
    //------------- Reference Number Order SALE -----------\\

    public function getNumberOrder()
    {

        $last = DB::table('sales')->latest('id')->first();

        if ($last) {
            $item = $last->Ref;
            $nwMsg = explode('_', $item);
            $inMsg = $nwMsg[1] + 1;
            $code = $nwMsg[0].'_'.$inMsg;
        } else {
            // $code = 'SL_1111';
            $code = 'SL_1';
        }

        return $code;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        request()->validate([
            'client_id' => 'required',
            'warehouse_id' => 'required',
        ]);
        \DB::transaction(function () use ($request) {
            $order = new Sale();
            $order->is_pos = 0;
            $order->date = $request->date;
            $order->Ref = $this->getNumberOrder();
            $order->client_id = $request->client_id;
            $order->GrandTotal = $request->GrandTotal;
            $order->warehouse_id = $request->warehouse_id;
            $order->tax_rate = $request->tax_rate;
            $order->TaxNet = $request->TaxNet;
            $order->discount = $request->discount;
            $order->shipping = $request->shipping;
            $order->statut = $request->statut;
            // handle status pending
            $order->payment_method = $request->payment_method;
            if ($order->statut == 'pending') {
                $order->payment_statut = 'unpaid';
                $order->paid_amount = 0;
            } elseif ($order->statut == 'completed') {
                if ($order->payment_method == 'cash') {
                    $order->payment_statut = 'paid';
                    $order->paid_amount = $request->GrandTotal;
                } elseif ($order->payment_method == 'midtrans') {
                    $order->payment_statut = 'unpaid';
                } else {
                    $order->payment_statut = 'unpaid';
                }
            }
            $order->notes = $request->notes;
            $order->user_id = Auth::user()->id;
            $order->save();
            $data = $request['details'];
            foreach ($data as $key => $value) {
                $unit = Unit::where('id', $value['sale_unit_id'])
                    ->first();
                $orderDetails[] = [
                    'date' => $request->date,
                    'sale_id' => $order->id,
                    'sale_unit_id' => $value['sale_unit_id'] ? $value['sale_unit_id'] : null,
                    'quantity' => $value['quantity'],
                    'price' => $value['Unit_price'],
                    'TaxNet' => $value['tax_percent'],
                    'tax_method' => $value['tax_method'],
                    'discount' => 0,
                    'discount_method' => 'nodiscount',
                    'product_id' => $value['product_id'],
                    'product_variant_id' => $value['product_variant_id'] ? $value['product_variant_id'] : null,
                    'total' => $value['subtotal'],
                    'imei_number' => $value['imei_number'] ?? null,
                ];

                if ($order->statut == 'completed') {
                    if ($value['product_variant_id'] !== null) {
                        $product_warehouse = ProductWarehouse::where('deleted_at', '=', null)
                            ->where('warehouse_id', $order->warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->where('product_variant_id', $value['product_variant_id'])
                            ->first();

                        if ($unit && $product_warehouse) {
                            if ($unit->operator == '/') {
                                $product_warehouse->qty -= $value['quantity'] / $unit->operator_value;
                            } else {
                                $product_warehouse->qty -= $value['quantity'] * $unit->operator_value;
                            }
                            $product_warehouse->save();
                        }
                    } else {
                        $product_warehouse = ProductWarehouse::where('deleted_at', '=', null)
                            ->where('warehouse_id', $order->warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->first();

                        if ($unit && $product_warehouse) {
                            if ($unit->operator == '/') {
                                $product_warehouse->qty -= $value['quantity'] / $unit->operator_value;
                            } else {
                                $product_warehouse->qty -= $value['quantity'] * $unit->operator_value;
                            }
                            $product_warehouse->save();
                        }
                    }
                }
            }
            SaleDetail::insert($orderDetails);
            if ($order->statut == 'completed') {
                if ($order->payment_method == 'midtrans') {
                    $transaction = PaymentSale::create([
                        'user_id' => $order->user_id,
                        'date' => $order->date,
                        'Ref' => 'INV-'.$order->Ref,
                        'sale_id' => $order->id,
                        'montant' => $order->GrandTotal,
                        'change' => 0,
                        'Reglement' => 'midtrans',
                        'status' => 'pending',
                    ]);
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
                            'order_id' => rand(),
                            'gross_amount' => $order->GrandTotal,
                        ],
                    ];
                    $snapToken = \Midtrans\Snap::getSnapToken($params);
                    $transaction->Reglement = $snapToken;
                    $transaction->save();
                } else {
                    $transaction = PaymentSale::create([
                        'user_id' => $order->user_id,
                        'date' => $order->date,
                        'Ref' => 'INV-'.$order->Ref,
                        'sale_id' => $order->id,
                        'montant' => $order->GrandTotal,
                        'change' => $request->change_return ?? 0,
                        'Reglement' => 'cash',
                        'status' => 'success',
                    ]);
                }
            } elseif ($order->statut == 'pending') {
                $transaction = PaymentSale::create([
                    'user_id' => $order->user_id,
                    'date' => $order->date,
                    'Ref' => 'INV-'.$order->Ref,
                    'sale_id' => $order->id,
                    'montant' => $order->GrandTotal,
                    'change' => 0,
                    'Reglement' => 'pending',
                    'status' => 'pending',
                ]);
            }

            //{{============= Integrasi Midtrans ===================}}\\
            $sale = Sale::findOrFail($order->id);

            $detail_sale = Sale::with('details')->find($order->id);

            //{{==================================================================}}\\
            //{{=============================== ROPIQ ==============================}}\\
            //{{==================================================================}}\\
            // Mengambil client_id dari sale
            $clientId = $sale->client_id;
            // Cek apakah client_id bukan default
            if ($clientId != 1) {
                // Mengambil client dari sale
                $client_sale = Client::find($clientId);
                if ($client_sale) {
                    //hitung harga bersih barang
                    if ($detail_sale) {
                        $total_spend = 0;
                        foreach ($detail_sale->details as $detail) {
                            $total_spend += $detail->total - $detail->TaxNet;
                        }
                    }

                    //ambil settingan membershgip
                    $membership_term = Membership::latest()->first();

                    $spend_every = $membership_term->spend_every;
                    $score_to_email = $membership_term->score_to_email;
                    $one_score_equal = $membership_term->one_score_equal;

                    //hitung score yang didapat berdasarkan settingan membership
                    $total_score = intdiv($total_spend, $spend_every);

                    // Menambahkan total_score ke client score
                    $client_sale->score += $total_score;

                    // Menyimpan perubahan pada client
                    $client_sale->save();
                }
            }
            //{{=========================================================================}}\\
            //{{==================================================================}}\\
            //{{==========================================================}}\\
        }, 10);

        // dd($request->all());
        return redirect()->route('sale.index')->with('success', 'Sale created successfully');
        // return response()->json(['success' => true]);
    }

    public function success($transactionId)
    {
        // Find the transaction by ID
        $transaction = PaymentSale::find($transactionId);
        if ($transaction) {
            // Update the transaction status to success
            $transaction->status = 'success';
            $transaction->save();

            // Update the associated sale's payment status
            $sale = Sale::find($transaction->sale_id);
            if ($sale) {
                $sale->payment_statut = 'paid';
                $sale->paid_amount = $sale->GrandTotal;
                $sale->save();
            }

            return redirect()->route('sale.index')->with('success', 'Payment Sales successfully');
        } else {
            return redirect()->route('sale.index')->with('error', 'Transaction not found');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $sale_data = Sale::with('details.product.unitSale')
            ->where('deleted_at', '=', null)
            ->findOrFail($id);

        $details = [];
        $sale_details['Ref'] = $sale_data->Ref;
        $sale_details['date'] = $sale_data->date;
        $sale_details['note'] = $sale_data->notes;
        $sale_details['statut'] = $sale_data->statut;
        $sale_details['warehouse'] = $sale_data['warehouse']->name;
        $sale_details['discount'] = $sale_data->discount;
        $sale_details['shipping'] = $sale_data->shipping;
        $sale_details['tax_rate'] = $sale_data->tax_rate;
        $sale_details['TaxNet'] = $sale_data->TaxNet;
        $sale_details['client_name'] = $sale_data['client']->name;
        $sale_details['client_phone'] = $sale_data['client']->phone;
        $sale_details['client_adr'] = $sale_data['client']->adresse;
        $sale_details['client_email'] = $sale_data['client']->email;
        $sale_details['client_tax'] = $sale_data['client']->tax_number;
        $sale_details['GrandTotal'] = number_format($sale_data->GrandTotal, 2, '.', '');
        $sale_details['paid_amount'] = number_format($sale_data->paid_amount, 2, '.', '');
        $sale_details['due'] = number_format($sale_details['GrandTotal'] - $sale_details['paid_amount'], 2, '.', '');
        $sale_details['payment_status'] = $sale_data->payment_statut;

        if (SaleReturn::where('sale_id', $id)->where('deleted_at', '=', null)->exists()) {
            $sellReturn = SaleReturn::where('sale_id', $id)->where('deleted_at', '=', null)->first();
            $sale_details['salereturn_id'] = $sellReturn->id;
            $sale_details['sale_has_return'] = 'yes';
        } else {
            $sale_details['sale_has_return'] = 'no';
        }

        foreach ($sale_data['details'] as $detail) {

            //check if detail has sale_unit_id Or Null
            if ($detail->sale_unit_id !== null) {
                $unit = Unit::where('id', $detail->sale_unit_id)->first();
            } else {
                $product_unit_sale_id = Product::with('unitSale')
                    ->where('id', $detail->product_id)
                    ->first();

                if ($product_unit_sale_id['unitSale']) {
                    $unit = Unit::where('id', $product_unit_sale_id['unitSale']->id)->first();
                }
                $unit = null;
            }

            if ($detail->product_variant_id) {

                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)->first();

                $data['code'] = $productsVariants->code;
                $data['name'] = '['.$productsVariants->name.']'.$detail['product']['name'];
            } else {
                $data['code'] = $detail['product']['code'];
                $data['name'] = $detail['product']['name'];
            }

            $data['quantity'] = $detail->quantity;
            $data['total'] = $detail->total;
            $data['price'] = $detail->price;
            $data['unit_sale'] = $unit ? $unit->ShortName : '';

            if ($detail->discount_method == '2') {
                $data['DiscountNet'] = $detail->discount;
            } else {
                $data['DiscountNet'] = $detail->price * $detail->discount / 100;
            }

            $tax_price = $detail->TaxNet * (($detail->price - $data['DiscountNet']) / 100);
            $data['Unit_price'] = $detail->price;
            $data['discount'] = $detail->discount;

            if ($detail->tax_method == 'Exclusive') {
                $data['Net_price'] = $detail->price - $data['DiscountNet'];
                $data['taxe'] = $tax_price;
            } else {
                $data['Net_price'] = ($detail->price - $data['DiscountNet']) / (($detail->TaxNet / 100) + 1);
                $data['taxe'] = $detail->price - $data['Net_price'] - $data['DiscountNet'];
            }

            $data['is_imei'] = $detail['product']['is_imei'];
            $data['imei_number'] = $detail->imei_number;

            $details[] = $data;
        }

        $company = Setting::where('deleted_at', '=', null)->first();

        // return response()->json([
        //     'details' => $details,
        //     'sale' => $sale_details,
        //     // 'company' => $company,
        // ]);
        return view(
            'templates.sale.show',
            [
                'details' => $details,
                'sale' => $sale_details,
                'company' => $company,
            ]
        );
    }

    public function Payments_Sale(Request $request, $id)
    {
        $Sale = Sale::findOrFail($id);
        $payments = PaymentSale::with('sale')
            ->where('sale_id', $id)->orderBy('id', 'DESC')->get();

        $due = $Sale->GrandTotal - $Sale->paid_amount;

        return response()->json(['payments' => $payments, 'due' => $due]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        if (SaleReturn::where('sale_id', $id)->where('deleted_at', '=', null)->exists()) {
            return response()->json(['success' => false, 'Return exist for the Transaction' => false], 403);
        } else {
            $Sale_data = Sale::with('details.product.unitSale')
                ->where('deleted_at', '=', null)
                ->findOrFail($id);
            $details = [];
            if ($Sale_data->client_id) {
                if (Client::where('id', $Sale_data->client_id)
                    ->where('deleted_at', '=', null)
                    ->first()
                ) {
                    $sale['client_id'] = $Sale_data->client_id;
                } else {
                    $sale['client_id'] = '';
                }
            } else {
                $sale['client_id'] = '';
            }

            if ($Sale_data->warehouse_id) {
                if (Warehouse::where('id', $Sale_data->warehouse_id)
                    ->where('deleted_at', '=', null)
                    ->first()
                ) {
                    $sale['warehouse_id'] = $Sale_data->warehouse_id;
                } else {
                    $sale['warehouse_id'] = '';
                }
            } else {
                $sale['warehouse_id'] = '';
            }

            $sale['id'] = $Sale_data->id;
            $sale['GrandTotal'] = $Sale_data->GrandTotal;
            $sale['date'] = $Sale_data->date;
            $sale['tax_rate'] = $Sale_data->tax_rate;
            $sale['TaxNet'] = $Sale_data->TaxNet;
            $sale['discount'] = $Sale_data->discount;
            $sale['shipping'] = $Sale_data->shipping;
            $sale['statut'] = $Sale_data->statut;
            $sale['payment_method'] = $Sale_data->payment_method ?? 'cash';
            $sale['notes'] = $Sale_data->notes;

            $detail_id = 0;
            foreach ($Sale_data['details'] as $detail) {

                //check if detail has sale_unit_id Or Null
                if ($detail->sale_unit_id !== null) {
                    $unit = Unit::where('id', $detail->sale_unit_id)->first();
                    $data['no_unit'] = 1;
                } else {
                    $product_unit_sale_id = Product::with('unitSale')
                        ->where('id', $detail->product_id)
                        ->first();

                    if ($product_unit_sale_id['unitSale']) {
                        $unit = Unit::where('id', $product_unit_sale_id['unitSale']->id)->first();
                    }
                    $unit = null;

                    $data['no_unit'] = 0;
                }

                if ($detail->product_variant_id) {
                    $item_product = ProductWarehouse::where('product_id', $detail->product_id)
                        ->where('deleted_at', '=', null)
                        ->where('product_variant_id', $detail->product_variant_id)
                        ->where('warehouse_id', $Sale_data->warehouse_id)
                        ->first();

                    $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                        ->where('id', $detail->product_variant_id)->first();

                    $item_product ? $data['del'] = 0 : $data['del'] = 1;
                    $data['product_variant_id'] = $detail->product_variant_id;
                    $data['code'] = $productsVariants->code;
                    $data['name'] = '['.$productsVariants->name.']'.$detail['product']['name'];

                    if ($unit && $unit->operator == '/') {
                        $stock = $item_product ? $item_product->qty * $unit->operator_value : 0;
                    } elseif ($unit && $unit->operator == '*') {
                        $stock = $item_product ? $item_product->qty / $unit->operator_value : 0;
                    } else {
                        $stock = 0;
                    }
                } else {
                    $item_product = ProductWarehouse::where('product_id', $detail->product_id)
                        ->where('deleted_at', '=', null)->where('warehouse_id', $Sale_data->warehouse_id)
                        ->where('product_variant_id', '=', null)->first();

                    $item_product ? $data['del'] = 0 : $data['del'] = 1;
                    $data['product_variant_id'] = null;
                    $data['code'] = $detail['product']['code'];
                    $data['name'] = $detail['product']['name'];

                    if ($unit && $unit->operator == '/') {
                        $stock = $item_product ? $item_product->qty * $unit->operator_value : 0;
                    } elseif ($unit && $unit->operator == '*') {
                        $stock = $item_product ? $item_product->qty / $unit->operator_value : 0;
                    } else {
                        $stock = 0;
                    }
                }

                $data['id'] = $detail->id;
                $data['stock'] = $detail['product']['type'] != 'is_service' ? $stock : '---';
                $data['product_type'] = $detail['product']['type'];
                $data['detail_id'] = $detail_id += 1;
                $data['product_id'] = $detail->product_id;
                $data['total'] = $detail->total;
                $data['quantity'] = $detail->quantity;
                $data['qty_copy'] = $detail->quantity;
                $data['etat'] = 'current';
                $data['unitSale'] = $unit ? $unit->ShortName : '';
                $data['sale_unit_id'] = $unit ? $unit->id : '';
                $data['is_imei'] = $detail['product']['is_imei'];
                $data['imei_number'] = $detail->imei_number;

                if ($detail->discount_method == '2') {
                    $data['DiscountNet'] = $detail->discount;
                } else {
                    $data['DiscountNet'] = $detail->price * $detail->discount / 100;
                }

                $tax_price = $detail->TaxNet * (($detail->price - $data['DiscountNet']) / 100);
                $data['Unit_price'] = $detail->price;

                $data['tax_percent'] = $detail->TaxNet;
                $data['tax_method'] = $detail->tax_method;
                $data['discount'] = $detail->discount;
                $data['discount_Method'] = $detail->discount_method;

                if ($detail->tax_method == 'Exclusive') {
                    $data['Net_price'] = $detail->price - $data['DiscountNet'];
                    $data['taxe'] = $tax_price;
                    $data['subtotal'] = ($data['Net_price'] * $data['quantity']) + ($tax_price * $data['quantity']);
                } else {
                    $data['Net_price'] = ($detail->price - $data['DiscountNet']) / (($detail->TaxNet / 100) + 1);
                    $data['taxe'] = $detail->price - $data['Net_price'] - $data['DiscountNet'];
                    $data['subtotal'] = ($data['Net_price'] * $data['quantity']) + ($tax_price * $data['quantity']);
                }

                $details[] = $data;
            }

            //get warehouses assigned to user
            $user_auth = auth()->user();
            if ($user_auth->is_all_warehouses) {
                $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
            } else {
                $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
                $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
            }

            $clients = Client::where('deleted_at', '=', null)->get(['id', 'name']);

            // return response()->json([
            //     'details' => $details,
            //     'sale' => $sale,
            //     'clients' => $clients,
            //     'warehouses' => $warehouses,
            // ]);
            return view('templates.sale.edit', [
                'details' => $details,
                'sale' => $sale,
                'client' => $clients,
                'warehouse' => $warehouses,
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        request()->validate([
            'warehouse_id' => 'required',
            'client_id' => 'required',
        ]);

        \DB::transaction(function () use ($request, $id) {
            $current_Sale = Sale::findOrFail($id);

            if (SaleReturn::where('sale_id', $id)->where('deleted_at', '=', null)->exists()) {
                return response()->json(['success' => false, 'Return exist for the Transaction' => false], 403);
            } else {
                $old_sale_details = SaleDetail::where('sale_id', $id)->get();
                $new_sale_details = $request['details'];
                $length = count($new_sale_details);

                // Get Ids for new Details
                $new_products_id = [];
                foreach ($new_sale_details as $new_detail) {
                    $new_products_id[] = $new_detail['id'];
                }

                // Init Data with old Parametre
                $old_products_id = [];
                foreach ($old_sale_details as $key => $value) {
                    $old_products_id[] = $value->id;

                    //check if detail has sale_unit_id Or Null
                    if ($value['sale_unit_id'] !== null) {
                        $old_unit = Unit::where('id', $value['sale_unit_id'])->first();
                    } else {
                        $product_unit_sale_id = Product::with('unitSale')
                            ->where('id', $value['product_id'])
                            ->first();

                        if ($product_unit_sale_id['unitSale']) {
                            $old_unit = Unit::where('id', $product_unit_sale_id['unitSale']->id)->first();
                        }
                        $old_unit = null;
                    }

                    if ($current_Sale->statut == 'completed') {

                        if ($value['product_variant_id'] !== null) {
                            $product_warehouse = ProductWarehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $current_Sale->warehouse_id)
                                ->where('product_id', $value['product_id'])
                                ->where('product_variant_id', $value['product_variant_id'])
                                ->first();

                            if ($product_warehouse && $old_unit) {
                                if ($old_unit->operator == '/') {
                                    $product_warehouse->qty += $value['quantity'] / $old_unit->operator_value;
                                } else {
                                    $product_warehouse->qty += $value['quantity'] * $old_unit->operator_value;
                                }
                                $product_warehouse->save();
                            }
                        } else {
                            $product_warehouse = ProductWarehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $current_Sale->warehouse_id)
                                ->where('product_id', $value['product_id'])
                                ->first();
                            if ($product_warehouse && $old_unit) {
                                if ($old_unit->operator == '/') {
                                    $product_warehouse->qty += $value['quantity'] / $old_unit->operator_value;
                                } else {
                                    $product_warehouse->qty += $value['quantity'] * $old_unit->operator_value;
                                }
                                $product_warehouse->save();
                            }
                        }
                    }
                    // Delete Detail
                    if (! in_array($old_products_id[$key], $new_products_id)) {
                        $SaleDetail = SaleDetail::findOrFail($value->id);
                        $SaleDetail->delete();
                    }
                }

                // Update Data with New request
                foreach ($new_sale_details as $prd => $prod_detail) {

                    $get_type_product = Product::where('id', $prod_detail['product_id'])->first()->type;

                    if ($prod_detail['sale_unit_id'] !== null || $get_type_product == 'is_service') {
                        $unit_prod = Unit::where('id', $prod_detail['sale_unit_id'])->first();

                        if ($request['statut'] == 'completed') {

                            if ($prod_detail['product_variant_id'] !== null) {
                                $product_warehouse = ProductWarehouse::where('deleted_at', '=', null)
                                    ->where('warehouse_id', $request->warehouse_id)
                                    ->where('product_id', $prod_detail['product_id'])
                                    ->where('product_variant_id', $prod_detail['product_variant_id'])
                                    ->first();

                                if ($product_warehouse && $unit_prod) {
                                    if ($unit_prod->operator == '/') {
                                        $product_warehouse->qty -= $prod_detail['quantity'] / $unit_prod->operator_value;
                                    } else {
                                        $product_warehouse->qty -= $prod_detail['quantity'] * $unit_prod->operator_value;
                                    }
                                    $product_warehouse->save();
                                }
                            } else {
                                $product_warehouse = ProductWarehouse::where('deleted_at', '=', null)
                                    ->where('warehouse_id', $request->warehouse_id)
                                    ->where('product_id', $prod_detail['product_id'])
                                    ->first();

                                if ($product_warehouse && $unit_prod) {
                                    if ($unit_prod->operator == '/') {
                                        $product_warehouse->qty -= $prod_detail['quantity'] / $unit_prod->operator_value;
                                    } else {
                                        $product_warehouse->qty -= $prod_detail['quantity'] * $unit_prod->operator_value;
                                    }
                                    $product_warehouse->save();
                                }
                            }
                        }

                        $orderDetails['sale_id'] = $id;
                        $orderDetails['date'] = $request['date'];
                        $orderDetails['price'] = $prod_detail['Unit_price'];
                        $orderDetails['sale_unit_id'] = $prod_detail['sale_unit_id'];
                        $orderDetails['TaxNet'] = $prod_detail['tax_percent'];
                        $orderDetails['tax_method'] = 'Exclusive';
                        $orderDetails['discount'] = 0;
                        $orderDetails['discount_method'] = 'nodiscount';
                        $orderDetails['quantity'] = $prod_detail['quantity'];
                        $orderDetails['product_id'] = $prod_detail['product_id'];
                        $orderDetails['product_variant_id'] = $prod_detail['product_variant_id'];
                        $orderDetails['total'] = $prod_detail['subtotal'];
                        // $orderDetails['imei_number']        = $prod_detail['imei_number'];

                        if (! in_array($prod_detail['id'], $old_products_id)) {
                            $orderDetails['date'] = Carbon::now();
                            $orderDetails['sale_unit_id'] = $unit_prod ? $unit_prod->id : null;
                            SaleDetail::Create($orderDetails);
                        } else {
                            SaleDetail::where('id', $prod_detail['id'])->update($orderDetails);
                        }
                    }
                }
                $payment_method = $request->payment_method;
                $transaction = PaymentSale::where('sale_id', $id)->first();
                $updateData = [
                    'date' => $request['date'],
                    'client_id' => $request['client_id'],
                    'warehouse_id' => $request['warehouse_id'],
                    'notes' => $request['notes'],
                    'statut' => $request['statut'],
                    'tax_rate' => $request['tax_rate'],
                    'TaxNet' => $request['TaxNet'],
                    'discount' => $request['discount'],
                    'shipping' => $request['shipping'],
                    'GrandTotal' => $request['GrandTotal'],
                    'payment_method' => $payment_method,
                ];
                if ($payment_method == 'cash') {
                    $updateData['paid_amount'] = $request['GrandTotal'];
                    $updateData['payment_statut'] = 'paid';
                    if ($transaction) {
                        $transaction->update([
                            'montant' => $request->GrandTotal,
                            'change' => $request->change_return ?? 0,
                            'Reglement' => 'cash',
                            'status' => 'success',
                        ]);
                    }
                } else {
                    $updateData['paid_amount'] = 0;
                    $updateData['payment_statut'] = 'unpaid';
                    if ($transaction) {
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
                                'order_id' => rand(),
                                'gross_amount' => $request->GrandTotal,
                            ],
                        ];
                        $snapToken = \Midtrans\Snap::getSnapToken($params);
                        $transaction->update([
                            'montant' => $request->GrandTotal,
                            'change' => 0,
                            'Reglement' => $snapToken,
                            'status' => 'pending',
                        ]);
                    }
                }

                $current_Sale->update($updateData);
            }
        }, 10);

        // return response()->json(['success' => true]);
        return redirect()->route('sale.index')->with('success', 'Sale Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
