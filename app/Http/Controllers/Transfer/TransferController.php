<?php

namespace App\Http\Controllers\Transfer;

use App\Http\Controllers\Controller;
use App\Models\ProductWarehouse;
use App\Models\Transfer;
use App\Models\TransferDetail;
use App\Models\Unit;
use App\Models\UserWarehouse;
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
        $transfer = Transfer::with('from_warehouse', 'to_warehouse')->where('deleted_at', '=', null)->get();

        // dd($transfer);
        return view('templates.transfer.index', ['transfer' => $transfer]);
    }
    // public function index(request $request)
    // {
    //     // $this->authorizeForUser($request->user('api'), 'view', Transfer::class);
    //     // $role = Auth::user()->roles()->first();
    //     // $view_records = Role::findOrFail($role->id)->inRole('record_view');

    //     // // How many items do you want to display.
    //     // $perPage = $request->limit;
    //     // $pageStart = \Request::get('page', 1);
    //     // // Start displaying items from this number;
    //     // $offSet = ($pageStart * $perPage) - $perPage;
    //     // $order = $request->SortField;
    //     // $dir = $request->SortType;
    //     // $helpers = new helpers();
    //     // // Filter fields With Params to retrieve
    //     // $columns = array(0 => 'Ref', 1 => 'from_warehouse_id', 2 => 'to_warehouse_id', 3 => 'statut');
    //     // $param = array(0 => 'like', 1 => '=', 2 => '=', 3 => 'like');
    //     $data = array();

    //     // Check If User Has Permission View  All Records
    //     $transfers = Transfer::with('from_warehouse', 'to_warehouse')
    //         ->where('deleted_at', '=', null);
    //     // ->where(function ($query) use ($view_records) {
    //     //     if (!$view_records) {
    //     //         return $query->where('user_id', '=', Auth::user()->id);
    //     //     }
    //     // });

    //     //Multiple Filter
    //     // $Filtred = $helpers->filter($transfers, $columns, $param, $request)
    //     //     // Search With Multiple Param
    //     //     ->where(function ($query) use ($request) {
    //     //         return $query->when($request->filled('search'), function ($query) use ($request) {
    //     //             return $query->where('Ref', 'LIKE', "%{$request->search}%")
    //     //                 ->orWhere('statut', 'LIKE', "%{$request->search}%")
    //     //                 ->orWhere(function ($query) use ($request) {
    //     //                     return $query->whereHas('from_warehouse', function ($q) use ($request) {
    //     //                         $q->where('name', 'LIKE', "%{$request->search}%");
    //     //                     });
    //     //                 })
    //     //                 ->orWhere(function ($query) use ($request) {
    //     //                     return $query->whereHas('to_warehouse', function ($q) use ($request) {
    //     //                         $q->where('name', 'LIKE', "%{$request->search}%");
    //     //                     });
    //     //                 });
    //     //         });
    //     //     });

    //     // $totalRows = $Filtred->count();
    //     // if ($perPage == "-1") {
    //     //     $perPage = $totalRows;
    //     // }
    //     // $transfers = $Filtred->offset($offSet)
    //     //     ->limit($perPage)
    //     //     ->orderBy($order, $dir)
    //     //     ->get();

    //     foreach ($transfers as $transfer) {
    //         $item['id'] = $transfer->id;
    //         $item['date'] = $transfer->date;
    //         $item['Ref'] = $transfer->Ref;
    //         $item['from_warehouse'] = $transfer['from_warehouse']->name;
    //         $item['to_warehouse'] = $transfer['to_warehouse']->name;
    //         $item['GrandTotal'] = $transfer->GrandTotal;
    //         $item['items'] = $transfer->items;
    //         $item['statut'] = $transfer->statut;
    //         $data[] = $item;
    //     }

    //     //get warehouses assigned to user
    //     $user_auth = auth()->user();
    //     // if ($user_auth->is_all_warehouses) {
    //     //     $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
    //     // }
    //     // else {
    //     $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
    //     $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
    //     // }

    //     return response()->json([
    //         // 'totalRows' => $totalRows,
    //         'warehouses' => $warehouses,
    //         'transfers' => $data,
    //     ]);
    //     // return view('templates.transfer.index', [
    //     //     'warehouses' => $warehouses,
    //     //     'transfers' => $data,
    //     // ]);
    // }

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
                // $orderDetails['tax_method'] = $value['tax_method'];
                // $orderDetails['discount'] = $value['discount'];
                // $orderDetails['tax_method'] = $value['tax_method'];
                $orderDetails['discount'] = 0;
                $orderDetails['discount_method'] = 'nodiscount';
                $orderDetails['total'] = $value['subtotal'];

                TransferDetail::insert($orderDetails);
            }
        }, 10);

        return redirect()->route('transfer.index')->with('success', 'Transfer created successfully');
        // return response()->json(['success' => true]);
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
