<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WarehousesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $warehouses = Warehouse::query()->latest()->filter($request->query())
        // ->paginate($request->query('limit') ?? 10);
        // return view('university.index', [
        //     'university' => $university,
        // ]);
        $warehouses = Warehouse::query()->latest()->get();

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

        // return redirect('/settings/warehouses/index');
        return redirect()->route('settings.warehouses.index');
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
