<?php

namespace App\Http\Controllers\Adjustment;

use App\Http\Controllers\Controller;
use App\Models\Adjustment;
use App\Models\AdjustmentDetail;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductWarehouse;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('templates.adjustment.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $warehouse = Warehouse::query()->get();

        return view('templates.adjustment.create', ['warehouse' => $warehouse]);
    }

    public function getNumberOrder()
    {

        $last = DB::table('adjustments')->latest('id')->first();

        if ($last) {
            // cari data ref dari id terakhir
            $item = $last->Ref;
            // dipisah berdasarkan (_) / underscor jadi 2 array
            $nwMsg = explode('_', $item);
            //array ke2 di tambah 1
            $inMsg = $nwMsg[1] + 1;
            // array item pertama ditambah dengan array item kedua
            $code = $nwMsg[0].'_'.$inMsg;
        } else {
            $code = 'AD_1'; // AD=pertama 1=kedua
        }

        return $code;
    }

    public function Products_by_Warehouse(request $request, $id)
    {
        $data = []; //menyimpan data array
        $product_warehouse_data = ProductWarehouse::with('warehouse', 'product', 'productVariant')
            ->where(function ($query) use ($request, $id) {
                return $query->where('warehouse_id', $id)
                    ->where('deleted_at', '=', null)
                    ->where(function ($query) use ($request) {
                        return $query->whereHas('product', function ($q) use ($request) {
                            if ($request->is_sale == '1') {
                                $q->where('not_selling', '=', 0);
                            }
                        });
                    }) //mencarai data product warehouse berdasarkan yang sudah dipilih di dropdown warehouse sebelumnya
                    ->where(function ($query) use ($request) {
                        if ($request->stock == '1' && $request->product_service == '1') {
                            return $query->where('qty', '>', 0)->orWhere('manage_stock', false);
                        } elseif ($request->stock == '1' && $request->product_service == '0') {
                            return $query->where('qty', '>', 0)->orWhere('manage_stock', true);
                        } else {
                            return $query->where('manage_stock', true);
                        }
                    });
            })->get();

        foreach ($product_warehouse_data as $product_warehouse) { //araay setelah dapat data product warehouse

            if ($product_warehouse->product_variant_id) { //jika memiliki data product_variant_id
                $item['product_variant_id'] = $product_warehouse->product_variant_id;
                $item['code'] = $product_warehouse['productVariant']->code; //code ngambil dari relasi productVariant
                $item['Variant'] = '['.$product_warehouse['productVariant']->name.']'.$product_warehouse['product']->name; //code ngambil dari relasi productVariant
                $item['name'] = '['.$product_warehouse['productVariant']->name.']'.$product_warehouse['product']->name; //code ngambil dari relasi productVariant
                $item['barcode'] = $product_warehouse['productVariant']->code; //code ngambil dari relasi productVariant

                $product_price = $product_warehouse['productVariant']->price; //code ngambil dari relasi productVariant
            } else { //jika tidak memiliki data product_variant_id
                $item['product_variant_id'] = null;
                $item['Variant'] = null;
                $item['code'] = $product_warehouse['product']->code;
                $item['name'] = $product_warehouse['product']->name;
                $item['barcode'] = $product_warehouse['product']->code;
                $product_price = $product_warehouse['product']->price;
            }

            $item['id'] = $product_warehouse->product_id;
            $item['product_type'] = $product_warehouse['product']->type;
            $item['Type_barcode'] = $product_warehouse['product']->Type_barcode;
            $firstimage = explode(',', $product_warehouse['product']->image);
            $item['image'] = $firstimage[0];

            if ($product_warehouse['product']['unitSale']) {

                if ($product_warehouse['product']['unitSale']->operator == '/') {
                    $item['qty_sale'] = $product_warehouse->qty * $product_warehouse['product']['unitSale']->operator_value;
                    $price = $product_price / $product_warehouse['product']['unitSale']->operator_value;
                } else {
                    $item['qty_sale'] = $product_warehouse->qty / $product_warehouse['product']['unitSale']->operator_value;
                    $price = $product_price * $product_warehouse['product']['unitSale']->operator_value;
                }
            } else {
                $item['qty_sale'] = $product_warehouse['product']->type != 'is_service' ? $product_warehouse->qty : '---';
                $price = $product_price;
            }

            if ($product_warehouse['product']['unitPurchase']) { //memeriksa apakah ada informasi tentang penjualan unit untuk produk di gudang.

                if ($product_warehouse['product']['unitPurchase']->operator == '/') {
                    $item['qty_purchase'] = round($product_warehouse->qty * $product_warehouse['product']['unitPurchase']->operator_value, 5);
                } else {
                    $item['qty_purchase'] = round($product_warehouse->qty / $product_warehouse['product']['unitPurchase']->operator_value, 5);
                }
            } else {
                $item['qty_purchase'] = $product_warehouse->qty;
            }

            $item['manage_stock'] = $product_warehouse->manage_stock;
            $item['qty'] = $product_warehouse['product']->type != 'is_service' ? $product_warehouse->qty : '---';
            $item['unitSale'] = $product_warehouse['product']['unitSale'] ? $product_warehouse['product']['unitSale']->ShortName : '';
            $item['unitPurchase'] = $product_warehouse['product']['unitPurchase'] ? $product_warehouse['product']['unitPurchase']->ShortName : '';

            if ($product_warehouse['product']->TaxNet !== 0.0) {
                //Exclusive
                if ($product_warehouse['product']->tax_method == '1') {
                    $tax_price = $price * $product_warehouse['product']->TaxNet / 100;
                    $item['Net_price'] = $price + $tax_price;
                    // Inxclusive
                } else {
                    $item['Net_price'] = $price;
                }
            } else {
                $item['Net_price'] = $price;
            }

            $data[] = $item;
        }

        return response()->json($data);
    }

    public function show_product_data($id, $variant_id)
    {

        $Product_data = Product::with('unit')
            ->where('id', $id)
            ->where('deleted_at', '=', null)
            ->first();

        $data = [];
        $item['id'] = $Product_data['id']; //id product
        $item['image'] = $Product_data['image'];
        $item['product_type'] = $Product_data['type']; //type product
        $item['Type_barcode'] = $Product_data['Type_barcode'];

        $item['unit_id'] = $Product_data['unit'] ? $Product_data['unit']->id : ''; //ngambil dari relasi unit
        $item['unit'] = $Product_data['unit'] ? $Product_data['unit']->ShortName : ''; //ngambil dari relasi unit

        $item['purchase_unit_id'] = $Product_data['unitPurchase'] ? $Product_data['unitPurchase']->id : '';
        $item['unitPurchase'] = $Product_data['unitPurchase'] ? $Product_data['unitPurchase']->ShortName : '';

        $item['sale_unit_id'] = $Product_data['unitSale'] ? $Product_data['unitSale']->id : '';
        $item['unitSale'] = $Product_data['unitSale'] ? $Product_data['unitSale']->ShortName : '';

        $item['tax_method'] = $Product_data['tax_method'];
        $item['tax_percent'] = $Product_data['TaxNet'];

        $item['is_imei'] = $Product_data['is_imei'];
        $item['not_selling'] = $Product_data['not_selling'];
        // $item['qty']         = $Product_data['warehouse']->qty;

        //product single
        if ($Product_data['type'] == 'is_single') {
            $product_price = $Product_data['price'];
            $product_cost = $Product_data['cost'];

            $item['code'] = $Product_data['code'];
            $item['name'] = $Product_data['name'];

            //product is_variant
        } elseif ($Product_data['type'] == 'is_variant') {

            $product_variant_data = ProductVariant::where('product_id', $id)
                ->where('id', $variant_id)->first();

            $product_price = $product_variant_data['price'];
            $product_cost = $product_variant_data['cost'];
            $item['code'] = $product_variant_data['code'];
            $item['name'] = '['.$product_variant_data['name'].']'.$Product_data['name'];

            //product is_service
        } else {

            $product_price = $Product_data['price'];
            $product_cost = 0;

            $item['code'] = $Product_data['code'];
            $item['name'] = $Product_data['name'];
        }

        //check if product has Unit sale
        if ($Product_data['unitSale']) {

            if ($Product_data['unitSale']->operator == '/') {
                $price = $product_price / $Product_data['unitSale']->operator_value;
            } else {
                $price = $product_price * $Product_data['unitSale']->operator_value;
            }
        } else {
            $price = $product_price;
        }

        //check if product has Unit Purchase

        if ($Product_data['unitPurchase']) {

            if ($Product_data['unitPurchase']->operator == '/') {
                $cost = $product_cost / $Product_data['unitPurchase']->operator_value;
            } else {
                $cost = $product_cost * $Product_data['unitPurchase']->operator_value;
            }
        } else {
            $cost = 0;
        }

        $item['Unit_cost'] = $cost;
        $item['fix_cost'] = $product_cost;
        $item['Unit_price'] = $price;
        $item['fix_price'] = $product_price;

        if ($Product_data->TaxNet !== 0.0) {
            //Exclusive
            if ($Product_data['tax_method'] == '1') {
                $tax_price = $price * $Product_data['TaxNet'] / 100;
                $tax_cost = $cost * $Product_data['TaxNet'] / 100;

                $item['Total_cost'] = $cost + $tax_cost;
                $item['Total_price'] = $price + $tax_price;
                $item['Net_cost'] = $cost;
                $item['Net_price'] = $price;
                $item['tax_price'] = $tax_price;
                $item['tax_cost'] = $tax_cost;

                // Inxclusive
            } else {
                $item['Total_cost'] = $cost;
                $item['Total_price'] = $price;
                $item['Net_cost'] = $cost / (($Product_data['TaxNet'] / 100) + 1);
                $item['Net_price'] = $price / (($Product_data['TaxNet'] / 100) + 1);
                $item['tax_cost'] = $item['Total_cost'] - $item['Net_cost'];
                $item['tax_price'] = $item['Total_price'] - $item['Net_price'];
            }
        } else {
            $item['Total_cost'] = $cost;
            $item['Total_price'] = $price;
            $item['Net_cost'] = $cost;
            $item['Net_price'] = $price;
            $item['tax_price'] = 0;
            $item['tax_cost'] = 0;
        }

        $data[] = $item;

        return response()->json($data[0]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // rules adjustment
            $adjustmentRules = $request->validate([
                'warehouse_id' => 'required',

            ]);
            DB::beginTransaction();
            $adjustmentValue = new Adjustment();
            $adjustmentValue->date = $request->date;
            $adjustmentValue->Ref = $this->getNumberOrder();
            $adjustmentValue->warehouse_id = $request->warehouse_id;
            $adjustmentValue->notes = $request->notes;
            $adjustmentValue->items = count($request['details'] ?? []); // Menggunakan null coalescing operator untuk menangani nilai null
            $adjustmentValue->user_id = auth()->user()->id;
            $adjustmentValue->save();

            $data = $request->details; // Decode JSON dari details
            foreach ($data as $value) { //milik adjustemnt detail
                $orderDetails[] = [
                    'adjustment_id' => $adjustmentValue->id,
                    'quantity' => $value['quantity'],
                    'product_id' => $value['product_id'],
                    'product_variant_id' => $value['product_variant_id'],
                    'type' => $value['type'],
                ];

                if ($value['type'] == 'add') { //milik adjustemnt detail
                    if ($value['product_variant_id'] !== null) { //milik adjustemnt detail
                        $product_warehouse = ProductWarehouse::where('deleted_at', '=', null) //mulai peoduct warehouse
                            ->where('warehouse_id', $adjustmentValue->warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->where('product_variant_id', $value['product_variant_id'])
                            ->first();

                        if ($product_warehouse) {
                            $product_warehouse->qty += $value['quantity'];
                            $product_warehouse->save();
                        }
                    } else {
                        $product_warehouse = ProductWarehouse::where('deleted_at', '=', null)
                            ->where('warehouse_id', $adjustmentValue->warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->first();

                        if ($product_warehouse) {
                            $product_warehouse->qty += $value['quantity'];
                            $product_warehouse->save();
                        }
                    }
                } else {
                    if ($value['product_variant_id'] !== null) { //milik adjustemnt detail
                        $product_warehouse = ProductWarehouse::where('deleted_at', '=', null) //mulai peoduct warehouse
                            ->where('warehouse_id', $adjustmentValue->warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->where('product_variant_id', $value['product_variant_id'])
                            ->first();

                        if ($product_warehouse) {
                            $product_warehouse->qty -= $value['quantity'];
                            $product_warehouse->save();
                        }
                    } else {
                        $product_warehouse = ProductWarehouse::where('deleted_at', '=', null)
                            ->where('warehouse_id', $adjustmentValue->warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->first();

                        if ($product_warehouse) {
                            $product_warehouse->qty -= $value['quantity'];
                            $product_warehouse->save();
                        }
                    }
                }
            }
            AdjustmentDetail::insert($orderDetails);
            DB::commit();

            return redirect()->route('adjustment.index')->with('success', 'Product created successfully');
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
