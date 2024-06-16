<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Sale;
use App\Models\SaleReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function payments()
    {
        return view('templates.reports.payments');
    }

    public function profitLoss()
    {
        return view('templates.reports.profit-loss');
    }

    public function quantityAlerts()
    {
        return view('templates.reports.quantity-alerts');
    }

    public function stock()
    {
        return view('templates.reports.stock');
    }

    public function stockDetail($id)
    {
        return view('templates.reports.stock-detail');
    }

    // public function customers()
    // {
    //     return view('templates.reports.customers');
    // }
    //----------------- Customers Report -----------------------\\

    public function customers(Request $request)
    {
        $clientsQuery = Client::where('deleted_at', '=', null)->latest();
        if ($request->filled('search')) {
            $clientsQuery->where(function ($query) use ($request) {
                $query->orWhere('name', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('phone', 'like', '%' . $request->input('search') . '%');
            });
        }
        $clients = $clientsQuery->paginate($request->input('limit', 5))->appends($request->except('page'));
        $data = array();
        // Initialize totals
        $totalSales = 0;
        $totalAmount = 0;
        $totalPaid = 0;
        $totalDue = 0;
        $totalReturnDue = 0;
        foreach ($clients as $client) {
            $item['total_sales'] = DB::table('sales')
                ->where('deleted_at', '=', null)
                ->where('client_id', $client->id)
                ->count();

            $item['total_amount'] = DB::table('sales')
                ->where('deleted_at', '=', null)
                ->where('client_id', $client->id)
                ->sum('GrandTotal');

            $item['total_paid'] = DB::table('sales')
                ->where('sales.deleted_at', '=', null)
                ->where('sales.client_id', $client->id)
                ->sum('paid_amount');

            $item['due'] = $item['total_amount'] - $item['total_paid'];

            $item['total_amount_return'] = DB::table('sale_returns')
                ->where('deleted_at', '=', null)
                ->where('client_id', $client->id)
                ->sum('GrandTotal');

            $item['total_paid_return'] = DB::table('sale_returns')
                ->where('sale_returns.deleted_at', '=', null)
                ->where('sale_returns.client_id', $client->id)
                ->sum('paid_amount');

            $item['return_Due'] = $item['total_amount_return'] - $item['total_paid_return'];

            $item['name'] = $client->name;
            $item['phone'] = $client->phone;
            $item['code'] = $client->code;
            $item['id'] = $client->id;

            $data[] = $item;
            // Add to totals
            $totalSales += $item['total_sales'];
            $totalAmount += $item['total_amount'];
            $totalPaid += $item['total_paid'];
            $totalDue += $item['due'];
            $totalReturnDue += $item['return_Due'];
        }

        // return response()->json([
        //     'report' => $data,
        //     'total_sales' => $totalSales,
        //     'total_amount' => $totalAmount,
        //     'total_paid' => $totalPaid,
        //     'total_due' => $totalDue,
        //     'total_return_due' => $totalReturnDue,

        // ]);
        return view('templates.reports.customers', [
            'report' => $data,
            'clients' => $clients,
            'total_sales' => $totalSales,
            'total_amount' => $totalAmount,
            'total_paid' => $totalPaid,
            'total_due' => $totalDue,
            'total_return_due' => $totalReturnDue,
        ]);
    }
    public function customersDetailSales(Request $request, $id)
    {
        // Find the client or fail if not found
        $client = Client::where('deleted_at', '=', null)->findOrFail($id);

        // Calculate client-specific data
        $data['total_sales'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)->count();
        $data['total_amount'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)->sum('GrandTotal');
        $data['total_paid'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)->sum('paid_amount');
        $data['due'] = $data['total_amount'] - $data['total_paid'];


        // Retrieve sales data for the specific client
        $salesQuery = Sale::where('deleted_at', '=', null)->where('client_id', $id)->with('client', 'warehouse')->latest();
        if ($request->filled('search')) {
            $salesQuery->where(function ($query) use ($request) {
                $query->orWhere('Ref', 'like', '%' . $request->input('search') . '%');
                // ->orWhere('phone', 'like', '%' . $request->input('search') . '%');
            });
        }
        $sales =  $salesQuery->paginate($request->input('limit', 5))->appends($request->except('page'));
        $report = [];
        foreach ($sales as $sale) {
            $item = [
                'id' => $sale->id,
                'date' => $sale->date,
                'Ref' => $sale->Ref,
                'warehouse_name' => $sale->warehouse->name,
                'client_name' => $sale->client->name,
                'statut' => $sale->statut,
                'GrandTotal' => $sale->GrandTotal,
                'paid_amount' => $sale->paid_amount,
                'due' => $sale->GrandTotal - $sale->paid_amount,
                'payment_status' => $sale->payment_statut,
                'shipping_status' => $sale->shipping_status,
            ];

            $report[] = $item;
        }

        return view('templates.reports.customers.customers-detail-sales', [
            'client_data' => $data,
            'report' => $report,
            'sales' => $sales,
            'client' =>  $client,
        ]);
    }
    public function customersDetailReturns(Request $request, $id)
    {
        // Find the client or fail if not found
        $client = Client::where('deleted_at', '=', null)->findOrFail($id);

        // Calculate client-specific data
        $data['total_sales'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)->count();
        $data['total_amount'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)->sum('GrandTotal');
        $data['total_paid'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)->sum('paid_amount');
        $data['due'] = $data['total_amount'] - $data['total_paid'];
        // Retrieve sale returns data for the specific client
        $saleReturnsQuery = SaleReturn::where('deleted_at', '=', null)
            ->with('sale', 'client', 'warehouse')
            ->where('client_id', $id)
            ->when($request->filled('search'), function ($query) use ($request) {
                return $query->where(function ($query) use ($request) {
                    return $query->where('Ref', 'LIKE', "%{$request->search}%")
                        ->orWhere('statut', 'LIKE', "%{$request->search}%")
                        ->orWhere('payment_statut', 'LIKE', "%{$request->search}%")
                        ->orWhereHas('client', function ($q) use ($request) {
                            $q->where('name', 'LIKE', "%{$request->search}%");
                        })
                        ->orWhereHas('sale', function ($q) use ($request) {
                            $q->where('Ref', 'LIKE', "%{$request->search}%");
                        })
                        ->orWhereHas('warehouse', function ($q) use ($request) {
                            $q->where('name', 'LIKE', "%{$request->search}%");
                        });
                });
            });

        $saleReturns = $saleReturnsQuery->paginate($request->input('limit', 1))->appends($request->except('page'));
        $returnsCustomer = [];
        foreach ($saleReturns as $saleReturn) {
            $item = [
                'id' => $saleReturn->id,
                'Ref' => $saleReturn->Ref,
                'statut' => $saleReturn->statut,
                'client_name' => $saleReturn->client->name,
                'sale_ref' => $saleReturn->sale ? $saleReturn->sale->Ref : '---',
                'sale_id' => $saleReturn->sale ? $saleReturn->sale->id : null,
                'warehouse_name' => $saleReturn->warehouse->name,
                'GrandTotal' => $saleReturn->GrandTotal,
                'paid_amount' => $saleReturn->paid_amount,
                'due' => $saleReturn->GrandTotal - $saleReturn->paid_amount,
                'payment_status' => $saleReturn->payment_statut,
            ];

            $returnsCustomer[] = $item;
        }
        return view('templates.reports.customers.customers-detail-returns', [
            'client_data' => $data,
            'saleReturns' => $saleReturns,
            'returns_customer' => $returnsCustomer,
            'client' =>  $client,
        ]);
    }
    // public function customersDetailPayments(Request $request, $id)
    // {
    //     // Find the client or fail if not found
    //     $client = Client::where('deleted_at', '=', null)->findOrFail($id);

    //     // Calculate client-specific data
    //     $data['total_sales'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)->count();
    //     $data['total_amount'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)->sum('GrandTotal');
    //     $data['total_paid'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)->sum('paid_amount');
    //     $data['due'] = $data['total_amount'] - $data['total_paid'];

    //     $paymentsQuery = DB::table('payment_sales')
    //         ->where('payment_sales.deleted_at', '=', null)
    //         ->join('sales', 'payment_sales.sale_id', '=', 'sales.id')
    //         ->where('sales.client_id', $id)
    //         ->when($request->filled('search'), function ($query) use ($request) {
    //             return $query->where(function ($query) use ($request) {
    //                 return $query->where('payment_sales.Ref', 'LIKE', "%{$request->search}%")
    //                     ->orWhere('payment_sales.date', 'LIKE', "%{$request->search}%")
    //                     ->orWhere('payment_sales.Reglement', 'LIKE', "%{$request->search}%");
    //             });
    //         });

    //     $payments = $paymentsQuery->select(
    //         'payment_sales.date',
    //         'payment_sales.Ref AS Payment_Ref',
    //         'sales.Ref AS Sale_Ref',
    //         'payment_sales.Reglement',
    //         'payment_sales.montant'
    //     )->paginate($request->input('payments_page', 5), ['*'], 'payments_page')->appends($request->except('payments_page'));

    //     $paymentDetails = [];
    //     foreach ($payments as $payment) {
    //         $item = [
    //             'date' => $payment->date,
    //             'Payment_Ref' => $payment->Payment_Ref,
    //             'Sale_Ref' => $payment->Sale_Ref,
    //             'Reglement' => $payment->Reglement,
    //             'montant' => $payment->montant,
    //         ];

    //         $paymentDetails[] = $item;
    //     }

    //     return view('templates.reports.customers.customers-detail-payments', [
    //         'client_data' => $data,
    //         'payments' => $paymentDetails,
    //         'client' =>  $client,
    //     ]);
    // }
    public function customersDetailPayments(Request $request, $id)
    {
        // Find the client or fail if not found
        $client = Client::where('deleted_at', '=', null)->findOrFail($id);

        // Calculate client-specific data
        $data['total_sales'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)->count();
        $data['total_amount'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)->sum('GrandTotal');
        $data['total_paid'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)->sum('paid_amount');
        $data['due'] = $data['total_amount'] - $data['total_paid'];

        // Retrieve payment data for the specific client
        $paymentsQuery = DB::table('payment_sales')
            ->where('payment_sales.deleted_at', '=', null)
            ->join('sales', 'payment_sales.sale_id', '=', 'sales.id')
            ->where('sales.client_id', $id)
            ->when($request->filled('search'), function ($query) use ($request) {
                return $query->where(function ($query) use ($request) {
                    return $query->where('payment_sales.Ref', 'LIKE', "%{$request->search}%")
                        ->orWhere('payment_sales.date', 'LIKE', "%{$request->search}%")
                        ->orWhere('payment_sales.Reglement', 'LIKE', "%{$request->search}%");
                });
            });
            dd($paymentsQuery->get());
        $payments = $paymentsQuery->select(
            'payment_sales.date',
            'payment_sales.Ref AS Payment_Ref',
            'sales.Ref AS Sale_Ref',
            'payment_sales.Reglement',
            'payment_sales.montant'
        )->paginate($request->input('limit', 5))->appends($request->except('page'));

        $paymentDetails = [];
        foreach ($payments as $payment) {
            $item = [
                'date' => $payment->date,
                'Payment_Ref' => $payment->Payment_Ref,
                'Sale_Ref' => $payment->Sale_Ref,
                'Reglement' => $payment->Reglement,
                'montant' => $payment->montant,
            ];

            $paymentDetails[] = $item;
        }

        return view('templates.reports.customers.customers-detail-payments', [
            'client_data' => $data,
            'report' => $paymentDetails,
            'payments' => $payments,
            'client' =>  $client,
        ]);
    }

    public function supplier()
    {
        return view('templates.reports.supplier');
    }

    public function supplierDetail($id)
    {
        return view('templates.reports.supplier-detail');
    }

    public function topSellingProduct()
    {
        return view('templates.reports.top-selling-product');
    }

    public function warehouse()
    {
        return view('templates.reports.warehouse');
    }

    public function sale()
    {
        return view('templates.reports.sale');
    }

    public function purchase()
    {
        return view('templates.reports.purchase');
    }
}
