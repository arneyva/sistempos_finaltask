<?php

namespace App\Http\Controllers\hrm;

use App\Http\Controllers\Controller;
use App\Models\OfficeShift;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;

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

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //ambil user yang belum terhubung dengan jadwal
        $usersWithoutOfficeShift = User::whereDoesntHave('office_shifts')->get();

        return view('templates.hrm.shift.create', [
            'users' => $usersWithoutOfficeShift,
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

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

    }
}
