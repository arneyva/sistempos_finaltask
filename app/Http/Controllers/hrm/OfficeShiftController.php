<?php

namespace App\Http\Controllers\hrm;

use App\Http\Controllers\Controller;
use App\Models\OfficeShift;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OfficeShiftController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:superadmin');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view ('templates.hrm.shift.index', [
            'shifts' => OfficeShift::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('templates.hrm.shift.create', [
            'warehouses' => Warehouse::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request['monday'] == null && $request['tuesday'] == null && $request['wednesday'] == null && $request['thursday'] == null && $request['friday'] == null && $request['saturday'] == null && $request['sunday'] == null) {
            return back()->with('error', 'Isi hari Masuk');
        }

        $rules = [
            'location' => 'required',
            'name' => 'required|unique:office_shifts',
        ];
        $messages = [
            'required' => 'Tidak boleh kosong!',
            'unique' => ':attribute sudah terdaftar',
        ];

        $validateData = $request->validate($rules, $messages);

        $shift = new OfficeShift;
        $shift->name = $request['name'];
        if ($request['monday']) {
            request()->validate([
                'monday_in'           => 'required',
                'monday_out'     => 'required',
            ]);

            $shift->monday_in = $request['monday_in'];
            $shift->monday_out = $request['monday_out'];
        } else {
            $shift->monday_in = null;
            $shift->monday_out = null;
        }

        // Selasa
        if ($request['tuesday']) {
            request()->validate([
                'tuesday_in'           => 'required',
                'tuesday_out'     => 'required',
            ]);

            $shift->tuesday_in = $request['tuesday_in'];
            $shift->tuesday_out = $request['tuesday_out'];
        } else {
            $shift->tuesday_in = null;
            $shift->tuesday_out = null;
        }

        // Rabu
        if ($request['wednesday']) {
            request()->validate([
                'wednesday_in'           => 'required',
                'wednesday_out'     => 'required',
            ]);

            $shift->wednesday_in = $request['wednesday_in'];
            $shift->wednesday_out = $request['wednesday_out'];
        } else {
            $shift->wednesday_in = null;
            $shift->wednesday_out = null;
        }

        // Kamis
        if ($request['thursday']) {
            request()->validate([
                'thursday_in'           => 'required',
                'thursday_out'     => 'required',
            ]);

            $shift->thursday_in = $request['thursday_in'];
            $shift->thursday_out = $request['thursday_out'];
        } else {
            $shift->thursday_in = null;
            $shift->thursday_out = null;
        }

        // Jumat
        if ($request['friday']) {
            request()->validate([
                'friday_in'           => 'required',
                'friday_out'     => 'required',
            ]);

            $shift->friday_in = $request['friday_in'];
            $shift->friday_out = $request['friday_out'];
        } else {
            $shift->friday_in = null;
            $shift->friday_out = null;
        }

        // Sabtu
        if ($request['saturday']) {
            request()->validate([
                'saturday_in'           => 'required',
                'saturday_out'     => 'required',
            ]);

            $shift->saturday_in = $request['saturday_in'];
            $shift->saturday_out = $request['saturday_out'];
        } else {
            $shift->saturday_in = null;
            $shift->saturday_out = null;
        }

        // Minggu
        if ($request['sunday']) {
            request()->validate([
                'sunday_in'           => 'required',
                'sunday_out'     => 'required',
            ]);

            $shift->sunday_in = $request['sunday_in'];
            $shift->sunday_out = $request['sunday_out'];
        } else {
            $shift->sunday_in = null;
            $shift->sunday_out = null;
        }

        $shift->save();
        $shift->warehouses()->attach($request['location']);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $shift=OfficeShift::findOrFail($id);
        // Dapatkan user yang terhubung dengan office shift tersebut
        $usersAssociated = $shift->users;
        // Dapatkan daftar warehouse IDs yang terhubung dengan office shift tersebut
        $warehouseIds = $shift->warehouses->pluck('id');

        // Temukan user yang terhubung dengan warehouse tersebut
        $users = User::whereHas('warehouses', function ($query) use ($warehouseIds) {
            $query->whereIn('warehouses.id', $warehouseIds);
        })
        ->whereDoesntHave('office_shifts')->get();//filter user yang belum terhubung dengan office shift tersebut

        return view('templates.hrm.shift.edit', [
            'shift' => $shift,
            'users' => $users,
            'users_office_shift' => $usersAssociated,
            'warehouses' => Warehouse::all(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $shift=OfficeShift::findOrFail($id);
        if (! $shift) {
            return back()->with('warning', 'Shift tidak ditemukan!');
        }

        if ($request['monday'] == null && $request['tuesday'] == null && $request['wednesday'] == null && $request['thursday'] == null && $request['friday'] == null && $request['saturday'] == null && $request['sunday'] == null) {
            return back()->with('error', 'Isi hari Masuk');
        }

        $rules = [
            'location' => 'required',
            'name' => ['required', Rule::unique('office_shifts')->ignore($id)],
        ];
        $messages = [
            'required' => 'Tidak boleh kosong!',
            'unique' => ':attribute sudah terdaftar',
        ];

        $validateData = $request->validate($rules, $messages);

        $shift->update([
            'name' => $request['name']
        ]);

        // Senin
        if ($request['monday']) {
            request()->validate([
                'monday_in'           => 'required',
                'monday_out'     => 'required',
            ]);

            $shift->update([
                'monday_in' => $request['monday_in'],
                'monday_out' => $request['monday_out'],
            ]);
        } else {
            $shift->monday_in = null;
            $shift->monday_out = null;
        }

        // Selasa
        if ($request['tuesday']) {
            request()->validate([
                'tuesday_in'           => 'required',
                'tuesday_out'     => 'required',
            ]);
            $shift->update([
                'tuesday_in' => $request['tuesday_in'],
                'tuesday_out' => $request['tuesday_out'],
            ]);

        } else {
            $shift->tuesday_in = null;
            $shift->tuesday_out = null;
        }

        // Rabu
        if ($request['wednesday']) {
            request()->validate([
                'wednesday_in'           => 'required',
                'wednesday_out'     => 'required',
            ]);
            $shift->update([
                'wednesday_in' => $request['wednesday_in'],
                'wednesday_out' => $request['wednesday_out'],
            ]);
        } else {
            $shift->wednesday_in = null;
            $shift->wednesday_out = null;
        }

        // Kamis
        if ($request['thursday']) {
            request()->validate([
                'thursday_in'           => 'required',
                'thursday_out'     => 'required',
            ]);
            $shift->update([
                'thursday_in' => $request['thursday_in'],
                'thursday_out' => $request['thursday_out'],
            ]);

        } else {
            $shift->thursday_in = null;
            $shift->thursday_out = null;
        }

        // Jumat
        if ($request['friday']) {
            request()->validate([
                'friday_in'           => 'required',
                'friday_out'     => 'required',
            ]);
            $shift->update([
                'friday_in' => $request['friday_in'],
                'friday_out' => $request['friday_out'],
            ]);
        } else {
            $shift->friday_in = null;
            $shift->friday_out = null;
        }

        // Sabtu
        if ($request['saturday']) {
            request()->validate([
                'saturday_in'           => 'required',
                'saturday_out'     => 'required',
            ]);
            $shift->update([
                'saturday_in' => $request['saturday_in'],
                'saturday_out' => $request['saturday_out'],
            ]);
        } else {
            $shift->saturday_in = null;
            $shift->saturday_out = null;
        }

        // Minggu
        if ($request['sunday']) {
            request()->validate([
                'sunday_in'           => 'required',
                'sunday_out'     => 'required',
            ]);
            $shift->update([
                'sunday_in' => $request['sunday_in'],
                'sunday_out' => $request['sunday_out'],
            ]);
        } else {
            $shift->sunday_in = null;
            $shift->sunday_out = null;
        }

        if ($request->input('users') != null) {
            // Ambil array ID user dari request
            $usersToInput = json_decode($request->input('users'), true);
            $usersToInput = User::whereIn('id', $usersToInput)->get();
    
            foreach ($usersToInput as $user) {
                $user->office_shifts()->attach($id);
            }
        }

        if ($request->input('delete_users') != null) {
            // Ambil array ID user dari request
            $usersToDelete = json_decode($request->input('delete_users'), true);
            $usersToDelete = User::whereIn('id', $usersToDelete)->get();
    
            foreach ($usersToDelete as $user) {
                // Cek apakah user IDs terhubung dengan OfficeShift
                if ($user->office_shifts->contains($id)) {
                    // Hapus hubungan antara user dan OfficeShift
                    $user->office_shifts()->detach($id);
                }
            }
        }

        $shift->warehouses()->sync($request['location']);

        // Dapatkan user yang terhubung dengan office shift tersebut
        $usersAssociated = $shift->users;

        // Dapatkan daftar warehouse IDs yang terhubung dengan office shift tersebut
        $warehouseIds = $shift->warehouses->pluck('id')->toArray();

        if ($usersAssociated) {
            foreach ($usersAssociated as $user) {
                // Cek apakah user IDs terhubung dengan salah satu atau lebih warehouse
                $userWarehouseIds = $user->warehouses->pluck('id')->toArray();
    
                // Jika user tidak terhubung dengan salah satu warehouse dari OfficeShift, hapus hubungan
                $isConnected = false;
                foreach ($warehouseIds as $warehouseId) {
                    if (in_array($warehouseId, $userWarehouseIds)) {
                        $isConnected = true;
                        break;
                    }
                }
    
                if (! $isConnected) {
                    // Hapus hubungan antara user dan OfficeShift
                    $user->office_shifts()->detach($id);
                }
            }
        }

        return back()->with('success', 'Shift berhasil diedit');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

    }
}
