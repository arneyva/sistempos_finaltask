<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $unitQuery = Unit::query()->where('deleted_at', '=', null)->latest();
        if ($request->filled('search')) {
            $search = $request->input('search');
            $unitQuery->where(function ($query) use ($search) {
                $query->where('ShortName', 'like', '%'.$search.'%')
                    ->orWhere('name', 'like', '%'.$search.'%');
            });
        }
        $unit = $unitQuery->paginate($request->input('limit', 5))->appends($request->except('page'));

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
        if (Auth::user()->hasAnyRole(['superadmin', 'inventaris'])) {
            $validated = $request->validate([
                'name' => [
                    'required',
                    Rule::unique('units')->whereNull('deleted_at')->ignore($id),
                ],
                'ShortName' => [
                    'required',
                    Rule::unique('units')->whereNull('deleted_at')->ignore($id),
                ],
            ]);
            if (! $request->base_unit) {
                $operator = '*';
                $operator_value = 1;
            } else {
                $operator = $request->operator;
                $operator_value = $request->operator_value;
            }
            Unit::Where('id', $id)->update([
                'name' => $request['name'],
                'ShortName' => $request['ShortName'],
                'base_unit' => $request['base_unit'],
                'operator' => $operator,
                'operator_value' => $operator_value,
            ]);

            return redirect()->route('product.unit.index')->with('success', 'Unit updated successfully');
        } else {
            return redirect()->back()->with('errorzz', 'You are not authorized to update product');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (Auth::user()->hasAnyRole(['superadmin', 'inventaris'])) {
            $unit = Unit::Where('id', $id)->first();
            $unit->delete();

            return redirect()->route('product.unit.index')->with('success', 'Unit deleted successfully');
        } else {
            return redirect()->back()->with('errorzz', 'You are not authorized to this actions');
        }
    }
}
