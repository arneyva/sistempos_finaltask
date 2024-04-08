<?php

namespace App\Http\Controllers\Transfer;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use Illuminate\Http\Request;
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
        return view('templates.transfer.create');
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
            $transferValue->items = count($request->transfer['details']);
            $transferValue->tax_rate = $request->transfer['tax_rate'] ? $request->transfer['tax_rate'] : 0;
            $transferValue->TaxNet = $request->transfer['TaxNet'] ? $request->transfer['TaxNet'] : 0;
            $transferValue->discount = $request->transfer['discount'] ? $request->transfer['discount'] : 0;
            $transferValue->shipping = $request->transfer['shipping'] ? $request->transfer['shipping'] : 0;
            $transferValue->statut = $request->transfer['statut'];
            $transferValue->notes = $request->transfer['notes'];
            $transferValue->GrandTotal = $request['GrandTotal'];
            $transferValue->save();

            // logik detail foreach
            $data = $request['details'];
        } catch (\Throwable $th) {
            //throw $th;
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
