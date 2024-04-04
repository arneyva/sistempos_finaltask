<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductWarehouse;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WarehousesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $warehouses = Warehouse::query()->latest()->paginate(1);

        return view('templates.settings.warehouses.index', [
            'warehouses' => $warehouses,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validate([
                'name' => [
                    'required',
                    Rule::unique(Warehouse::class, 'name')->whereNull('deleted_at'),
                ],
                'city' => [
                    'required',
                ],
                'mobile' => [
                    'required',
                    Rule::unique(Warehouse::class, 'mobile')->whereNull('deleted_at'),
                ],
                'zip' => [
                    'required',
                ],
                'email' => [
                    'required',
                    Rule::unique(Warehouse::class, 'email')->whereNull('deleted_at'),
                ],
                'country' => [
                    'required',
                ],
            ]);
            $warehouses = Warehouse::create([
                'name' => $validated['name'],
                'city' => $validated['city'],
                'mobile' => $validated['mobile'],
                'zip' => $validated['zip'],
                'email' => $validated['email'],
                'country' => $validated['country'],
            ]);

            $products = Product::where('deleted_at', '=', null)->get(['id', 'type']);
            if ($products) {
                foreach ($products as $products) {
                    $product_warehouse = [];
                    // handle product standart
                    $product_warehouse[] = [
                        'product_id' => $products->id,
                        'warehouse_id' => $warehouses->id,
                        'product_variant_id' => null,
                        'manage_stock' => 1,
                        'qte' => 0,
                    ];
                    ProductWarehouse::insert($product_warehouse);
                    DB::commit();
                }
            }

            // return redirect('/settings/warehouses/index');
            return redirect()->route('settings.warehouses.index')->with('success', 'Data Warehouses created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
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
        $updateRules = $request->validate([
            'name' => [
                'required',
                Rule::unique(Warehouse::class, 'name')->whereNull('deleted_at')->ignore($id),
            ],
            'city' => [
                'required',
            ],
            'mobile' => [
                'required',
                Rule::unique(Warehouse::class, 'mobile')->whereNull('deleted_at')->ignore($id),
            ],
            'zip' => [
                'required',
            ],
            'email' => [
                'required',
                Rule::unique(Warehouse::class, 'email')->whereNull('deleted_at')->ignore($id),
            ],
            'country' => [
                'required',
            ],
        ]);

        $warehouses = Warehouse::where('id', $id)->update([
            'name' => $updateRules['name'],
            'city' => $updateRules['city'],
            'mobile' => $updateRules['mobile'],
            'zip' => $updateRules['zip'],
            'email' => $updateRules['email'],
            'country' => $updateRules['country'],
        ]);

        return redirect()->route('settings.warehouses.index')->with('success', 'Data Warehouse updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $warehouses = Warehouse::where('id', $id)->first();
        $warehouses->delete();
        ProductWarehouse::where('warehouse_id', $id)->update([
            'deleted_at' => Carbon::now(),
        ]);

        return redirect()->route('settings.warehouses.index')->with('success', 'Data Warehouse deleted successfully');
    }
}
