<?php

namespace App\Http\Controllers\Sale;

use App\Exports\SalesExport;
use App\Exports\SalesReturnExport;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductWarehouse;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\SaleReturnDetails;
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
        if ($user_auth->hasRole(['superadmin', 'inventaris'])) {
            $salereturnQuery = SaleReturn::query()->with(['sale', 'facture', 'client', 'warehouse'])->where('deleted_at', '=', null)->latest();
        } else {
            $salereturnQuery = SaleReturn::query()->with(['sale', 'facture', 'client', 'warehouse'])->where('deleted_at', '=', null)->where('warehouse_id', $warehouses_id)->latest();
        }
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
        $salereturn =  $salereturnQuery->paginate($request->input('limit', 5))->appends($request->except('page'));
        if ($user_auth->hasAnyRole(['superadmin', 'inventaris'])) {
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
        } else {
            $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
        }
        $client = Client::where('deleted_at', '=', null)->get(['id', 'name']);
        return view('templates.sale.return_index', [
            'salereturn' => $salereturn,
            'warehouses' => $warehouses,
            'client' => $client
        ]);
    }
    public function create_sell_return(Request $request, $id)
    {
        $SaleReturn = Sale::with('details.product.unitSale')
            ->where('deleted_at', '=', null)
            ->findOrFail($id);

        $details = array();
        $Return_detail['client_id'] = $SaleReturn->client_id;
        $Return_detail['warehouse_id'] = $SaleReturn->warehouse_id;
        $Return_detail['sale_id'] = $SaleReturn->id;
        $Return_detail['sale_ref'] = $SaleReturn->Ref;
        $Return_detail['tax_rate'] = 0;
        $Return_detail['TaxNet'] = 0;
        $Return_detail['discount'] = 0;
        $Return_detail['shipping'] = 0;
        $Return_detail['statut'] = "received";
        $Return_detail['notes'] = "";

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
                } {
                    $unit = NULL;
                }

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
            }

            $data['id'] = $detail->id;
            $data['detail_id'] = $detail_id += 1;
            $data['product_type'] = $detail['product']['type'];
            $data['quantity'] = 0;
            $data['sale_quantity'] = $detail->quantity;
            $data['product_id'] = $detail->product_id;
            $data['unitSale'] = $unit ? $unit->ShortName : '';
            $data['sale_unit_id'] = $unit ? $unit->id : '';
            $data['is_imei'] = $detail['product']['is_imei'];
            $data['imei_number'] = $detail->imei_number;

            if ($detail->discount_method == 'nodiscount') {
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
            $nwMsg = explode("_", $item);
            $inMsg = $nwMsg[1] + 1;
            $code = $nwMsg[0] . '_' . $inMsg;
        } else {
            $code = 'RT_1111';
        }
        return $code;
    }
    public function store(request $request)
    {
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
            $order->discount = $request->discount;
            $order->shipping = $request->shipping;
            $order->GrandTotal = $request->GrandTotal;
            $order->statut = $request->statut;
            $order->payment_statut = 'unpaid';
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
                    'sale_unit_id' =>  $value['sale_unit_id'],
                    'TaxNet' => $value['tax_percent'],
                    'tax_method' => 'Exclusive',
                    'discount' => 0,
                    'discount_method' => 'nodiscount',
                    'product_id' => $value['product_id'],
                    'product_variant_id' => $value['product_variant_id'],
                    'total' => $value['subtotal'],
                    // 'imei_number' => $value['imei_number'],
                ];

                if ($order->statut == "received") {
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
        }, 10);
        return redirect()->route('sale.return_index')->with('success', 'Sale created successfully');
    }
}
