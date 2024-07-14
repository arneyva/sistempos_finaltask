<?php

namespace App\Http\Controllers\Sale;

use App\Exports\SalesReturnExport;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\PaymentSaleReturns;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductWarehouse;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\SaleReturnDetails;
use App\Models\Setting;
use App\Models\Unit;
use App\Models\UserWarehouse;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SaleReturnController extends Controller
{
    public function index(Request $request)
    {
        $user_auth = auth()->user();
        $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');
        //  berdasarkan role user yang login
        if ($user_auth->hasRole(['superadmin', 'inventaris'])) {
            $salereturnQuery = SaleReturn::query()->with(['sale', 'facture', 'client', 'warehouse'])->where('deleted_at', '=', null)->latest();
        } else {
            $salereturnQuery = SaleReturn::query()->with(['sale', 'facture', 'client', 'warehouse'])->where('deleted_at', '=', null)->where('warehouse_id', $warehouses_id)->latest();
        }
        // filtering
        if ($request->filled('date')) {
            $salereturnQuery->whereDate('date', '=', $request->input('date'));
        }
        if ($request->filled('Ref')) {
            $salereturnQuery->where('Ref', 'like', '%' . $request->input('Ref') . '%');
        }

        if ($request->filled('warehouse_id')) {
            $salereturnQuery->where('warehouse_id', '=', $request->input('warehouse_id'));
        }
        if ($request->filled('client_id')) {
            $salereturnQuery->where('client_id', '=', $request->input('client_id'));
        }
        if ($request->filled('statut')) {
            $salereturnQuery->where('statut', '=', $request->input('statut'));
        }
        // menampilkan data hasil filtering dan dipaginasi
        $salereturn = $salereturnQuery->paginate($request->input('limit', 5))->appends($request->except('page'));
        // mendapatkan data warehouse berdasarkan role user yang login
        if ($user_auth->hasAnyRole(['superadmin', 'inventaris'])) {
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
        } else {
            $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
        }
        // mendapatkan data clinet berdasarkan role user yang login
        $client = Client::where('deleted_at', '=', null)->get(['id', 'name']);
        // mengirim data ke frontend
        return view('templates.sale.return_index', [
            'salereturn' => $salereturn,
            'warehouse' => $warehouses,
            'client' => $client,
        ]);
    }

    public function create_sell_return(Request $request, $id)
    {

        $cek = SaleReturn::where('sale_id', $id)->first();
        if ($cek) {
            return redirect()->back()->with('error', 'Sale Return Already Created');
        };
        $SaleReturn = Sale::with('details.product.unitSale')
            ->where('deleted_at', '=', null)
            ->findOrFail($id);

        $details = [];
        $Return_detail['client_id'] = $SaleReturn->client_id;
        $Return_detail['warehouse_id'] = $SaleReturn->warehouse_id;
        $Return_detail['sale_id'] = $SaleReturn->id;
        $Return_detail['sale_ref'] = $SaleReturn->Ref;
        $Return_detail['tax_rate'] = $SaleReturn->tax_rate;
        $Return_detail['TaxNet'] = $SaleReturn->TaxNet;
        $Return_detail['discount'] = $SaleReturn->discount;
        $Return_detail['shipping'] = $SaleReturn->shipping;
        $Return_detail['statut'] = 'received';
        $Return_detail['notes'] = $SaleReturn->notes;

        $detail_id = 0;
        foreach ($SaleReturn['details'] as $detail) {

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
                    ->where('product_variant_id', $detail->product_variant_id)
                    ->where('deleted_at', '=', null)
                    ->where('warehouse_id', $SaleReturn->warehouse_id)
                    ->first();

                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)->first();

                $item_product ? $data['del'] = 0 : $data['del'] = 1;
                $data['product_variant_id'] = $detail->product_variant_id;
                $data['code'] = $productsVariants->code;
                $data['quantity_discount'] = $item_product->quantity_discount;
                $data['discount_percentage'] = $item_product->discount_percentage;

                $data['name'] = '[' . $productsVariants->name . ']' . $detail['product']['name'];
            } else {
                $item_product = ProductWarehouse::where('product_id', $detail->product_id)
                    ->where('warehouse_id', $SaleReturn->warehouse_id)
                    ->where('deleted_at', '=', null)->where('product_variant_id', '=', null)
                    ->first();

                $item_product ? $data['del'] = 0 : $data['del'] = 1;
                $data['product_variant_id'] = null;
                $data['code'] = $detail['product']['code'];
                $data['name'] = $detail['product']['name'];
                $data['quantity_discount'] = $item_product->quantity_discount;
                $data['discount_percentage'] = $item_product->discount_percentage;
            }

            $data['id'] = $detail->id;
            $data['detail_id'] = $detail_id += 1;
            $data['product_type'] = $detail['product']['type'];
            $data['quantity'] = 0;
            $data['sale_quantity'] = $detail->quantity;
            $data['product_id'] = $detail->product_id;
            $data['total'] = $detail->total;
            $data['unitSale'] = $unit ? $unit->ShortName : '';
            $data['sale_unit_id'] = $unit ? $unit->id : '';
            $data['is_imei'] = $detail['product']['is_imei'];
            $data['imei_number'] = $detail->imei_number;
            if ($detail->discount_method == 'discount') {
                $data['DiscountNet'] = $detail->discount;
            } else {
                $data['DiscountNet'] = $detail->price * $detail->discount / 100;
            }

            $tax_price = $detail->TaxNet * ($detail->price / 100);
            $data['Unit_price'] = $detail->price;
            $data['tax_percent'] = $detail->TaxNet;
            $data['tax_method'] = $detail->tax_method;
            $data['discount'] = $detail->discount;
            $data['discount_method'] = $detail->discount_method;
            if ($detail->tax_method == 'Exclusive') {
                $data['Net_price'] = $detail->price - $data['DiscountNet'];
                $data['taxe'] = $tax_price;
                $data['subtotal'] = ($data['Net_price'] * $data['quantity']) + ($tax_price * $data['quantity']);
            } else {
                $data['Net_price'] = ($detail->price - $data['DiscountNet']) / (($detail->TaxNet / 100) + 1);
                $data['taxe'] = $detail->price - $data['Net_price'] - $data['DiscountNet'];
                $data['subtotal'] = ($data['Net_price'] * $data['quantity']) + ($tax_price * $data['quantity']);
            }

            // Memasukkan $data ke dalam array $details
            $details[] = $data;
        }

        // return response()->json([
        //     'details' => $details,
        //     'sale_return' => $Return_detail,
        // ]);
        return view('templates.sale.return_create', [
            'details' => $details,
            'sale_return' => $Return_detail,
        ]);
    }

    public function export(Request $request)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "SalesReturn_{$timestamp}.xlsx";

        return Excel::download(new SalesReturnExport($request), $filename);
    }

    public function getNumberOrder()
    {
        $last = DB::table('sale_returns')->latest('id')->first();

        if ($last) {
            $item = $last->Ref;
            $nwMsg = explode('_', $item);
            $inMsg = $nwMsg[1] + 1;
            $code = $nwMsg[0] . '_' . $inMsg;
        } else {
            $code = 'RT_1111';
        }

        return $code;
    }
    public function getNumberOrderPayement()
    {
        $last = DB::table('payment_sale_returns')->latest('id')->first();

        if ($last) {
            $item = $last->Ref;
            $nwMsg = explode("_", $item);
            $inMsg = $nwMsg[1] + 1;
            $code = $nwMsg[0] . '_' . $inMsg;
        } else {
            $code = 'INV/RT_1111';
        }
        return $code;
    }

    public function store(request $request)
    {
        try {
            request()->validate([
                'client_id' => 'required',
                'warehouse_id' => 'required',
                'statut' => 'required',
            ]);

            \DB::transaction(function () use ($request) {
                $order = new SaleReturn();

                $order->date = $request->date;
                $order->Ref = $this->getNumberOrder();
                $order->client_id = $request->client_id;
                $order->sale_id = $request->sale_id;
                $order->warehouse_id = $request->warehouse_id;
                $order->tax_rate = $request->tax_rate;
                $order->TaxNet = $request->TaxNet;
                $order->discount = $request->discount_value;
                $order->shipping = $request->shipping_value;
                $order->GrandTotal = $request->GrandTotal;
                $order->paid_amount = $request->GrandTotal;
                $order->statut = $request->statut;
                $order->payment_statut = 'paid';
                $order->notes = $request->notes;
                $order->user_id = Auth::user()->id;

                $order->save();

                $data = $request['details'];
                foreach ($data as $key => $value) {
                    $unit = Unit::where('id', $value['sale_unit_id'])->first();

                    $orderDetails[] = [
                        'sale_return_id' => $order->id,
                        'quantity' => $value['quantity'],
                        'price' => $value['Unit_price'],
                        'sale_unit_id' => $value['sale_unit_id'],
                        'TaxNet' => $value['tax_percent'],
                        'tax_method' => 'Exclusive',
                        'discount' => $value['discount'] ? $value['discount'] : 0,
                        'discount_method' => $value['discount_method'],
                        'product_id' => $value['product_id'],
                        'product_variant_id' => $value['product_variant_id'],
                        'total' => $value['subtotal'],
                        // 'imei_number' => $value['imei_number'],
                    ];

                    if ($order->statut == 'received') {
                        if ($value['product_variant_id'] !== null) {
                            $product_warehouse = ProductWarehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $order->warehouse_id)
                                ->where('product_id', $value['product_id'])
                                ->where('product_variant_id', $value['product_variant_id'])
                                ->first();

                            if ($unit && $product_warehouse) {
                                if ($unit->operator == '/') {
                                    $product_warehouse->qty += $value['quantity'] / $unit->operator_value;
                                } else {
                                    $product_warehouse->qty += $value['quantity'] * $unit->operator_value;
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
                                    $product_warehouse->qty += $value['quantity'] / $unit->operator_value;
                                } else {
                                    $product_warehouse->qty += $value['quantity'] * $unit->operator_value;
                                }

                                $product_warehouse->save();
                            }
                        }
                    }
                }
                SaleReturnDetails::insert($orderDetails);
                // paymnents
                $transaction = PaymentSaleReturns::create([
                    'user_id' => $order->user_id,
                    'date' => $order->date,
                    'Ref' => $this->getNumberOrderPayement(),
                    'sale_return_id' => $order->id,
                    'montant' => $order->GrandTotal,
                    'change' =>  0,
                    'Reglement' => 'cash',
                ]);
                // dd($request->all());
            }, 10);
            return redirect()->route('sale.return.index')->with('success', 'Sale created successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Sale Returns created failed');
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function show(Request $request, $id)
    {
        $Sale_Return = SaleReturn::with('sale', 'details.product.unitSale')
            ->where('deleted_at', '=', null)
            ->findOrFail($id);
        $details = [];
        $return_details['Ref'] = $Sale_Return->Ref;
        $return_details['sale_id'] = $Sale_Return->sale_id ? $Sale_Return['sale']->id : null;
        $return_details['sale_ref'] = $Sale_Return['sale'] ? $Sale_Return['sale']->Ref : '---';
        $return_details['date'] = $Sale_Return->date;
        $return_details['note'] = $Sale_Return->notes;
        $return_details['statut'] = $Sale_Return->statut;
        $return_details['discount'] = $Sale_Return->discount;
        $return_details['shipping'] = $Sale_Return->shipping;
        $return_details['tax_rate'] = $Sale_Return->tax_rate;
        $return_details['TaxNet'] = $Sale_Return->TaxNet;
        $return_details['client_name'] = $Sale_Return['client']->name;
        $return_details['client_phone'] = $Sale_Return['client']->phone;
        $return_details['client_adr'] = $Sale_Return['client']->adresse;
        $return_details['client_email'] = $Sale_Return['client']->email;
        $return_details['client_tax'] = $Sale_Return['client']->tax_number;
        $return_details['warehouse'] = $Sale_Return['warehouse']->name;
        $return_details['GrandTotal'] = number_format($Sale_Return->GrandTotal, 2, '.', '');
        $return_details['paid_amount'] = number_format($Sale_Return->paid_amount, 2, '.', '');
        $return_details['due'] = number_format($return_details['GrandTotal'] - $return_details['paid_amount'], 2, '.', '');
        $return_details['payment_status'] = $Sale_Return->payment_statut;

        foreach ($Sale_Return['details'] as $detail) {

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
                $data['name'] = '[' . $productsVariants->name . ']' . $detail['product']['name'];
            } else {
                $data['code'] = $detail['product']['code'];
                $data['name'] = $detail['product']['name'];
            }

            $data['quantity'] = $detail->quantity;
            $data['total'] = $detail->total;
            $data['price'] = $detail->price;
            $data['unit_sale'] = $unit ? $unit->ShortName : '';

            if ($detail->discount_method == 'discount') {
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
        //     'sale_Return' => $return_details,
        //     'company' => $company,
        // ]);
        return view(
            'templates.sale.return_show',
            [
                'details' => $details,
                'sale_Return' => $return_details,
                'company' => $company,
            ]
        );
    }
}
