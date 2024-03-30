<?php

namespace App\Http\Controllers\Adjustment;

use App\Http\Controllers\Controller;
use App\Models\Adjustment;
use App\Models\AdjustmentDetail;
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

    public function warehouse()
    {

        $data = Warehouse::where('name', 'LIKE', '%'.request('q').'%')->paginate(10);

        return response()->json($data);
    }

    public function getProductWarehouse($id)
    {
        $data = ProductWarehouse::with(['product', 'variant'])->where('warehouse_id', $id)->where('product_id', 'LIKE', '%'.request('q').'%')->paginate(10);

        return response()->json($data);
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // rules adjustment
            $adjustmentRules = $request->validate([
                'warehouse_id' => 'required',
            ]);
            $adjustmentValue = new Adjustment();
            $adjustmentValue->date = $request->date;
            $adjustmentValue->Ref = $this->getNumberOrder();
            $adjustmentValue->warehouse_id = $request->warehouse_id;
            $adjustmentValue->notes = $request->notes;
            $adjustmentValue->items = count($request['details']);
            $data = $request['details'];
            $i = 0;
            foreach ($data as $key => $value) {
                $orderDetails[] = [
                    'adjustment_id' => $adjustmentValue->id,
                    'quantity' => $value['quantity'],
                    'product_id' => $value['product_id'],
                    'product_variant_id' => $value['product_variant_id'],
                    'type' => $value['type'],
                ];

                if ($value['type'] == 'add') {
                    if ($value['product_variant_id'] !== null) {
                        $product_warehouse = ProductWarehouse::where('deleted_at', '=', null)
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
                    if ($value['product_variant_id'] !== null) {
                        $product_warehouse = ProductWarehouse::where('deleted_at', '=', null)
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
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
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
