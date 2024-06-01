<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Membership;
use App\Models\PaymentSale;
use App\Models\ProductWarehouse;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Unit;
use App\Models\Warehouse;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sale = Sale::with('user', 'warehouse', 'client','paymentSales')->latest()->get();

        return view('templates.sale.index', [
            'sale' => $sale,
        ]);
    }

    public function shipments()
    {
        return view('templates.sale.shipments');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $warehouse = Warehouse::query()->get();
        $client = Client::query()->get();

        return view('templates.sale.create', ['warehouse' => $warehouse, 'client' => $client]);
    }
    //------------- Reference Number Order SALE -----------\\

    public function getNumberOrder()
    {

        $last = DB::table('sales')->latest('id')->first();

        if ($last) {
            $item = $last->Ref;
            $nwMsg = explode('_', $item);
            $inMsg = $nwMsg[1] + 1;
            $code = $nwMsg[0] . '_' . $inMsg;
        } else {
            // $code = 'SL_1111';
            $code = 'SL_1';
        }

        return $code;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        request()->validate([
            'client_id' => 'required',
            'warehouse_id' => 'required',
        ]);
        \DB::transaction(function () use ($request) {
            $order = new Sale();
            $order->is_pos = 0;
            $order->date = $request->date;
            $order->Ref = $this->getNumberOrder();
            $order->client_id = $request->client_id;
            $order->GrandTotal = $request->GrandTotal;
            $order->warehouse_id = $request->warehouse_id;
            $order->tax_rate = $request->tax_rate;
            $order->TaxNet = $request->TaxNet;
            $order->discount = $request->discount;
            $order->shipping = $request->shipping;
            $order->statut = $request->statut;
            $payment_method = $request->payment_method;
            if ($payment_method == 'cash') {
                $order->payment_statut = 'paid';
                $order->paid_amount = $request->GrandTotal;
            } else if ($payment_method == 'midtrans') {
                $order->payment_statut = 'unpaid';
            } else {
                $order->payment_statut = 'unpaid';
            }
            $order->notes = $request->notes;
            $order->user_id = Auth::user()->id;
            $order->save();
            $data = $request['details'];
            foreach ($data as $key => $value) {
                $unit = Unit::where('id', $value['sale_unit_id'])
                    ->first();
                $orderDetails[] = [
                    'date' => $request->date,
                    'sale_id' => $order->id,
                    'sale_unit_id' => $value['sale_unit_id'] ? $value['sale_unit_id'] : null,
                    'quantity' => $value['quantity'],
                    'price' => $value['Unit_price'],
                    'TaxNet' => $value['tax_percent'],
                    'tax_method' => $value['tax_method'],
                    'discount' => 0,
                    'discount_method' => 'nodiscount',
                    'product_id' => $value['product_id'],
                    'product_variant_id' => $value['product_variant_id'] ? $value['product_variant_id'] : null,
                    'total' => $value['subtotal'],
                    'imei_number' => $value['imei_number'] ?? null,
                ];

                if ($order->statut == 'completed') {
                    if ($value['product_variant_id'] !== null) {
                        $product_warehouse = ProductWarehouse::where('deleted_at', '=', null)
                            ->where('warehouse_id', $order->warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->where('product_variant_id', $value['product_variant_id'])
                            ->first();

                        if ($unit && $product_warehouse) {
                            if ($unit->operator == '/') {
                                $product_warehouse->qty -= $value['quantity'] / $unit->operator_value;
                            } else {
                                $product_warehouse->qty -= $value['quantity'] * $unit->operator_value;
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
                                $product_warehouse->qty -= $value['quantity'] / $unit->operator_value;
                            } else {
                                $product_warehouse->qty -= $value['quantity'] * $unit->operator_value;
                            }
                            $product_warehouse->save();
                        }
                    }
                }
            }
            SaleDetail::insert($orderDetails);
            if ($payment_method == 'midtrans') {
                $transaction = PaymentSale::create([
                    'user_id' => $order->user_id,
                    'date' => $order->date,
                    'Ref' => 'INV-' . $order->Ref,
                    'sale_id' => $order->id,
                    'montant' => $order->GrandTotal,
                    'change' => 0,
                    'Reglement' => 'midtrans',
                    'status' => 'pending',
                ]);
                // Set your Merchant Server Key
                \Midtrans\Config::$serverKey = config('midtrans.serverKey');
                // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
                \Midtrans\Config::$isProduction = false;
                // Set sanitization on (default)
                \Midtrans\Config::$isSanitized = true;
                // Set 3DS transaction for credit card to true
                \Midtrans\Config::$is3ds = true;
                $params = array(
                    'transaction_details' => array(
                        'order_id' => rand(),
                        'gross_amount' => $order->GrandTotal,
                    )
                );
                $snapToken = \Midtrans\Snap::getSnapToken($params);
                // dd($snapToken);
                $transaction->Reglement = $snapToken;
                $transaction->save();
            } else {
                $transaction = PaymentSale::create([
                    'user_id' => $order->user_id,
                    'date' => $order->date,
                    'Ref' => 'INV-' . $order->Ref,
                    'sale_id' => $order->id,
                    'montant' => $order->GrandTotal,
                    'change' => $request->change_return,
                    'Reglement' => 'cash',
                    'status' => 'success',
                ]);
            }
            //{{============= Integrasi Midtrans ===================}}\\
            $sale = Sale::findOrFail($order->id);

            $detail_sale = Sale::with('details')->find($order->id);

            //{{==================================================================}}\\
            //{{=============================== ROPIQ ==============================}}\\
            //{{==================================================================}}\\
            // Mengambil client_id dari sale
            $clientId = $sale->client_id;
            // Cek apakah client_id bukan default
            if ($clientId != 1) {
                // Mengambil client dari sale
                $client_sale = Client::find($clientId);
                if ($client_sale) {
                    //hitung harga bersih barang
                    if ($detail_sale) {
                        $total_spend = 0;
                        foreach ($detail_sale->details as $detail) {
                            $total_spend += $detail->total - $detail->TaxNet;
                        }
                    }

                    //ambil settingan membershgip
                    $membership_term = Membership::latest()->first();

                    $spend_every = $membership_term->spend_every;
                    $score_to_email = $membership_term->score_to_email;
                    $one_score_equal = $membership_term->one_score_equal;

                    //hitung score yang didapat berdasarkan settingan membership
                    $total_score = intdiv($total_spend, $spend_every);

                    // Menambahkan total_score ke client score
                    $client_sale->score += $total_score;

                    // Menyimpan perubahan pada client
                    $client_sale->save();
                }
            }
            //{{=========================================================================}}\\
            //{{==================================================================}}\\
            //{{==========================================================}}\\
        }, 10);

        return redirect()->route('sale.index')->with('success', 'Sale created successfully');
        // return response()->json(['success' => true]);
    }
    public function success($transactionId)
    {
        // Find the transaction by ID
        $transaction = PaymentSale::find($transactionId);
        if ($transaction) {
            // Update the transaction status to success
            $transaction->status = 'success';
            $transaction->save();

            // Update the associated sale's payment status
            $sale = Sale::find($transaction->sale_id);
            if ($sale) {
                $sale->payment_statut = 'paid';
                $sale->save();
            }

            return redirect()->route('sale.index')->with('success', 'Payment Sales successfully');
        } else {
            return redirect()->route('sale.index')->with('error', 'Transaction not found');
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
