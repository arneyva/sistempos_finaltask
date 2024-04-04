<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CurrencyController extends Controller
{
    public function index()
    {
        $currency = Currency::query()->latest()->paginate(1);

        return view('templates.settings.currency.index', [
            'currency' => $currency,
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
                'code' => [
                    'required',
                    Rule::unique(Currency::class, 'code')->whereNull('deleted_at'),
                ],
                'name' => [
                    'required',
                    Rule::unique(Currency::class, 'name')->whereNull('deleted_at'),
                ],
                'symbol' => [
                    'required',
                ],
            ]);
            $currency = Currency::create([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'symbol' => $validated['symbol'],
            ]);
            DB::commit();

            return redirect()->route('settings.currency.index')->with('success', 'Data Currency created successfully');
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
    // public function update(Request $request, string $id)
    // {
    //     $updateRules = $request->validate([
    //         'name' => [
    //             'required',
    //             Rule::unique(Warehouse::class, 'name')->whereNull('deleted_at')->ignore($id),
    //         ],
    //         'city' => [
    //             'required',
    //         ],
    //         'mobile' => [
    //             'required',
    //             Rule::unique(Warehouse::class, 'mobile')->whereNull('deleted_at')->ignore($id),
    //         ],
    //         'zip' => [
    //             'required',
    //         ],
    //         'email' => [
    //             'required',
    //             Rule::unique(Warehouse::class, 'email')->whereNull('deleted_at')->ignore($id),
    //         ],
    //         'country' => [
    //             'required',
    //         ],
    //     ]);

    //     $warehouses = Warehouse::where('id', $id)->update([
    //         'name' => $updateRules['name'],
    //         'city' => $updateRules['city'],
    //         'mobile' => $updateRules['mobile'],
    //         'zip' => $updateRules['zip'],
    //         'email' => $updateRules['email'],
    //         'country' => $updateRules['country'],
    //     ]);

    //     return redirect()->route('settings.warehouses.index')->with('success', 'Data Warehouse updated successfully');
    // }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $id)
    // {
    //     $warehouses = Warehouse::where('id', $id)->first();
    //     $warehouses->delete();
    //     ProductWarehouse::where('warehouse_id', $id)->update([
    //         'deleted_at' => Carbon::now(),
    //     ]);

    //     return redirect()->route('settings.warehouses.index')->with('success', 'Data Warehouse deleted successfully');
    // }
}
