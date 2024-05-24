<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\Client;
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
        $sale = Sale::with('user', 'warehouse', 'client')->latest()->get();

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
            $code = $nwMsg[0].'_'.$inMsg;
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

        // $this->authorizeForUser($request->user('api'), 'create', Sale::class);

        request()->validate([
            'client_id' => 'required',
            'warehouse_id' => 'required',
        ]);
        // dd($request->all());
        \DB::transaction(function () use ($request) {
            // $helpers = new helpers();
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
            $order->payment_statut = 'unpaid';
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

            // $role = Auth::user()->roles()->first();
            // $view_records = Role::findOrFail($role->id)->inRole('record_view');

            if ($request->payment['status'] != 'pending') {
                $sale = Sale::findOrFail($order->id);
                // Check If User Has Permission view All Records
                // if (!$view_records) {
                //     // Check If User->id === sale->id
                //     $this->authorizeForUser($request->user('api'), 'check_record', $sale);
                // }

                try {

                    $total_paid = $sale->paid_amount + $request['amount'];
                    $due = $sale->GrandTotal - $total_paid;

                    if ($due === 0.0 || $due < 0.0) {
                        $payment_statut = 'paid';
                    } elseif ($due != $sale->GrandTotal) {
                        $payment_statut = 'partial';
                    } elseif ($due == $sale->GrandTotal) {
                        $payment_statut = 'unpaid';
                    }

                    // if ($request['amount'] > 0) {
                    //     if ($request->payment['Reglement'] == 'credit card') {
                    //         $Client = Client::whereId($request->client_id)->first();
                    //         Stripe\Stripe::setApiKey(config('app.STRIPE_SECRET'));

                    //         // Check if the payment record exists
                    //         $PaymentWithCreditCard = PaymentWithCreditCard::where('customer_id', $request->client_id)->first();
                    //         if (! $PaymentWithCreditCard) {

                    //             // Create a new customer and charge the customer with a new credit card
                    //             $customer = \Stripe\Customer::create([
                    //                 'source' => $request->token,
                    //                 'email' => $Client->email,
                    //                 'name' => $Client->name,
                    //             ]);

                    //             // Charge the Customer instead of the card:
                    //             $charge = \Stripe\Charge::create([
                    //                 'amount' => $request['amount'] * 100,
                    //                 'currency' => 'usd',
                    //                 'customer' => $customer->id,
                    //             ]);
                    //             $PaymentCard['customer_stripe_id'] = $customer->id;

                    //             // Check if the payment record not exists
                    //         } else {

                    //             // Retrieve the customer ID and card ID
                    //             $customer_id = $PaymentWithCreditCard->customer_stripe_id;
                    //             $card_id = $request->card_id;

                    //             // Charge the customer with the new credit card or the selected card
                    //             if ($request->is_new_credit_card || $request->is_new_credit_card == 'true' || $request->is_new_credit_card === 1) {
                    //                 // Retrieve the customer
                    //                 $customer = \Stripe\Customer::retrieve($customer_id);

                    //                 // Create New Source
                    //                 $card = \Stripe\Customer::createSource(
                    //                     $customer_id,
                    //                     [
                    //                         'source' => $request->token,
                    //                     ]
                    //                 );

                    //                 $charge = \Stripe\Charge::create([
                    //                     'amount' => $request['amount'] * 100,
                    //                     'currency' => 'usd',
                    //                     'customer' => $customer_id,
                    //                     'source' => $card->id,
                    //                 ]);
                    //                 $PaymentCard['customer_stripe_id'] = $customer_id;
                    //             } else {
                    //                 $charge = \Stripe\Charge::create([
                    //                     'amount' => $request['amount'] * 100,
                    //                     'currency' => 'usd',
                    //                     'customer' => $customer_id,
                    //                     'source' => $card_id,
                    //                 ]);
                    //                 $PaymentCard['customer_stripe_id'] = $customer_id;
                    //             }
                    //         }

                    //         $PaymentSale = new PaymentSale();
                    //         $PaymentSale->sale_id = $order->id;
                    //         $PaymentSale->Ref = app('App\Http\Controllers\PaymentSalesController')->getNumberOrder();
                    //         $PaymentSale->date = Carbon::now();
                    //         $PaymentSale->Reglement = $request->payment['Reglement'];
                    //         $PaymentSale->montant = $request['amount'];
                    //         $PaymentSale->change = $request['change'];
                    //         $PaymentSale->notes = null;
                    //         $PaymentSale->user_id = Auth::user()->id;
                    //         $PaymentSale->save();

                    //         $sale->update([
                    //             'paid_amount' => $total_paid,
                    //             'payment_statut' => $payment_statut,
                    //         ]);

                    //         $PaymentCard['customer_id'] = $request->client_id;
                    //         $PaymentCard['payment_id'] = $PaymentSale->id;
                    //         $PaymentCard['charge_id'] = $charge->id;
                    //         PaymentWithCreditCard::create($PaymentCard);

                    //         // Paying Method Cash
                    //     } else {

                    //         PaymentSale::create([
                    //             'sale_id' => $order->id,
                    //             'Ref' => app('App\Http\Controllers\PaymentSalesController')->getNumberOrder(),
                    //             'date' => Carbon::now(),
                    //             'Reglement' => $request->payment['Reglement'],
                    //             'montant' => $request['amount'],
                    //             'change' => $request['change'],
                    //             'notes' => null,
                    //             'user_id' => Auth::user()->id,
                    //         ]);

                    //         $sale->update([
                    //             'paid_amount' => $total_paid,
                    //             'payment_statut' => $payment_statut,
                    //         ]);
                    //     }
                    // }
                } catch (Exception $e) {
                    return response()->json(['message' => $e->getMessage()], 500);
                }
            }
        }, 10);

        return redirect()->route('sale.index')->with('success', 'Sale created successfully');
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
