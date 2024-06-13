<?php

namespace App\Http\Controllers\Transfer;

use App\Exports\TransfersExport;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductWarehouse;
use App\Models\Transfer;
use App\Models\TransferDetail;
use App\Models\Unit;
use App\Models\UserWarehouse;
use App\Models\Warehouse;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user_auth = auth()->user();
        $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');
        if ($user_auth->hasRole(['superadmin', 'inventaris'])) {
            $transferQuery = Transfer::query()->with(['from_warehouse', 'to_warehouse', 'details'])->where('deleted_at', '=', null)->latest();
        } else {
            $transferQuery = Transfer::query()->with(['from_warehouse', 'to_warehouse', 'details'])->where('deleted_at', '=', null)->where('to_warehouse_id', $warehouses_id)->latest();
        }
        if ($request->filled('date')) {
            $transferQuery->whereDate('date', '=', $request->input('date'));
        }
        if ($request->filled('Ref')) {
            $transferQuery->where('Ref', 'like', '%'.$request->input('Ref').'%');
        }

        if ($request->filled('from_warehouse_id')) {
            $transferQuery->where('from_warehouse_id', '=', $request->input('from_warehouse_id'));
        }
        if ($request->filled('to_warehouse_id')) {
            $transferQuery->where('to_warehouse_id', '=', $request->input('to_warehouse_id'));
        }
        if ($request->filled('statut')) {
            $transferQuery->where('statut', '=', $request->input('statut'));
        }
        // dd($transferQuery);
        $transfer = $transferQuery->paginate($request->input('limit', 5))->appends($request->except('page'));

        if ($user_auth->hasAnyRole(['superadmin', 'inventaris'])) {
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
        } else {
            $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
        }

        return view('templates.transfer.index', ['transfer' => $transfer, 'warehouse' => $warehouses]);
    }

    public function export(Request $request)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "transfers_{$timestamp}.xlsx";

        return Excel::download(new TransfersExport($request), $filename);
    }

    public function exportToPDF(Request $request)
    {
        $user_auth = auth()->user();
        $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');
        if ($user_auth->hasRole(['superadmin', 'inventaris'])) {
            $TransferQuery = Transfer::query()->with(['from_warehouse', 'to_warehouse', 'details'])->where('deleted_at', '=', null)->latest();
        } else {
            $TransferQuery = Transfer::query()->with(['from_warehouse', 'to_warehouse', 'details'])->where('deleted_at', '=', null)->where('to_warehouse_id', $warehouses_id)->latest();
        }
        // Terapkan filter berdasarkan parameter yang diterima dari request
        if ($request->has('date') && $request->filled('date')) {
            $TransferQuery->whereDate('date', '=', $request->input('date'));
        }

        if ($request->has('Ref') && $request->filled('Ref')) {
            $TransferQuery->where('Ref', 'like', '%'.$request->input('Ref').'%');
        }

        if ($request->has('from_warehouse_id') && $request->filled('from_warehouse_id')) {
            $TransferQuery->where('from_warehouse_id', '=', $request->input('from_warehouse_id'));
        }

        if ($request->has('to_warehouse_id') && $request->filled('to_warehouse_id')) {
            $TransferQuery->where('to_warehouse_id', '=', $request->input('to_warehouse_id'));
        }

        if ($request->has('statut') && $request->filled('statut')) {
            $TransferQuery->where('statut', '=', $request->input('statut'));
        }

        // Lakukan sorting sesuai request jika diperlukan
        if ($request->has('SortField') && $request->has('SortType')) {
            $sortField = $request->input('SortField');
            $sortType = $request->input('SortType');
            $TransferQuery->orderBy($sortField, $sortType);
        }

        $transfers = $TransferQuery->get();

        // Generate PDF
        $pdf = Pdf::loadView('export.transfer', compact('transfers'));

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');

        return $pdf->download("transfers_{$timestamp}.pdf");
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
    public function edit(Request $request, $id)
    {
        $Transfer_data = Transfer::with('details.product.unit')
            ->where('deleted_at', '=', null)
            ->findOrFail($id);

        $details = [];
        if ($Transfer_data->from_warehouse_id) {
            if (Warehouse::where('id', $Transfer_data->from_warehouse_id)
                ->where('deleted_at', '=', null)
                ->first()
            ) {
                $transfer['from_warehouse'] = $Transfer_data->from_warehouse_id;
            } else {
                $transfer['from_warehouse'] = '';
            }
        } else {
            $transfer['from_warehouse'] = '';
        }

        if ($Transfer_data->to_warehouse_id) {
            if (Warehouse::where('id', $Transfer_data->to_warehouse_id)->where('deleted_at', '=', null)->first()) {
                $transfer['to_warehouse'] = $Transfer_data->to_warehouse_id;
            } else {
                $transfer['to_warehouse'] = '';
            }
        } else {
            $transfer['to_warehouse'] = '';
        }
        $transfer['id'] = $Transfer_data->id;
        $transfer['GrandTotal'] = $Transfer_data->GrandTotal;
        $transfer['statut'] = $Transfer_data->statut;
        $transfer['notes'] = $Transfer_data->notes;
        $transfer['date'] = $Transfer_data->date;
        $transfer['tax_rate'] = $Transfer_data->tax_rate;
        $transfer['TaxNet'] = $Transfer_data->TaxNet;
        $transfer['discount'] = $Transfer_data->discount;
        $transfer['shipping'] = $Transfer_data->shipping;

        $detail_id = 0;
        foreach ($Transfer_data['details'] as $detail) {
            //-------check if detail has purchase_unit_id Or Null
            if ($detail->purchase_unit_id !== null) {
                $unit = Unit::where('id', $detail->purchase_unit_id)->first();
                $data['no_unit'] = 1;
            } else {
                $product_unit_purchase_id = Product::with('unitPurchase')
                    ->where('id', $detail->product_id)
                    ->first();
                $unit = Unit::where('id', $product_unit_purchase_id['unitPurchase']->id)->first();
                $data['no_unit'] = 0;
            }

            if ($detail->product_variant_id) {
                $item_product = ProductWarehouse::where('product_id', $detail->product_id)
                    ->where('deleted_at', '=', null)
                    ->where('product_variant_id', $detail->product_variant_id)
                    ->where('warehouse_id', $Transfer_data->from_warehouse_id)
                    ->first();

                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)->first();

                $item_product ? $data['del'] = 0 : $data['del'] = 1;
                $data['name'] = '['.$productsVariants->name.']'.$detail['product']['name'];
                $data['code'] = $productsVariants->code;

                $data['product_variant_id'] = $detail->product_variant_id;

                if ($unit && $unit->operator == '/') {
                    $data['stock'] = $item_product ? $item_product->qty * $unit->operator_value : 0;
                } elseif ($unit && $unit->operator == '*') {
                    $data['stock'] = $item_product ? $item_product->qty / $unit->operator_value : 0;
                } else {
                    $data['stock'] = 0;
                }
                $data['unitPurchase'] = $detail['product']['unitPurchase']->ShortName;
            } else {
                $item_product = ProductWarehouse::where('product_id', $detail->product_id)
                    ->where('deleted_at', '=', null)->where('warehouse_id', $Transfer_data->from_warehouse_id)
                    ->where('product_variant_id', '=', null)->first();

                $item_product ? $data['del'] = 0 : $data['del'] = 1;
                $data['product_variant_id'] = null;
                $data['code'] = $detail['product']['code'];
                $data['name'] = $detail['product']['name'];

                if ($unit && $unit->operator == '/') {
                    $data['stock'] = $item_product ? $item_product->qty * $unit->operator_value : 0;
                } elseif ($unit && $unit->operator == '*') {
                    $data['stock'] = $item_product ? $item_product->qty / $unit->operator_value : 0;
                } else {
                    $data['stock'] = 0;
                }
            }

            $data['id'] = $detail->id;
            $data['detail_id'] = $detail_id += 1;
            $data['quantity'] = $detail->quantity;
            $data['product_id'] = $detail->product_id;
            $data['etat'] = 'current';
            $data['qty_copy'] = $detail->quantity;
            $data['unitPurchase'] = $unit->ShortName;
            $data['purchase_unit_id'] = $unit->id;

            if ($detail->discount_method == '2') {
                $data['DiscountNet'] = $detail->discount;
            } else {
                $data['DiscountNet'] = $detail->cost * $detail->discount / 100;
            }
            $tax_cost = $detail->TaxNet * (($detail->cost - $data['DiscountNet']) / 100);
            $data['Unit_cost'] = $detail->cost;
            $data['tax_percent'] = $detail->TaxNet;
            $data['tax_method'] = $detail->tax_method;
            $data['discount'] = $detail->discount;
            $data['discount_Method'] = $detail->discount_method;

            if ($detail->tax_method == '1') {
                $data['Net_cost'] = $detail->cost - $data['DiscountNet'];
                $data['taxe'] = $tax_cost;
                $data['subtotal'] = ($data['Net_cost'] * $data['quantity']) + ($tax_cost * $data['quantity']);
            } else {
                $data['Net_cost'] = ($detail->cost - $data['DiscountNet']) / (($detail->TaxNet / 100) + 1);
                $data['taxe'] = $detail->cost - $data['Net_cost'] - $data['DiscountNet'];
                $data['subtotal'] = ($data['Net_cost'] * $data['quantity']) + ($tax_cost * $data['quantity']);
            }
            $details[] = $data;
        }

        //get warehouses assigned to user
        $user_auth = auth()->user();
        // if ($user_auth->is_all_warehouses) {
        //     $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
        // } else {
        // }
        $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
        // $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
        $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);

        // return response()->json([
        //     'details' => $details,
        //     'transfer' => $transfer,
        //     'warehouses' => $warehouses,
        // ]);
        return view('templates.transfer.edit', ['transfer' => $transfer, 'details' => $details, 'warehouse' => $warehouses]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        request()->validate([
            'transfer.to_warehouse' => 'required',
            'transfer.from_warehouse' => 'required',
        ]);
        // dd($request->all());
        \DB::transaction(function () use ($request, $id) {

            $current_Transfer = Transfer::findOrFail($id);
            $Old_Details = TransferDetail::where('transfer_id', $id)->get();
            $data = $request['details'];
            $Trans = $request->transfer;
            $length = count($data);

            // Get Ids details
            $new_products_id = [];
            // dd($new_products_id); adjustment juga kosong
            foreach ($data as $new_detail) {
                $new_products_id[] = $new_detail['id'];
            }
            // dd($new_products_id);
            // dd($data);
            // Init Data with old Parametre
            $old_products_id = [];
            foreach ($Old_Details as $key => $value) {
                //check if detail has purchase_unit_id Or Null
                if ($value['purchase_unit_id'] !== null) {
                    $unit = Unit::where('id', $value['purchase_unit_id'])->first();
                } else {
                    $product_unit_purchase_id = Product::with('unitPurchase')
                        ->where('id', $value['product_id'])
                        ->first();
                    $unit = Unit::where('id', $product_unit_purchase_id['unitPurchase']->id)->first();
                }

                $old_products_id[] = $value->id;

                if ($value['purchase_unit_id'] !== null) {

                    if ($current_Transfer->statut == 'completed') {
                        if ($value['product_variant_id'] !== null) {

                            $warehouse_from_variant = ProductWarehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $current_Transfer->from_warehouse_id)
                                ->where('product_id', $value['product_id'])
                                ->where('product_variant_id', $value['product_variant_id'])
                                ->first();

                            if ($unit && $warehouse_from_variant) {
                                if ($unit->operator == '/') {
                                    $warehouse_from_variant->qty += $value['quantity'] / $unit->operator_value;
                                } else {
                                    $warehouse_from_variant->qty += $value['quantity'] * $unit->operator_value;
                                }
                                $warehouse_from_variant->save();
                            }

                            $warehouse_To_variant = ProductWarehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $current_Transfer->to_warehouse_id)
                                ->where('product_id', $value['product_id'])
                                ->where('product_variant_id', $value['product_variant_id'])
                                ->first();

                            if ($unit && $warehouse_To_variant) {
                                if ($unit->operator == '/') {
                                    $warehouse_To_variant->qty -= $value['quantity'] / $unit->operator_value;
                                } else {
                                    $warehouse_To_variant->qty -= $value['quantity'] * $unit->operator_value;
                                }
                                $warehouse_To_variant->save();
                            }
                        } else {
                            $warehouse_from = ProductWarehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $current_Transfer->from_warehouse_id)
                                ->where('product_id', $value['product_id'])->first();

                            if ($unit && $warehouse_from) {
                                if ($unit->operator == '/') {
                                    $warehouse_from->qty += $value['quantity'] / $unit->operator_value;
                                } else {
                                    $warehouse_from->qty += $value['quantity'] * $unit->operator_value;
                                }
                                $warehouse_from->save();
                            }

                            $warehouse_To = ProductWarehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $current_Transfer->to_warehouse_id)
                                ->where('product_id', $value['product_id'])->first();

                            if ($unit && $warehouse_To) {
                                if ($unit->operator == '/') {
                                    $warehouse_To->qty -= $value['quantity'] / $unit->operator_value;
                                } else {
                                    $warehouse_To->qty -= $value['quantity'] * $unit->operator_value;
                                }
                                $warehouse_To->save();
                            }
                        }
                    } elseif ($current_Transfer->statut == 'sent') {
                        if ($value['product_variant_id'] !== null) {

                            $Sent_variant_To = ProductWarehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $current_Transfer->from_warehouse_id)
                                ->where('product_id', $value['product_id'])
                                ->where('product_variant_id', $value['product_variant_id'])
                                ->first();

                            if ($unit && $Sent_variant_To) {
                                if ($unit->operator == '/') {
                                    $Sent_variant_To->qty += $value['quantity'] / $unit->operator_value;
                                } else {
                                    $Sent_variant_To->qty += $value['quantity'] * $unit->operator_value;
                                }
                                $Sent_variant_To->save();
                            }
                        } else {
                            $Sent_variant_From = ProductWarehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $current_Transfer->from_warehouse_id)
                                ->where('product_id', $value['product_id'])->first();

                            if ($unit && $Sent_variant_From) {
                                if ($unit->operator == '/') {
                                    $Sent_variant_From->qty += $value['quantity'] / $unit->operator_value;
                                } else {
                                    $Sent_variant_From->qty += $value['quantity'] * $unit->operator_value;
                                }
                                $Sent_variant_From->save();
                            }
                        }
                    }

                    // Delete Detail
                    if (! in_array($old_products_id[$key], $new_products_id)) {
                        $TransferDetail = TransferDetail::findOrFail($value->id);
                        $TransferDetail->delete();
                    }
                }
            }

            // Update Data with New request
            foreach ($data as $key => $product_detail) {

                if ($product_detail['no_unit'] !== 0) {
                    $unit = Unit::where('id', $product_detail['purchase_unit_id'])->first();
                    if ($Trans['statut'] == 'completed') {
                        if ($product_detail['product_variant_id'] !== null) {

                            //--------- eliminate the quantity ''from_warehouse''--------------\\
                            $product_warehouse_from = ProductWarehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $Trans['from_warehouse'])
                                ->where('product_id', $product_detail['product_id'])
                                ->where('product_variant_id', $product_detail['product_variant_id'])
                                ->first();

                            if ($unit && $product_warehouse_from) {
                                if ($unit->operator == '/') {
                                    $product_warehouse_from->qty -= $product_detail['quantity'] / $unit->operator_value;
                                } else {
                                    $product_warehouse_from->qty -= $product_detail['quantity'] * $unit->operator_value;
                                }
                                $product_warehouse_from->save();
                            }

                            //--------- ADD the quantity ''TO_warehouse''------------------\\
                            $product_warehouse_to = ProductWarehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $Trans['to_warehouse'])
                                ->where('product_id', $product_detail['product_id'])
                                ->where('product_variant_id', $product_detail['product_variant_id'])
                                ->first();

                            if ($unit && $product_warehouse_to) {
                                if ($unit->operator == '/') {
                                    $product_warehouse_to->qty += $product_detail['quantity'] / $unit->operator_value;
                                } else {
                                    $product_warehouse_to->qty += $product_detail['quantity'] * $unit->operator_value;
                                }
                                $product_warehouse_to->save();
                            }
                        } else {

                            //--------- eliminate the quantity ''from_warehouse''--------------\\
                            $product_warehouse_from = ProductWarehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $Trans['from_warehouse'])
                                ->where('product_id', $product_detail['product_id'])->first();

                            if ($unit && $product_warehouse_from) {
                                if ($unit->operator == '/') {
                                    $product_warehouse_from->qty -= $product_detail['quantity'] / $unit->operator_value;
                                } else {
                                    $product_warehouse_from->qty -= $product_detail['quantity'] * $unit->operator_value;
                                }
                                $product_warehouse_from->save();
                            }

                            //--------- ADD the quantity ''TO_warehouse''------------------\\
                            $product_warehouse_to = ProductWarehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $Trans['to_warehouse'])
                                ->where('product_id', $product_detail['product_id'])->first();

                            if ($unit && $product_warehouse_to) {
                                if ($unit->operator == '/') {
                                    $product_warehouse_to->qty += $product_detail['quantity'] / $unit->operator_value;
                                } else {
                                    $product_warehouse_to->qty += $product_detail['quantity'] * $unit->operator_value;
                                }
                                $product_warehouse_to->save();
                            }
                        }
                    } elseif ($Trans['statut'] == 'sent') {

                        if ($product_detail['product_variant_id'] !== null) {

                            $product_warehouse_from = ProductWarehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $Trans['from_warehouse'])
                                ->where('product_id', $product_detail['product_id'])
                                ->where('product_variant_id', $product_detail['product_variant_id'])
                                ->first();

                            if ($unit && $product_warehouse_from) {
                                if ($unit->operator == '/') {
                                    $product_warehouse_from->qty -= $product_detail['quantity'] / $unit->operator_value;
                                } else {
                                    $product_warehouse_from->qty -= $product_detail['quantity'] * $unit->operator_value;
                                }
                                $product_warehouse_from->save();
                            }
                        } else {

                            $product_warehouse_from = ProductWarehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $Trans['from_warehouse'])
                                ->where('product_id', $product_detail['product_id'])->first();

                            if ($unit && $product_warehouse_from) {
                                if ($unit->operator == '/') {
                                    $product_warehouse_from->qty -= $product_detail['quantity'] / $unit->operator_value;
                                } else {
                                    $product_warehouse_from->qty -= $product_detail['quantity'] * $unit->operator_value;
                                }
                                $product_warehouse_from->save();
                            }
                        }
                    }

                    $TransDetail['transfer_id'] = $id;
                    $TransDetail['quantity'] = $product_detail['quantity'];
                    $TransDetail['purchase_unit_id'] = $product_detail['purchase_unit_id'];
                    $TransDetail['product_id'] = $product_detail['product_id'];
                    $TransDetail['product_variant_id'] = $product_detail['product_variant_id'];
                    $TransDetail['cost'] = $product_detail['Unit_cost'];
                    $TransDetail['TaxNet'] = $product_detail['tax_percent'];
                    // $TransDetail['tax_method'] = $product_detail['tax_method'];
                    // $TransDetail['discount'] = $product_detail['discount'];
                    // $TransDetail['discount_method'] = $product_detail['discount_Method'];
                    $TransDetail['total'] = $product_detail['subtotal'];

                    // if (!in_array($product_detail['id'], $old_products_id)) {
                    //     TransferDetail::Create($TransDetail);
                    // } else {
                    //     TransferDetail::where('id', $product_detail['id'])->update($TransDetail);
                    // }
                    if (! isset($product_detail['id']) || ! in_array($product_detail['id'], $old_products_id)) {
                        TransferDetail::create($TransDetail);
                    } else {
                        TransferDetail::where('id', $product_detail['id'])->update($TransDetail);
                    }
                }
            }

            $current_Transfer->update([
                'to_warehouse_id' => $Trans['to_warehouse'],
                'from_warehouse_id' => $Trans['from_warehouse'],
                'date' => $Trans['date'],
                'notes' => $Trans['notes'],
                'statut' => $Trans['statut'],
                'items' => count($request['details']),
                'tax_rate' => $Trans['tax_rate'] ? $Trans['tax_rate'] : 0,
                'TaxNet' => $Trans['TaxNet'] ? $Trans['TaxNet'] : 0,
                'discount' => $Trans['discount'] ? $Trans['discount'] : 0,
                'shipping' => $Trans['shipping'] ? $Trans['shipping'] : 0,
                'GrandTotal' => $request['GrandTotal'],
            ]);
        }, 10);

        // dd($request->all());
        // return response()->json(['success' => true]);
        return redirect()->route('transfer.index')->with('success', 'Transfer updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
