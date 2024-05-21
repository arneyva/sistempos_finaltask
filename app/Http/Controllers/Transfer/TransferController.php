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
        request()->validate([
            'transfer.from_warehouse' => 'required',
            'transfer.to_warehouse' => 'required',
        ], [
            'transfer.from_warehouse.required' => 'Gudang asal harus dipilih.',
            'transfer.to_warehouse.required' => 'Gudang tujuan harus dipilih.',
        ]);
        // dd($request->all()); //ini dah jalan
        \DB::transaction(function () use ($request) {
            $order = new Transfer;

            $order->date = $request->transfer['date'];
            $order->Ref = $this->getNumbertransferValue();
            $order->from_warehouse_id = $request->transfer['from_warehouse'];
            $order->to_warehouse_id = $request->transfer['to_warehouse'];
            $order->items = count($request['details']);
            $order->tax_rate = $request->transfer['tax_rate'] ? $request->transfer['tax_rate'] : 0;
            $order->TaxNet = $request->transfer['TaxNet'] ? $request->transfer['TaxNet'] : 0;
            $order->discount = $request->transfer['discount'] ? $request->transfer['discount'] : 0;
            $order->shipping = $request->transfer['shipping'] ? $request->transfer['shipping'] : 0;
            $order->statut = $request->transfer['statut'];
            $order->notes = $request->transfer['notes'];
            $order->GrandTotal = $request['GrandTotal'];
            $order->user_id = Auth::user()->id;
            $order->save();

            $data = $request['details'];

            foreach ($data as $key => $value) {

                $unit = Unit::where('id', $value['purchase_unit_id'])->first();

                if ($request->transfer['statut'] == 'completed') {
                    if ($value['product_variant_id'] !== null) {

                        //--------- eliminate the quantity ''from_warehouse''--------------\\
                        $product_warehouse_from = ProductWarehouse::where('deleted_at', '=', null)
                            ->where('warehouse_id', $request->transfer['from_warehouse'])
                            ->where('product_id', $value['product_id'])
                            ->where('product_variant_id', $value['product_variant_id'])
                            ->first();

                        if ($unit && $product_warehouse_from) {
                            if ($unit->operator == '/') {
                                $product_warehouse_from->qty -= $value['quantity'] / $unit->operator_value;
                            } else {
                                $product_warehouse_from->qty -= $value['quantity'] * $unit->operator_value;
                            }
                            $product_warehouse_from->save();
                        }

                        //--------- ADD the quantity ''TO_warehouse''------------------\\
                        $product_warehouse_to = ProductWarehouse::where('deleted_at', '=', null)
                            ->where('warehouse_id', $request->transfer['to_warehouse'])
                            ->where('product_id', $value['product_id'])
                            ->where('product_variant_id', $value['product_variant_id'])
                            ->first();

                        if ($unit && $product_warehouse_to) {
                            if ($unit->operator == '/') {
                                $product_warehouse_to->qty += $value['quantity'] / $unit->operator_value;
                            } else {
                                $product_warehouse_to->qty += $value['quantity'] * $unit->operator_value;
                            }
                            $product_warehouse_to->save();
                        }
                    } else {

                        //--------- eliminate the quantity ''from_warehouse''--------------\\
                        $product_warehouse_from = ProductWarehouse::where('deleted_at', '=', null)
                            ->where('warehouse_id', $request->transfer['from_warehouse'])
                            ->where('product_id', $value['product_id'])->first();

                        if ($unit && $product_warehouse_from) {
                            if ($unit->operator == '/') {
                                $product_warehouse_from->qty -= $value['quantity'] / $unit->operator_value;
                            } else {
                                $product_warehouse_from->qty -= $value['quantity'] * $unit->operator_value;
                            }
                            $product_warehouse_from->save();
                        }

                        //--------- ADD the quantity ''TO_warehouse''------------------\\
                        $product_warehouse_to = ProductWarehouse::where('deleted_at', '=', null)
                            ->where('warehouse_id', $request->transfer['to_warehouse'])
                            ->where('product_id', $value['product_id'])->first();

                        if ($unit && $product_warehouse_to) {
                            if ($unit->operator == '/') {
                                $product_warehouse_to->qty += $value['quantity'] / $unit->operator_value;
                            } else {
                                $product_warehouse_to->qty += $value['quantity'] * $unit->operator_value;
                            }
                            $product_warehouse_to->save();
                        }
                    }
                } elseif ($request->transfer['statut'] == 'sent') {

                    if ($value['product_variant_id'] !== null) {

                        $product_warehouse_from = ProductWarehouse::where('deleted_at', '=', null)
                            ->where('warehouse_id', $request->transfer['from_warehouse'])
                            ->where('product_id', $value['product_id'])
                            ->where('product_variant_id', $value['product_variant_id'])
                            ->first();

                        if ($unit && $product_warehouse_from) {
                            if ($unit->operator == '/') {
                                $product_warehouse_from->qty -= $value['quantity'] / $unit->operator_value;
                            } else {
                                $product_warehouse_from->qty -= $value['quantity'] * $unit->operator_value;
                            }
                            $product_warehouse_from->save();
                        }
                    } else {

                        $product_warehouse_from = ProductWarehouse::where('deleted_at', '=', null)
                            ->where('warehouse_id', $request->transfer['from_warehouse'])
                            ->where('product_id', $value['product_id'])->first();

                        if ($unit && $product_warehouse_from) {
                            if ($unit->operator == '/') {
                                $product_warehouse_from->qty -= $value['quantity'] / $unit->operator_value;
                            } else {
                                $product_warehouse_from->qty -= $value['quantity'] * $unit->operator_value;
                            }
                            $product_warehouse_from->save();
                        }
                    }
                }

                $orderDetails['transfer_id'] = $order->id;
                $orderDetails['quantity'] = $value['quantity'];
                $orderDetails['purchase_unit_id'] = $value['purchase_unit_id'];
                $orderDetails['product_id'] = $value['product_id'];
                $orderDetails['product_variant_id'] = $value['product_variant_id'];
                $orderDetails['cost'] = $value['Unit_cost'];
                $orderDetails['TaxNet'] = $value['tax_percent'];
                $orderDetails['tax_method'] = $value['tax_method'];
                $orderDetails['discount'] = $value['discount'];
                $orderDetails['discount_method'] = $value['discount_Method'];
                $orderDetails['total'] = $value['subtotal'];

                TransferDetail::insert($orderDetails);
            }
        }, 10);

        return response()->json(['success' => true]);
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
