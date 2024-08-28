<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RequestAttendance;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;

class RequestAttendanceController extends Controller
{
    public function index()
    {
        // Dapatkan id dari user yang terautentikasi
        $userId = Auth::id();
        

        // Dapatkan user yang sedang diautentikasi
        $user = Auth::user();

        // Dapatkan nama role yang dimiliki oleh user
        $roles = $user->getRoleNames(); // Mengembalikan koleksi

        // Jika Anda hanya ingin menampilkan atau menggunakan role pertama
        $roleName = $roles->first();

        // Dapatkan array dari warehouse_id yang terkait dengan user yang terautentikasi
        $warehouseIds = Auth::user()->warehouses->pluck('id');

        if ($roleName === 'staff' || $roleName === 'inventaris') {
            $RequestAttendance= RequestAttendance::where('user_id', $userId)->get();
        } else {
            $RequestAttendance= RequestAttendance::all();
        }

        return view('templates.hrm.attendance.request.index', [
            'expenses' => $RequestAttendance
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(String $attd)
    {
        // Dapatkan array dari warehouse_id yang terkait dengan user yang terautentikasi
        $attds = Attendance::where('user_id', Auth::id())
        ->where(function ($query) {
            $query->where('status', 'absent')
                  ->orWhere('late_in', 'yes')
                  ->orWhere('late_out', 'yes');
        })
        ->get();
        // dd($attds);

        return view('templates.hrm.attendance.request.create', [
            'attd' => $attds,
            'attdId' => $attd,
        ]);
    }

    public function store(Request $request)
    {

        // Dapatkan user yang sedang diautentikasi
        $user = Auth::user();

        // Dapatkan nama role yang dimiliki oleh user
        $roles = $user->getRoleNames(); // Mengembalikan koleksi

        // Jika Anda hanya ingin menampilkan atau menggunakan role pertama
        $roleName = $roles->first();
            $rules = [
                'date' => 'required',
                'attendance_id' => 'required',
                'details' => 'required',
                'file_pendukung' => 'required',
            ];

            $message = [
                'required' => 'Tidak boleh kosong!',
            ];
        

        $validateData = $request->validate($rules, $message);

        $file = $request->file('file_pendukung');
        $path = public_path().'/hopeui/html/assets/files/attendance/request/';
        $filename = rand(11111111, 99999999).$file->getClientOriginalName();

        $file->move(public_path('/hopeui/html/assets/files/attendance/request/'), $filename);


        $attd = new RequestAttendance;
        $attd->date = $request->date;
        $attd->user_id = Auth::user()->id;
        $attd->attendance_id = $request->attendance_id;
        $attd->details = $request->details;
        $attd->file_pendukung = $filename;
        $attd->status = 0;

        $attd->save();

        return redirect()->route('hrm.request.index')->with(['success' => 'Permintaan berhasil diproses']);

    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('templates.hrm.attendance.request.detail', [
            'attd' => RequestAttendance::findOrFail($id),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $reqattd = RequestAttendance::findOrFail($id);


        $attds = Attendance::where('user_id', $reqattd->user_id)
        ->where(function ($query) {
            $query->where('status', 'absent')
                  ->orWhere('late_in', 'yes')
                  ->orWhere('late_out', 'yes');
        })
        ->get();

        // mengambil tanggal dari model
        $dateValue = ! empty($reqattd->date) ? $reqattd->date->format('Y-m-d') : old('date');

        // dd($dateValue);
        return view('templates.hrm.attendance.request.edit', [
            'reqattd' => $reqattd,
            'attd' => $attds,
            'dateValue' => $dateValue,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $expense = RequestAttendance::findOrFail($id);
        if (! $expense) {
            return back()->with('warning', 'Data tidak ditemukan');
        }
        // dd($request->all());

        $action = $request->input('action');

        // Dapatkan user yang sedang diautentikasi
        $user = Auth::user();

        // Dapatkan nama role yang dimiliki oleh user
        $roles = $user->getRoleNames(); // Mengembalikan koleksi

        // Jika Anda hanya ingin menampilkan atau menggunakan role pertama
        $roleName = $roles->first();
        $rules = [
            'date' => 'required',
            'attendance_id' => 'required',
            'details' => 'required',
        ];

        $message = [
            'required' => 'Tidak boleh kosong!',
        ];

        $validateData = $request->validate($rules, $message);

        $current = $expense->file_pendukung;
        if ($request->file_pendukung != null) {

            $file = $request->file('file_pendukung');
            $path = public_path().'/hopeui/html/assets/files/attendance/request/';
            $filename = rand(11111111, 99999999).$file->getClientOriginalName();

            $file->move(public_path('/hopeui/html/assets/files/attendance/request/'), $filename);

            $currentFile = $path.$current;
            if (file_exists($currentFile)) {
                @unlink($currentFile);
            }

        } else {
            $filename = $current;
        }


        $expense->update([
            'date' => $request->date,
            'attendance_id' => $request->attendance_id,
            'details' => $request->details,
            'file_pendukung' => $filename,
        ]);

        if ($roleName === 'superadmin') {
            $expense->update([
                'admin_id' => Auth::user()->id,
                'agreed_at' => now(),
                'status' => $action,
            ]);
            if ($action == 1) {
                $attendance= Attendance::findorFail($request->attendance_id);
                $attendance->update(array(
                    'status' => 'present',
                    'late_in' => null,
                    'late_out' => null,
                    'admin_id' => Auth::user()->id,
                ));
            }
        }

        return redirect()->back()->with(['success' => 'Permintaan berhasil diproses']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
    }

    public function download($id)
    {
        $expense = RequestAttendance::find($id);
        $filePath = public_path('/hopeui/html/assets/files/attendance/request/'.$expense->file_pendukung);

        return response()->download($filePath);
    }
}
