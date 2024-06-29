<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Adjustment;
use App\Models\Product;
use App\Models\ProductWarehouse;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
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
                'google_maps' => [
                    'required',
                    'url',
                    'size:41',
                ],
                'address' => [
                    'required',
                ],
            ]);

            $gmaps = $request->input('google_maps');
            $expandedUrl = $this->expandUrl($gmaps);
            $coordinates = $this->extractCoordinates($expandedUrl);
            if (! $coordinates) {
                return back()->with('error', 'Invalid Google Maps URL, Use link from share feature in Google Maps');
            }

            $warehouses = Warehouse::create([
                'name' => $validated['name'],
                'city' => $validated['city'],
                'mobile' => $validated['mobile'],
                'zip' => $validated['zip'],
                'email' => $validated['email'],
                'country' => $validated['country'],
                'google_maps' => $validated['google_maps'],
                'address' => $validated['address'],
                'longitude' => $coordinates['longitude'],
                'latitude' => $coordinates['latitude'],
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
                        'qty' => 0,
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
            'google_maps' => [
                'required',
                'url',
                'size:41',
            ],
            'address' => [
                'required',
            ],
        ]);

        $gmaps = $request->input('google_maps');
        $expandedUrl = $this->expandUrl($gmaps);
        $coordinates = $this->extractCoordinates($expandedUrl);

        if (! $coordinates) {
            return redirect()->back()->with('error', 'Invalid Google Maps URL, Use link from share feature in Google Maps');
        }

        $warehouses = Warehouse::where('id', $id)->update([
            'name' => $updateRules['name'],
            'city' => $updateRules['city'],
            'mobile' => $updateRules['mobile'],
            'zip' => $updateRules['zip'],
            'email' => $updateRules['email'],
            'country' => $updateRules['country'],
            'google_maps' => $updateRules['google_maps'],
            'address' => $updateRules['address'],
            'longitude' => $coordinates['longitude'],
            'latitude' => $coordinates['latitude'],
        ]);

        return redirect()->route('settings.warehouses.index')->with('success', 'Data Warehouse updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $warehouses = Warehouse::where('id', $id)->first();
        $adjustment = Adjustment::where('warehouse_id', $id)->first();
        try {
            DB::beginTransaction();
            if ($warehouses && ! $adjustment) {
                $warehouses->delete();
                ProductWarehouse::where('warehouse_id', $id)->update([
                    'deleted_at' => Carbon::now(),
                ]);

                DB::commit();

                return redirect()->route('settings.warehouses.index')->with('success', 'Data Warehouse deleted successfully');
            } else {
                return redirect()->back()->with('error', 'Warehouse cannot be deleted because it is used in adjustments !!');
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Data Warehouse deleted failed');
        }
    }

    public function expandUrl($gmaps)
    {
        $ch = curl_init($gmaps);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $r = curl_exec($ch);

        if (preg_match('/Location: (?P<url>.*)/i', $r, $match))
        {
            return $this->expandUrl($match['url']);
        }

        return rtrim($gmaps);
    }

    private function extractCoordinates($gmaps)
    {
        $pattern = '/!3d([-+]?\d*\.?\d+)!4d([-+]?\d*\.?\d+)/';
        if (preg_match($pattern, $gmaps, $matches)) {
            $latitude = $matches[1];
            $longitude = $matches[2];
            return [
                'latitude' => $latitude,
                'longitude' => $longitude
            ];
        }
        return null;
    }
}
