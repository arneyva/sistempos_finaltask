<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OfficeShift;
use Spatie\Permission\Models\Role;
use App\Models\ProductWarehouse;
use App\Models\Warehouse;
use App\Models\UserWarehouse;
use App\utils\helpers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManagerStatic as Image;
use \Nwidart\Modules\Facades\Module;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orderBy = 'firstname';
        $order = 'asc';

        switch (request('orderBy')) {
            case 'nameAsc':
                $orderBy = 'firstname';
                $order = 'asc';
                break;
            case 'nameDesc':
                $orderBy = 'lastname';
                $order = 'desc';
                break;
            case 'newest':
                $orderBy = 'created_at';
                $order = 'desc';
                break;
            case 'oldest':
                $orderBy = 'created_at';
                $order = 'asc';
                break;
            default:
                $orderBy = 'firstname';
                $order = 'asc';
                break;
        }

        $show = request('show') ?? '10';
        $with = ['warehouses', 'office_shifts'];
        $users = User::filter(request(['gender', 'status', 'office_shifts', 'warehouses', 'search']))->with($with)->orderBy($orderBy, $order)->paginate($show)->withQueryString();

        return view('templates.usermanagement.user.index', [
            'users' => $users,
            'allUsers' => User::all(),
            'office_shifts' => OfficeShift::all(),
            'warehouses' => Warehouse::all(),
            'roles' =>  Role::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('templates.usermanagement.user.create', [
            'users' => User::all(),
            'office_shifts' => OfficeShift::all(),
            'warehouses' => Warehouse::all(),
            'roles' =>  Role::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'firstname' => 'required|max:10',
            'lastname' => 'required|max:10',
            'username' => 'required|min:3|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'avatar' => 'nullable|file|image|max:1024',
            'phone' => 'required|numeric|min:10|max:20',
            'gender' => 'required',
            'role' => 'required'
        ];

        $message = [
            'required' => 'Tidak boleh kosong!',
            'email' => 'Alamat email tidak valid!',
            'min' => 'Minimal :min karakter',
            'max' => 'Maksimal :max karakter',
            'unique' => ':attribute sudah terdaftar',
            'image' => 'File hanya boleh gambar!',
            'avatar.max' => 'Maksimal :max kb',
            'gender.required' => 'Pilih salah satu!',
        ];
        $validateData = $request->validate($rules, $message);

        if ($request->hasFile('avatar')) {

            $image = $request->file('avatar');
            $filename = rand(11111111, 99999999) . $image->getClientOriginalName();

            $image_resize = Image::make($image->getRealPath());
            $image_resize->resize(128, 128);
            $image_resize->save(public_path('/hopeui/html/assets/images/avatars' . $filename));

        } else {
            $filename = 'no_avatar.png';
        }


        $User = new User;
        $User->firstname = $request['firstname'];
        $User->lastname  = $request['lastname'];
        $User->username  = $request['username'];
        $User->email     = $request['email'];
        $User->phone     = $request['phone'];
        $User->gender     = $request['gender'];
        $User->password  = Hash::make($request['password']);
        $User->avatar    = $filename;
        $User->status   = 1;
        $User->warehouses()->sync($request['workLocation']);
        $User->assignRole($request['role']);
        $User->save();

        return redirect()->route('people.users.index?orderBy=newest')->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
