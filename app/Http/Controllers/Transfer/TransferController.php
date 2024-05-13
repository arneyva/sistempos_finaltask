<?php

namespace App\Http\Controllers\Transfer;

use App\Http\Controllers\Controller;
use App\Models\ProductWarehouse;
use App\Models\Transfer;
use App\Models\TransferDetail;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('templates.transfer.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $warehouses = Warehouse::where('deleted_at', '=', null)->get();

        return view('templates.transfer.create', ['warehouse' => $warehouses]);
    }

    public function getNumbertransferValue()
    {

        $last = DB::table('transfers')->latest('id')->first();

        if ($last) {
            $item = $last->Ref;
            $nwMsg = explode('_', $item);
            $inMsg = $nwMsg[1] + 1;
            $code = $nwMsg[0].'_'.$inMsg;
        } else {
            $code = 'TR_1';
        }

        return $code;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $transferRules = $request->validate([
                'transfer.from_warehouse_id' => 'required', //berbentuk array
                'transfer.to_warehouse_id' => 'required',
            ]);
            // create data transfer
            $transferValue = new Transfer();
            $transferValue->date = $request->transfer['date'];
            $transferValue->Ref = $this->getNumbertransferValue();
            $transferValue->from_warehouse_id = $request->transfer['from_warehouse_id'];
            $transferValue->to_warehouse_id = $request->transfer['to_warehouse_id'];
            $transferValue->items = count($request['details']);
            $transferValue->tax_rate = $request->transfer['tax_rate'] ? $request->transfer['tax_rate'] : 0;
            $transferValue->TaxNet = $request->transfer['TaxNet'] ? $request->transfer['TaxNet'] : 0;
            $transferValue->discount = $request->transfer['discount'] ? $request->transfer['discount'] : 0;
            $transferValue->shipping = $request->transfer['shipping'] ? $request->transfer['shipping'] : 0;
            $transferValue->statut = $request->transfer['statut'];
            $transferValue->notes = $request->transfer['notes'];
            $transferValue->GrandTotal = $request['GrandTotal'];
            $transferValue->GrandTotal = Auth::user()->id;
            $transferValue->save();

            $data = $request['details'];
            foreach ($data as $key => $value) {
                $unit = Unit::where('id', $value['purchase_unit_id'])->first();
                if ($request->transfer['statut'] === 'completed') {
                    if ($value['product_variant_id'] !== null) {
                        //--------- eliminate the quantity ''from_warehouse''--------------\\
                        $productWarehouseFrom = ProductWarehouse::where('deleted_at', '=', null)->where('warehouse_id', $request->transfer['from_warehouse_id'])->where('product_id', $value['product_id'])->where('product_variant_id', $value['product_variant_id'])->first();
                        if ($unit && $productWarehouseFrom) {
                            if ($unit->operater == '/') {
                                $productWarehouseFrom->qty -= $value['quantity'] / $unit->operater_value;
                            } else {
                                $productWarehouseFrom->qty -= $value['quantity'] * $unit->operater_value;
                            }
                            $productWarehouseFrom->save();
                        }
                        //--------- ADD the quantity ''TO_warehouse''------------------\\
                        $productWarehouseTo = ProductWarehouse::where('deleted_at', '=', null)->where('warehouse_id', $request->transfer['to_warehouse_id'])->where('product_id', $value['product_id'])->where('product_variant_id', $value['product_variant_id'])->first();
                        if ($unit && $productWarehouseTo) {
                            if ($unit->operator == '/') {
                                $productWarehouseTo->qty += $value['quantity'] / $unit->operater_value;
                            } else {
                                $productWarehouseTo->qty += $value['quantity'] * $unit->operater_value;
                            }
                            $productWarehouseTo->save();
                        }
                    } else {
                        //--------- eliminate the quantity ''from_warehouse''--------------\\
                        $productWarehouseFrom = ProductWarehouse::where('deleted_at', '=', null)->where('warehouse_id', $request->transfer['from_warehouse_id'])->where('product_id', $value['product_id'])->first();
                        if ($unit && $productWarehouseFrom) {
                            if ($unit->operater == '/') {
                                $productWarehouseFrom->qty -= $value['quantity'] / $unit->operater_value;
                            } else {
                                $productWarehouseFrom->qty -= $value['quantity'] * $unit->operater_value;
                            }
                            $productWarehouseFrom->save();
                        }
                        //--------- ADD the quantity ''TO_warehouse''------------------\\
                        $productWarehouseTo = ProductWarehouse::where('deleted_at', '=', null)->where('warehouse_id', $request->transfer['to_warehouse_id'])->where('product_id', $value['product_id'])->first();
                        if ($unit && $productWarehouseTo) {
                            if ($unit->operator == '/') {
                                $productWarehouseTo->qty += $value['quantity'] / $unit->operater_value;
                            } else {
                                $productWarehouseTo->qty += $value['quantity'] * $unit->operater_value;
                            }
                            $productWarehouseTo->save();
                        }
                    }
                } elseif ($request->transfer['statut'] === 'sent') {
                    if ($value['product_variant_id'] !== null) {
                        $productWarehouseFrom = ProductWarehouse::where('deleted_at', '=', null)->where('warehouse_id', $request->transfer['from_warehouse_id'])->where('product_id', $value['product_id'])->where('product_variant_id', $value['product_variant_id'])->first();
                        if ($unit && $productWarehouseFrom) {
                            if ($unit->operater == '/') {
                                $productWarehouseFrom->qty += $value['quantity'] / $unit->operater_value;
                            } else {
                                $productWarehouseFrom->qty += $value['quantity'] * $unit->operater_value;
                            }
                            $productWarehouseFrom->save();
                        }
                    } else {
                        //--------- eliminate the quantity ''from_warehouse''--------------\\
                        $productWarehouseFrom = ProductWarehouse::where('deleted_at', '=', null)->where('warehouse_id', $request->transfer['from_warehouse_id'])->where('product_id', $value['product_id'])->first();
                        if ($unit && $productWarehouseFrom) {
                            if ($unit->operater == '/') {
                                $productWarehouseFrom->qty += $value['quantity'] / $unit->operater_value;
                            } else {
                                $productWarehouseFrom->qty += $value['quantity'] * $unit->operater_value;
                            }
                            $productWarehouseFrom->save();
                        }
                    }
                }
                // save detail
                $transferDetails['transfer_id'] = $transferValue->id;
                $transferDetails['quantity'] = $value['quantity'];
                $transferDetails['purchase_unit_id'] = $value['purchase_unit_id'];
                $transferDetails['product_id'] = $value['product_id'];
                $transferDetails['product_variant_id'] = $value['product_variant_id'];
                $transferDetails['cost'] = $value['cost'];
                $transferDetails['TaxNet'] = $value['TaxNet'];
                $transferDetails['tax_method'] = $value['tax_method'];
                $orderDetails['discount'] = $value['discount'];
                $orderDetails['discount_method'] = $value['discount_method'];
                $orderDetails['total'] = $value['subtotal'];
                TransferDetail::insert($transferDetails);
                dd($transferValue);
                DB::commit();

                return redirect()->route('transfer.index')->with('success', 'Transfer created successfully');
            }
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
