<?php

namespace App\Http\Controllers;

use App\Models\OfficeShift;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManagerStatic as Image;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('role:superadmin');
    }

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
            'roles' => Role::all(),
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
            'roles' => Role::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $role = Role::find($request['role']);
        if ($role->name === 'superadmin') {
            $rules = [
                'username' => 'required|min:3|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'firstname' => 'required|max:12',
                'lastname' => 'required|max:12',
                'phone' => 'required|numeric|min_digits:12|max_digits:12',
                'gender' => 'required',
                'role' => 'required',
            ];

            $message = [
                'required' => 'Tidak boleh kosong!',
                'email' => 'Alamat email tidak valid!',
                'min' => 'Minimal :min karakter',
                'min_digits' => 'Nomor terdiri dari :min angka',
                'max' => 'Maksimal :max karakter',
                'max_digits' => 'Nomor terdiri dari :max angka',
                'unique' => ':attribute sudah terdaftar',
                'gender.required' => 'Pilih salah satu!',
                'role.required' => 'Pilih salah satu!',
            ];
        } else {
            $rules = [
                'username' => 'required|min:3|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'firstname' => 'required|max:12',
                'lastname' => 'required|max:12',
                'phone' => 'required|numeric|min_digits:12|max_digits:12',
                'gender' => 'required',
                'role' => 'required',
                'workLocation' => 'required',
            ];

            $message = [
                'required' => 'Tidak boleh kosong!',
                'email' => 'Alamat email tidak valid!',
                'min' => 'Minimal :min karakter',
                'min_digits' => 'Nomor terdiri dari :min angka',
                'max' => 'Maksimal :max karakter',
                'max_digits' => 'Nomor terdiri dari :max angka',
                'unique' => ':attribute sudah terdaftar',
                'gender.required' => 'Pilih salah satu!',
                'role.required' => 'Pilih salah satu!',
                'workLocation.required' => 'Pilih salah satu!',
            ];
        }

        $validateData = $request->validate($rules, $message);

        if ($request->input('avatar') !== null) {

            $avatarBase64 = $request->input('avatar');

            $avatarBinaryData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $avatarBase64));
            $filename = request('firstname').'_'.request('lastname').'_'.uniqid().'.png';

            $tempFilePath = public_path('/hopeui/html/assets/images/avatars/temp/'.$filename);
            file_put_contents($tempFilePath, $avatarBinaryData);

            $image_resize = Image::make($tempFilePath);
            $image_resize->resize(128, 128);
            $image_resize->save(public_path('/hopeui/html/assets/images/avatars/'.$filename));
            unlink($tempFilePath);
        } else {
            $filename = 'no_avatar.png';
        }

        $user = new User;
        $user->firstname = $request['firstname'];
        $user->lastname = $request['lastname'];
        $user->username = $request['username'];
        $user->email = $request['email'];
        $user->phone = $request['phone'];
        $user->gender = $request['gender'];
        $user->password = Hash::make($request['password']);
        $user->avatar = $filename;
        $user->status = 1;
        $user->save();

        $user->assignRole($role->name);
        if ($role->name === 'inventaris') {
            $user->warehouses()->attach(1);
            // Add additional warehouses if provided
            if (isset($request['outletAccess'])) {
                $user->warehouses()->attach($request['outletAccess']);
            }
        } elseif ($role->name === 'superadmin') {
            $user->warehouses()->sync(Warehouse::pluck('id')->toArray());
        } else {
            $user->warehouses()->sync($request['workLocation']);
        }

        return redirect()->route('people.users.index', ['orderBy' => 'newest'])->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('templates.usermanagement.user.edit', [
            'user' => User::findOrFail($id),
            'office_shifts' => OfficeShift::all(),
            'warehouses' => Warehouse::all(),
            'roles' => Role::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        if (! $user) {
            return back()->with('warning', 'User tidak ditemukan!');
        }

        $role = Role::find($request['role']);
        if ($role->name === 'superadmin') {
            $rules = [
                'username' => 'required|min:3|unique:users',
                'username' => Rule::unique('users')->ignore($id),
                'email' => 'required|email|unique:users',
                'email' => Rule::unique('users')->ignore($id),
                'firstname' => 'required|max:12',
                'lastname' => 'required|max:12',
                'phone' => 'required|numeric|min_digits:12|max_digits:12',
                'gender' => 'required',
                'role' => 'required',
            ];

            $message = [
                'required' => 'Tidak boleh kosong!',
                'email' => 'Alamat email tidak valid!',
                'min' => 'Minimal :min karakter',
                'min_digits' => 'Nomor terdiri dari :min angka',
                'max' => 'Maksimal :max karakter',
                'max_digits' => 'Nomor terdiri dari :max angka',
                'unique' => ':attribute sudah terdaftar',
                'gender.required' => 'Pilih salah satu!',
                'role.required' => 'Pilih salah satu!',
            ];
        } else {
            $rules = [
                'username' => 'required|min:3|unique:users',
                'username' => Rule::unique('users')->ignore($id),
                'email' => 'required|email|unique:users',
                'email' => Rule::unique('users')->ignore($id),
                'firstname' => 'required|max:12',
                'lastname' => 'required|max:12',
                'phone' => 'required|numeric|min_digits:12|max_digits:12',
                'gender' => 'required',
                'role' => 'required',
                'workLocation' => 'required',
            ];

            $message = [
                'required' => 'Tidak boleh kosong!',
                'email' => 'Alamat email tidak valid!',
                'min' => 'Minimal :min karakter',
                'min_digits' => 'Nomor terdiri dari :min angka',
                'max' => 'Maksimal :max karakter',
                'max_digits' => 'Nomor terdiri dari :max angka',
                'unique' => ':attribute sudah terdaftar',
                'gender.required' => 'Pilih salah satu!',
                'role.required' => 'Pilih salah satu!',
                'workLocation.required' => 'Pilih salah satu!',
            ];
        }

        $validateData = $request->validate($rules, $message);

        $current = $user->password;
        if ($request->NewPassword != 'null') {
            if ($request->NewPassword != $current) {
                $pass = Hash::make($request->NewPassword);
            } else {
                $pass = $user->password;
            }

        } else {
            $pass = $current;
        }

        $currentAvatar = $user->avatar;
        if ($request->avatar != null) {

            $avatarBase64 = $request->input('avatar');

            $avatarBinaryData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $avatarBase64));
            $filename = request('firstname').'_'.request('lastname').'_'.uniqid().'.png';

            $tempFilePath = public_path('/hopeui/html/assets/images/avatars/temp/'.$filename);
            file_put_contents($tempFilePath, $avatarBinaryData);

            $image_resize = Image::make($tempFilePath);
            $image_resize->resize(128, 128);
            $image_resize->save(public_path('/hopeui/html/assets/images/avatars/'.$filename));
            unlink($tempFilePath);

            $path = public_path('/hopeui/html/assets/images/avatars/');
            $currentPhotoPath = $path.$currentAvatar;
            if (file_exists($currentPhotoPath)) {
                if ($currentAvatar != 'no_avatar.png') {
                    @unlink($currentPhotoPath);
                }
            }
        } else {
            $filename = $currentAvatar;
        }

        User::whereId($id)->update([
            'firstname' => $request['firstname'],
            'lastname' => $request['lastname'],
            'username' => $request['username'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'gender' => $request['gender'],
            'password' => $pass,
            'avatar' => $filename,
        ]);

        $role = Role::find($request['role']);
        $user->assignRole($role->name);
        if ($role->name === 'inventaris') {
            // Add additional warehouses if provided
            if (isset($request['outletAccess'])) {
                $user->warehouses()->sync($request['outletAccess']);
            }
            $user->warehouses()->attach(1);
        } elseif ($role->name === 'superadmin') {
            $user->warehouses()->sync(Warehouse::pluck('id')->toArray());
        } else {
            $user->warehouses()->sync($request['workLocation']);
        }

        return back()->with('success', 'User berhasil diedit');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        if (! $user) {
            return back()->with('warning', 'User tidak ditemukan!');
        }

        /**
         * delete old image
         */
        $currentAvatar = $user->avatar;
        $path = public_path('/hopeui/html/assets/images/avatars/');
        $currentPhotoPath = $path.$currentAvatar;
        if (file_exists($currentPhotoPath)) {
            if ($currentAvatar != 'no_avatar.png') {
                @unlink($currentPhotoPath);
            }
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus');
    }
}
