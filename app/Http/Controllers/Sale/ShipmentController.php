<?php

namespace App\Http\Controllers\Sale;

use App\Exports\ShipmentsExport;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Shipment;
use App\Models\UserWarehouse;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ShipmentController extends Controller
{
    public function store(Request $request)
    {
        request()->validate([
            'status' => 'required',
        ]);

        \DB::transaction(function () use ($request) {
            $shipment = Shipment::firstOrNew(['Ref' => $request['Ref']]);

            $shipment->user_id = Auth::user()->id;
            $shipment->sale_id = $request['sale_id'];
            $shipment->delivered_to = $request['delivered_to'];
            $shipment->shipping_address = $request['shipping_address'];
            $shipment->shipping_details = $request['shipping_details'];
            $shipment->status = $request['status'];
            $shipment->save();

            $sale = Sale::findOrFail($request['sale_id']);
            $sale->update([
                'shipping_status' => $request['status'],
            ]);
        }, 10);

        // return response()->json(['success' => true]);
        return redirect()->route('sale.shipments')->with('success', 'Shipment created successfully');
    }

    public function index(Request $request)
    {
        $user_auth = auth()->user();
        $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id');
        if ($user_auth->hasRole(['superadmin', 'inventaris'])) {
            $shipmentQuery = Shipment::query()->with(['sale', 'sale.client', 'sale.warehouse'])->where('deleted_at', '=', null)->latest();
        } else {
            $shipmentQuery = Shipment::with(['sale', 'sale.client', 'sale.warehouse'])
                ->whereNull('deleted_at')
                ->whereHas('sale', function ($query) use ($warehouses_id) {
                    $query->whereIn('warehouse_id', $warehouses_id);
                })
                ->latest();
        }
        if ($request->filled('date')) {
            $shipmentQuery->whereDate('date', '=', $request->input('date'));
        }
        if ($request->filled('Ref')) {
            $shipmentQuery->where('Ref', 'like', '%'.$request->input('Ref').'%');
        }

        if ($request->filled('warehouse_id')) {
            $warehouse_id = $request->input('warehouse_id');
            $shipmentQuery->whereHas('sale', function ($query) use ($warehouse_id) {
                $query->where('warehouse_id', '=', $warehouse_id);
            });
        }

        if ($request->filled('status')) {
            $shipmentQuery->where('status', '=', $request->input('status'));
        }
        $shipments = $shipmentQuery->paginate($request->input('limit', 5))->appends($request->except('page'));
        if ($user_auth->hasAnyRole(['superadmin', 'inventaris'])) {
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
        } else {
            $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
        }

        return view('templates.sale.shipments', [
            'shipments' => $shipments,
            'warehouse' => $warehouses,
        ]);
    }
    public function shipmentsExport(Request $request)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "shipments_{$timestamp}.xlsx";

        return Excel::download(new ShipmentsExport($request), $filename);
    }
}
