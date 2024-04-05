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
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            $updateRules = $request->validate([
                'code' => [
                    'required',
                    Rule::unique(Currency::class, 'code')->whereNull('deleted_at')->ignore($id),
                ],
                'name' => [
                    'required',
                    Rule::unique(Currency::class, 'name')->whereNull('deleted_at')->ignore($id),
                ],
                'symbol' => [
                    'required',
                ],
            ]);

            $currency = Currency::where('id', $id)->update([
                'name' => $updateRules['name'],
                'code' => $updateRules['code'],
                'symbol' => $updateRules['symbol'],
            ]);
            DB::commit();

            return redirect()->route('settings.currency.index')->with('success', 'Data Warehouse updated successfully');
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $currency = Currency::where('id', $id)->first();
        $currency->delete();

        return redirect()->route('settings.currency.index')->with('success', 'Data Warehouse deleted successfully');
    }
}
