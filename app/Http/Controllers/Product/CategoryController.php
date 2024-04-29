<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $category = Category::query()->latest()->paginate(5);

        return view('templates.product.category.index', [
            'category' => $category,
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
                Rule::unique(Category::class, 'name')->whereNull('deleted_at'),
            ],
            'code' => [
                'required',
                Rule::unique(Category::class, 'code')->whereNull('deleted_at'),
            ],
        ]);
        $category = [
            'name' => $validated['name'],
            'code' => $validated['code'],
        ];
        Category::create($category);

        return redirect()->route('product.category.index')->with('success', 'Category created successfully');
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
                    Rule::unique(Category::class, 'name')->whereNull('deleted_at')->ignore($id),
                ],
                'code' => [
                    'required',
                    Rule::unique(Category::class, 'code')->whereNull('deleted_at')->ignore($id),
                ],
            ]);
            $newvalue = [
                'name' => $validated['name'],
                'code' => $validated['code'],
            ];
            Category::where('id', $id)->update($newvalue);

            return redirect()->route('product.category.index')->with('success', 'Category updated successfully');
        } else {
            return redirect()->back()->with('errorzz', 'You are not authorized to delete Category product');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (auth()->user()->hasAnyRole(['superadmin', 'inventaris'])) {
            $category = Category::where('id', $id);
            $category->delete();

            return redirect()->route('product.category.index')->with('success', 'Category deleted successfully');
        } else {
            return redirect()->back()->with('errorzz', 'You are not authorized to delete Category product');
        }
    }
}
