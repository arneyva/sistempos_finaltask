<?php

namespace App\Http\Controllers\Transfer;

use App\Exports\TransfersExport;
use App\Http\Controllers\Controller;
use App\Models\NotesTransfer;
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
        // proses filter data
        if ($request->filled('date')) {
            $transferQuery->whereDate('date', '=', $request->input('date'));
        }
        if ($request->filled('Ref')) {
            $transferQuery->where('Ref', 'like', '%' . $request->input('Ref') . '%');
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
        // menampilkan data sesuai filter dan dipaginasi
        $transfer = $transferQuery->paginate($request->input('limit', 5))->appends($request->except('page'));
        // mendapatkan warehouse berdasarkan hak akses user 
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
            $TransferQuery->where('Ref', 'like', '%' . $request->input('Ref') . '%');
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
            $code = $nwMsg[0] . '_' . $inMsg;
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
            // rules untuk validasi transfer
            request()->validate([
                'transfer.from_warehouse' => 'required',
                'transfer.to_warehouse' => 'required',
            ], [
                'transfer.from_warehouse.required' => 'Gudang asal harus dipilih.',
                'transfer.to_warehouse.required' => 'Gudang tujuan harus dipilih.',
            ]);
            // proses transaksi transfer
            \DB::transaction(function () use ($request) {
                $order = new Transfer;
                $order->date = $request->transfer['date'];
                $order->Ref = $this->getNumbertransferValue();
                $order->from_warehouse_id = $request->transfer['from_warehouse'];
                $order->to_warehouse_id = $request->transfer['to_warehouse'];
                $order->items = count($request['details']); // mengetahui jumlahnya berapa dari data detail transfer
                $order->tax_rate = $request->transfer['tax_rate'] ? $request->transfer['tax_rate'] : 0;
                $order->TaxNet = $request->transfer['TaxNet'] ? $request->transfer['TaxNet'] : 0;
                // $order->discount = $request->transfer['discount'] ? $request->transfer['discount'] : 0;
                // $order->shipping = $request->transfer['shipping'] ? $request->transfer['shipping'] : 0;
                $order->discount = $request->discount_value ? $request->discount_value : 0;
                $order->shipping = $request->shipping_value ? $request->shipping_value : 0;
                $order->statut = $request->transfer['statut'];
                // $order->notes = $request->transfer['notes'];
                $order->GrandTotal = $request['GrandTotal'];
                $order->user_id = Auth::user()->id;
                $order->save();
                // 
                NotesTransfer::create([
                    'transfer_id' => $order->id,
                    'user_id' => Auth::user()->id,
                    'note' => $request->transfer['notes'],
                ]);
                // proses penyimpanan detail transfer
                $data = $request['details'];
                foreach ($data as $key => $value) {
                    $unit = Unit::where('id', $value['purchase_unit_id'])->first();
                    if ($request->transfer['statut'] == 'completed') {
                        if ($value['product_variant_id'] !== null) {
                            //--------- menghapus quantity ''from_warehouse''--------------\\
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
                            //--------- menambah quantity ''TO_warehouse''------------------\\
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
                            //---------menghapus quantity ''from_warehouse''--------------\\
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

                            //--------- menambah quantity ''TO_warehouse''------------------\\
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
                    $orderDetails['Tax_method'] = 'Exclusive';
                    $orderDetails['discount'] = $value['discount'] ? $value['discount'] : 0;
                    $orderDetails['discount_method'] = $value['discount_method'];
                    $orderDetails['total'] = $value['subtotal'];
                    // memasukan data ke database
                    TransferDetail::insert($orderDetails);
                }
            }, 10);

            return redirect()->route('transfer.index')->with('success', 'Transfer created successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
            return redirect()->back()->with('error', 'Transfer created failed');
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
    public function edit(Request $request, $id)
    {
        $Transfer_data = Transfer::with('details.product.unit')
            ->where('deleted_at', '=', null)
            ->findOrFail($id);
        // Ambil semua catatan untuk transfer yang diberikan
        $notes = NotesTransfer::where('transfer_id', $id)
            ->with('user') // Memuat relasi pengguna
            ->get();

        // Format catatan dalam bentuk array yang mudah digunakan untuk tampilan
        $formattedNotes = $notes->map(function ($note) {
            return [
                'content' => $note->note,
                'created_at' => $note->created_at->format('m-d H:i'),
                'user' => $note->user->firstname, // Asumsi bahwa user memiliki atribut 'name'
                'avatar' => $note->user->avatar, // Asumsi bahwa user memiliki atribut 'name'
            ];
        });

        // Debug output untuk memastikan data yang benar
        // dd($formattedNotes);
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
        $transfer['Ref'] = $Transfer_data->Ref;
        $transfer['GrandTotal'] = $Transfer_data->GrandTotal;
        $transfer['statut'] = $Transfer_data->statut;
        $transfer['notes'] = $Transfer_data->notes;
        $transfer['date'] = $Transfer_data->date;

        $transfer['tax_rate'] = $Transfer_data->tax_rate;
        $transfer['TaxNet'] = $Transfer_data->TaxNet;
        $transfer['discount'] = $Transfer_data->discount;
        $transfer['shipping'] = $Transfer_data->shipping;

        $details = [];
        $detail_id = 0;
        foreach ($Transfer_data['details'] as $detail) {
            // Check if detail has purchase_unit_id or Null
            if ($detail->purchase_unit_id !== null) {
                $unit = Unit::where('id', $detail->purchase_unit_id)->first();
                $data['no_unit'] = 1;
                if ($unit->operator == '/') {
                    // $item['qty_product_purchase'] = floor($stock->qty * $Product_data['unitPurchase']->operator_value);
                } else {
                    // $item['qty_product_purchase'] = floor($stock->qty / $Product_data['unitPurchase']->operator_value);
                    $data['unitPurchaseOperatorValue'] = $unit['operator_value'];
                }
            } else {
                $product_unit_purchase_id = Product::with('unitPurchase')
                    ->where('id', $detail->product_id)
                    ->first();
                $unit = Unit::where('id', $product_unit_purchase_id['unitPurchase']->id)->first();
                $data['no_unit'] = 0;
            }
            // $data['quantity_discount'] = $detail->quantity_discount;
            // $data['discount_percentage'] = $detail->discount_percentage;
            // Fetch item_product based on product_variant_id
            if ($detail->product_variant_id) {
                $item_product = ProductWarehouse::where('product_id', $detail->product_id)
                    ->where('deleted_at', '=', null)
                    ->where('product_variant_id', $detail->product_variant_id)
                    ->where('warehouse_id', $Transfer_data->from_warehouse_id)
                    ->first();

                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)
                    ->first();

                $data['name'] = '[' . $productsVariants->name . ']' . $detail['product']['name'];
                $data['code'] = $productsVariants->code;
                $data['product_variant_id'] = $detail->product_variant_id;
                $quantity_discount =  $item_product->quantity_discount ?? 0;
                $discount_percentage =  $item_product->discount_percentage ?? 0;
            } else {
                $item_product = ProductWarehouse::where('product_id', $detail->product_id)
                    ->where('deleted_at', '=', null)
                    ->where('warehouse_id', $Transfer_data->from_warehouse_id)
                    ->where('product_variant_id', '=', null)
                    ->first();

                $data['product_variant_id'] = null;
                $data['code'] = $detail['product']['code'];
                $data['name'] = $detail['product']['name'];
                $quantity_discount =  $item_product->quantity_discount ?? 0;
                $discount_percentage =  $item_product->discount_percentage ?? 0;
            }

            // Calculate stock based on unit operator
            if ($unit && $unit->operator == '/') {
                $data['stock'] = floor($item_product ? $item_product->qty * $unit->operator_value : 0);
                $data['stock_sale'] = $item_product->qty;
            } elseif ($unit && $unit->operator == '*') {
                $data['stock'] = floor($item_product ? $item_product->qty / $unit->operator_value : 0);
                $data['stock_sale'] = $item_product->qty;
            } else {
                $data['stock'] = 0;
            }

            // Calculate initial stock in purchase unit by reversing the transfer quantity operation
            if ($unit && $unit->operator == '/') {
                $initial_stock_in_base_unit = floor(($data['stock'] + $detail->quantity) / $unit->operator_value);
            } elseif ($unit && $unit->operator == '*') {
                $initial_stock_in_base_unit = floor(($data['stock'] + $detail->quantity) * $unit->operator_value);
            } else {
                $initial_stock_in_base_unit = floor($data['stock'] + $detail->quantity);
            }

            // Convert initial stock from base unit to purchase unit
            if ($unit->operator == '*') {
                $data['initial_stock'] = floor($initial_stock_in_base_unit / $unit->operator_value);
            } elseif ($unit->operator == '/') {
                $data['initial_stock'] = floor($initial_stock_in_base_unit * $unit->operator_value);
            } else {
                $data['initial_stock'] = floor($initial_stock_in_base_unit);
            }

            // Assigning other data fields
            $data['id'] = $detail->id;
            $data['detail_id'] = $detail_id += 1;
            $data['quantity'] = $detail->quantity;
            $data['product_id'] = $detail->product_id;
            $data['etat'] = 'current';
            $data['qty_copy'] = $detail->quantity;
            $data['unitPurchase'] = $unit->ShortName;
            $data['purchase_unit_id'] = $unit->id;
            $data['total'] = $detail->total;
            $data['quantity_discount'] = $quantity_discount;
            $data['quantity_discount_init'] = $quantity_discount / $data['unitPurchaseOperatorValue'];
            // $data['quantity_discount_init'] = $detail->discount;
            $data['discount_percentage'] =  $discount_percentage;
            // $data['total'] = $detail->total;

            // Calculate discount and tax
            if ($detail->discount_method == 'discount') {
                $data['DiscountNet'] = $detail->discount;
            } else {
                $data['DiscountNet'] = $detail->cost * $detail->discount / 100;
            }
            // $tax_cost = $detail->TaxNet * (($detail->cost - $data['DiscountNet']) / 100);
            $tax_cost = $detail->TaxNet * ($detail->cost / 100);
            $data['Unit_cost'] = $detail->cost;
            $data['tax_percent'] = $detail->TaxNet;
            $data['tax_method'] = $detail->tax_method;
            $data['discount'] = $detail->discount;
            $data['discount_method'] = $detail->discount_method;

            // Calculate net cost and subtotal based on tax method
            if ($detail->tax_method == 'Exclusive') {
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



        $user_auth = auth()->user();
        if ($user_auth->hasAnyRole(['superadmin', 'inventaris'])) {
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
        } else {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
            $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
        }
        // return response()->json(['transfer' => $transfer, 'details' => $details, 'warehouse' => $warehouses]);
        return view('templates.transfer.edit', ['transfer' => $transfer, 'details' => $details, 'warehouse' => $warehouses, 'formattedNotes' => $formattedNotes]);
    }
    public function editForStaff(Request $request, $id)
    {
        // Ambil semua catatan untuk transfer yang diberikan
        $notes = NotesTransfer::where('transfer_id', $id)
            ->with('user') // Memuat relasi pengguna
            ->get();

        // Format catatan dalam bentuk array yang mudah digunakan untuk tampilan
        $formattedNotes = $notes->map(function ($note) {
            return [
                'content' => $note->note,
                'created_at' => $note->created_at->format('Y-m-d H:i:s'),
                'user' => $note->user->firstname, // Asumsi bahwa user memiliki atribut 'name'
                'avatar' => $note->user->avatar,
            ];
        });

        // Debug output untuk memastikan data yang benar
        // dd($formattedNotes);

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
        $transfer['Ref'] = $Transfer_data->Ref;
        $transfer['GrandTotal'] = $Transfer_data->GrandTotal;
        $transfer['statut'] = $Transfer_data->statut;
        $transfer['notes'] = $Transfer_data->notes;
        $transfer['date'] = $Transfer_data->date;

        $transfer['tax_rate'] = $Transfer_data->tax_rate;
        $transfer['TaxNet'] = $Transfer_data->TaxNet;
        $transfer['discount'] = $Transfer_data->discount;
        $transfer['shipping'] = $Transfer_data->shipping;

        $details = [];
        $detail_id = 0;
        foreach ($Transfer_data['details'] as $detail) {
            // Check if detail has purchase_unit_id or Null
            if ($detail->purchase_unit_id !== null) {
                $unit = Unit::where('id', $detail->purchase_unit_id)->first();
                $data['no_unit'] = 1;
                if ($unit->operator == '/') {
                    // $item['qty_product_purchase'] = floor($stock->qty * $Product_data['unitPurchase']->operator_value);
                } else {
                    // $item['qty_product_purchase'] = floor($stock->qty / $Product_data['unitPurchase']->operator_value);
                    $data['unitPurchaseOperatorValue'] = $unit['operator_value'];
                }
            } else {
                $product_unit_purchase_id = Product::with('unitPurchase')
                    ->where('id', $detail->product_id)
                    ->first();
                $unit = Unit::where('id', $product_unit_purchase_id['unitPurchase']->id)->first();
                $data['no_unit'] = 0;
            }
            // $data['quantity_discount'] = $detail->quantity_discount;
            // $data['discount_percentage'] = $detail->discount_percentage;
            // Fetch item_product based on product_variant_id
            if ($detail->product_variant_id) {
                $item_product = ProductWarehouse::where('product_id', $detail->product_id)
                    ->where('deleted_at', '=', null)
                    ->where('product_variant_id', $detail->product_variant_id)
                    ->where('warehouse_id', $Transfer_data->from_warehouse_id)
                    ->first();

                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)
                    ->first();

                $data['name'] = '[' . $productsVariants->name . ']' . $detail['product']['name'];
                $data['code'] = $productsVariants->code;
                $data['product_variant_id'] = $detail->product_variant_id;
                $quantity_discount =  $item_product->quantity_discount ?? 0;
                $discount_percentage =  $item_product->discount_percentage ?? 0;
            } else {
                $item_product = ProductWarehouse::where('product_id', $detail->product_id)
                    ->where('deleted_at', '=', null)
                    ->where('warehouse_id', $Transfer_data->from_warehouse_id)
                    ->where('product_variant_id', '=', null)
                    ->first();

                $data['product_variant_id'] = null;
                $data['code'] = $detail['product']['code'];
                $data['name'] = $detail['product']['name'];
                $quantity_discount =  $item_product->quantity_discount ?? 0;
                $discount_percentage =  $item_product->discount_percentage ?? 0;
            }

            // Calculate stock based on unit operator
            if ($unit && $unit->operator == '/') {
                $data['stock'] = floor($item_product ? $item_product->qty * $unit->operator_value : 0);
                $data['stock_sale'] = $item_product->qty;
            } elseif ($unit && $unit->operator == '*') {
                $data['stock'] = floor($item_product ? $item_product->qty / $unit->operator_value : 0);
                $data['stock_sale'] = $item_product->qty;
            } else {
                $data['stock'] = 0;
            }

            // Calculate initial stock in purchase unit by reversing the transfer quantity operation
            if ($unit && $unit->operator == '/') {
                $initial_stock_in_base_unit = floor(($data['stock'] + $detail->quantity) / $unit->operator_value);
            } elseif ($unit && $unit->operator == '*') {
                $initial_stock_in_base_unit = floor(($data['stock'] + $detail->quantity) * $unit->operator_value);
            } else {
                $initial_stock_in_base_unit = floor($data['stock'] + $detail->quantity);
            }

            // Convert initial stock from base unit to purchase unit
            if ($unit->operator == '*') {
                $data['initial_stock'] = floor($initial_stock_in_base_unit / $unit->operator_value);
            } elseif ($unit->operator == '/') {
                $data['initial_stock'] = floor($initial_stock_in_base_unit * $unit->operator_value);
            } else {
                $data['initial_stock'] = floor($initial_stock_in_base_unit);
            }

            // Assigning other data fields
            $data['id'] = $detail->id;
            $data['detail_id'] = $detail_id += 1;
            $data['quantity'] = $detail->quantity;
            $data['product_id'] = $detail->product_id;
            $data['etat'] = 'current';
            $data['qty_copy'] = $detail->quantity;
            $data['unitPurchase'] = $unit->ShortName;
            $data['purchase_unit_id'] = $unit->id;
            $data['total'] = $detail->total;
            $data['quantity_discount'] = $quantity_discount;
            $data['quantity_discount_init'] = $quantity_discount / $data['unitPurchaseOperatorValue'];
            // $data['quantity_discount_init'] = $detail->discount;
            $data['discount_percentage'] =  $discount_percentage;
            // $data['total'] = $detail->total;

            // Calculate discount and tax
            if ($detail->discount_method == 'discount') {
                $data['DiscountNet'] = $detail->discount;
            } else {
                $data['DiscountNet'] = $detail->cost * $detail->discount / 100;
            }
            // $tax_cost = $detail->TaxNet * (($detail->cost - $data['DiscountNet']) / 100);
            $tax_cost = $detail->TaxNet * ($detail->cost / 100);
            $data['Unit_cost'] = $detail->cost;
            $data['tax_percent'] = $detail->TaxNet;
            $data['tax_method'] = $detail->tax_method;
            $data['discount'] = $detail->discount;
            $data['discount_method'] = $detail->discount_method;

            // Calculate net cost and subtotal based on tax method
            if ($detail->tax_method == 'Exclusive') {
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



        $user_auth = auth()->user();
        if ($user_auth->hasAnyRole(['superadmin', 'inventaris'])) {
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
        } else {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
            $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
        }
        // return response()->json(['transfer' => $transfer, 'details' => $details, 'warehouse' => $warehouses]);
        return view('templates.transfer.confirm-staff', ['transfer' => $transfer, 'details' => $details, 'warehouse' => $warehouses, 'formattedNotes' => $formattedNotes]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
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
                $new_products_id = [];
                foreach ($data as $new_detail) {
                    $new_products_id[] = $new_detail['id'];
                }
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
                        if (!in_array($old_products_id[$key], $new_products_id)) {
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
                        $TransDetail['tax_method'] = 'Exclusive';
                        $TransDetail['total'] = $product_detail['subtotal'];
                        $TransDetail['discount'] = $product_detail['discount'] ? $product_detail['discount'] : 0;
                        $TransDetail['discount_method'] = $product_detail['discount_method'] ? $product_detail['discount_method'] : 0;
                        if (!isset($product_detail['id']) || !in_array($product_detail['id'], $old_products_id)) {
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
                    // 'notes' => $Trans['notes'],
                    'statut' => $Trans['statut'],
                    'items' => count($request['details']),
                    'tax_rate' => $Trans['tax_rate'] ? $Trans['tax_rate'] : 0,
                    'TaxNet' => $Trans['TaxNet'] ? $Trans['TaxNet'] : 0,
                    'discount' => $Trans['discount_value'] ? $Trans['discount_value'] : 0,
                    'shipping' => $Trans['shipping_value'] ? $Trans['shipping_value'] : 0,
                    'GrandTotal' => $request['GrandTotal'],
                ]);
                // 
                NotesTransfer::create([
                    'transfer_id' => $current_Transfer->id,
                    'user_id' => Auth::user()->id,
                    'note' => $request->transfer['notes'],
                ]);
            }, 10);
            return redirect()->route('transfer.index')->with('success', 'Transfer updated successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Transfer updated failed');
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function updateStaff(Request $request, $id)
    {
        try {
            \DB::transaction(function () use ($request, $id) {

                $current_Transfer = Transfer::findOrFail($id);
                $Old_Details = TransferDetail::where('transfer_id', $id)->get();
                $data = $request['details'];
                $Trans = $request->transfer;
                $length = count($data);
                $new_products_id = [];
                foreach ($data as $new_detail) {
                    $new_products_id[] = $new_detail['id'];
                }
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
                        if (!in_array($old_products_id[$key], $new_products_id)) {
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
                        $TransDetail['tax_method'] = 'Exclusive';
                        $TransDetail['total'] = $product_detail['subtotal'];
                        $TransDetail['discount'] = $product_detail['discount'] ? $product_detail['discount'] : 0;
                        $TransDetail['discount_method'] = $product_detail['discount_method'] ? $product_detail['discount_method'] : 0;
                        if (!isset($product_detail['id']) || !in_array($product_detail['id'], $old_products_id)) {
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
                    // 'notes' => $Trans['notes'],
                    'statut' => $Trans['statut'],
                    'items' => count($request['details']),
                    'tax_rate' => $Trans['tax_rate'] ? $Trans['tax_rate'] : 0,
                    'TaxNet' => $Trans['TaxNet'] ? $Trans['TaxNet'] : 0,
                    'discount' => $Trans['discount_value'] ? $Trans['discount_value'] : 0,
                    'shipping' => $Trans['shipping_value'] ? $Trans['shipping_value'] : 0,
                    'GrandTotal' => $request['GrandTotal'],
                ]);
                // dd($request->all());
                NotesTransfer::create([
                    'transfer_id' => $current_Transfer->id,
                    'user_id' => Auth::user()->id,
                    'note' => $request->transfer['notes'],
                ]);
            }, 10);
            return redirect()->route('transfer.index')->with('success', 'Transfer updated successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
            return redirect()->back()->with('error', 'Transfer updated failed');
        }
    }
    public function updateForStaff(Request $request, $id)
    {
        \DB::transaction(function () use ($request, $id) {
            // Find the current transfer
            $current_Transfer = Transfer::findOrFail($id);
            // Check if the current status is "sent"
            if ($current_Transfer->statut != 'sent') {
                return redirect()->route('transfer.index')->with('error', 'Only sent transfers can be confirmed.');
            }
            // Get all details of the current transfer
            $transferDetails = TransferDetail::where('transfer_id', $id)->get();
            foreach ($transferDetails as $detail) {
                // Find the purchase unit for the detail
                if ($detail->purchase_unit_id !== null) {
                    $unit = Unit::where('id', $detail->purchase_unit_id)->first();
                } else {
                    $product_unit_purchase_id = Product::with('unitPurchase')
                        ->where('id', $detail->product_id)
                        ->first();
                    $unit = Unit::where('id', $product_unit_purchase_id->unitPurchase->id)->first();
                }
                // Update the stock in the to warehouse
                $warehouse_to = ProductWarehouse::where('deleted_at', '=', null)
                    ->where('warehouse_id', $current_Transfer->to_warehouse_id)
                    ->where('product_id', $detail->product_id);

                if ($detail->product_variant_id !== null) {
                    $warehouse_to->where('product_variant_id', $detail->product_variant_id);
                }
                $warehouse_to = $warehouse_to->first();
                if ($unit && $warehouse_to) {
                    if ($unit->operator == '/') {
                        $warehouse_to->qty += $detail->quantity / $unit->operator_value;
                    } else {
                        $warehouse_to->qty += $detail->quantity * $unit->operator_value;
                    }
                    $warehouse_to->save();
                }
            }
            // Update the transfer status to "completed"
            $current_Transfer->update(['statut' => 'completed']);
        }, 10);

        return redirect()->route('transfer.index')->with('success', 'Transfer confirmed successfully');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (Auth::user()->hasAnyRole(['superadmin', 'inventaris'])) {
            $transfer = Transfer::find($id);

            if (!$transfer) {
                return redirect()->back()->with('error', 'transfer not found.');
            }
            // handle untuk mencegah pernghapusan
            if ($transfer->from_warehouse()->exists()) {
                return redirect()->back()->with('error', 'transfer cannot be deleted because it is already used in another data.');
            }
            $transfer->delete();
            return redirect()->route('transfer.index')->with('success', 'adjustment deleted successfully');
        } else {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk melakukan tindakan ini');
        }
    }
}
