<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $unit = Unit::query()->latest()->get();

        return view('templates.product.unit.index', [
            'unit' => $unit,
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
        $validated = $request->validate([
            'name' => [
                'required',
                Rule::unique('units')->whereNull('deleted_at'),
            ],
            'ShortName' => [
                'required',
                Rule::unique('units')->whereNull('deleted_at'),
            ],
        ]);
        if (! $request->base_unit) {
            $operator = '*';
            $operator_value = 1;
        } else {
            $operator = $request->operator;
            $operator_value = $request->operator_value;
        }
        Unit::create([
            'name' => $request['name'],
            'ShortName' => $request['ShortName'],
            'base_unit' => $request['base_unit'],
            'operator' => $operator,
            'operator_value' => $operator_value,
        ]);

        return redirect()->route('product.unit.index')->with('success', 'Unit created successfully');
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
        $unit = Unit::Where('id', $id);
        $unit->delete();

        return redirect()->route('product.unit.index')->with('success', 'Unit deleted successfully');
    }
}
