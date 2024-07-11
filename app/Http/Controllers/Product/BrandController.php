<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManagerStatic as Image;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $brandQuery = Brand::query()->where('deleted_at', '=', null)->latest();
        if ($request->filled('search')) {
            $search = $request->input('search');
            $brandQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        $brands = $brandQuery->paginate($request->input('limit', 10))->appends($request->except('page'));

        return view('templates.product.brand.index', [
            'brands' => $brands,
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
                Rule::unique(Brand::class, 'name')->whereNull('deleted_at'),
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
            'description' => 'nullable',
        ]);
        if ($request->input('avatar') !== null) {

            $avatarBase64 = $request->input('avatar');

            $avatarBinaryData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $avatarBase64));
            $filename = $request['name'] . '_' . uniqid() . '.png';

            $tempFilePath = public_path('/hopeui/html/assets/images/brands/temp/' . $filename);
            file_put_contents($tempFilePath, $avatarBinaryData);

            $image_resize = Image::make($tempFilePath);
            $image_resize->resize(128, 128);
            $image_resize->save(public_path('/hopeui/html/assets/images/brands/' . $filename));
            unlink($tempFilePath);
        } else {
            $filename = 'image.png';
        }

        $brands = Brand::create([
            'name' => $validated['name'],
            'image' => $filename,
            'description' => $validated['description'],
        ]);

        return redirect()->route('product.brand.index')->with('success', 'Brand created successfully');
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
            $brand = Brand::where('id', $id)
                ->where('deleted_at', '=', null)
                ->first();
            $validated = $request->validate([
                'name' => [
                    'required',
                    Rule::unique(Brand::class, 'name')->whereNull('deleted_at')->ignore($id),
                ],
                'image' => 'nullable|image|mimes:jpeg,png,jpg',
                'description' => 'nullable',
            ]);
            $currentAvatar = $brand->image;
            if ($request->avatar != null) {

                $avatarBase64 = $request->input('avatar');

                $avatarBinaryData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $avatarBase64));
                $filename = $request['name'] . '_' . uniqid() . '.png';

                $tempFilePath = public_path('/hopeui/html/assets/images/brands/temp/' . $filename);
                file_put_contents($tempFilePath, $avatarBinaryData);

                $image_resize = Image::make($tempFilePath);
                $image_resize->resize(128, 128);
                $image_resize->save(public_path('/hopeui/html/assets/images/brands/' . $filename));
                unlink($tempFilePath);

                $path = public_path('/hopeui/html/assets/images/brands/');
                $currentPhotoPath = $path . $currentAvatar;
                if (file_exists($currentPhotoPath)) {
                    if ($currentAvatar != 'image.png') {
                        @unlink($currentPhotoPath);
                    }
                }
            } else {
                $filename = $currentAvatar;
            }
            $newvalue = [
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'image' => $filename
            ];
            Brand::where('id', $id)->update($newvalue);

            return redirect()->route('product.brand.index')->with('success', 'Brand updated successfully');
        } else {
            return redirect()->back()->with('errorzz', 'You are not authorized to update Brand product');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (Auth::user()->hasAnyRole(['superadmin', 'inventaris'])) {
            $brands = Brand::where('id', $id)->first();

            // Cek apakah unit sudah digunakan di produk
            if ($brands->products()->exists()) {
                return redirect()->back()->with('error', 'Brand cannot be deleted because it is already used in a product.');
            }

            $brands->delete();
            return redirect()->route('product.brand.index')->with('success', 'Brand deleted successfully');
        } else {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk melakukan tindakan ini');
        }
    }
}
