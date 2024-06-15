<?php

namespace App\Http\Controllers\hrm;

use Carbon\Carbon;
use App\Http\Requests;
use App\Models\User;
use App\Models\OfficeShift;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClockController extends Controller
{
    public function index() {

        return view('templates.hrm.attendance.webclock');
    }

    public function clocking(Request $request) {
        $rules = [
            'pin' => 'required', 'numeric', 'min_digits:6','max_digits:6',
            'type' => 'required',
        ];
        $messages = [
            'required' => 'Tidak boleh kosong!',
        ];
        $request->validate($rules, $messages);

        $pin = strtoupper($request->pin);
        $type = $request->type;
        $date = date('Y-m-d');
        $time = date('h:i:s A');


    }
}
