<?php

namespace App\Http\Controllers\hrm;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OfficeShift;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class MyAttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Ambil instance OfficeShift yang terhubung dengan user
        $officeShift = $user->office_shifts->first();
    
        // Periksa apakah semua waktu shift adalah null
        $shifts = [
            'monday_in', 'monday_out', 'tuesday_in', 'tuesday_out',
            'wednesday_in', 'wednesday_out', 'thursday_in', 'thursday_out',
            'friday_in', 'friday_out', 'saturday_in', 'saturday_out',
            'sunday_in', 'sunday_out'
        ];
    
        $allShiftsNull = true;
        if ($officeShift) {
            foreach ($shifts as $shift) {
                if (!is_null($officeShift->$shift)) {
                    $allShiftsNull = false;
                    break;
                }
            }
        }
    
        // Jika semua null, kirim pesan ke view
        if ($allShiftsNull) {
            $message = "You doesn't have shift yet";
            return view('templates.hrm.attendance.my-attendance.index', compact('message'));
        }
    
        return view('templates.hrm.attendance.my-attendance.index');
    }

    public function checkAttendance(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);
        
        $user = Auth::user();
        $month = $request->input('month');
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        $today = Carbon::today();

        // Ambil instance OfficeShift yang terhubung dengan user
        $officeShift = $user->office_shifts->first();

        // Ambil data Attendance yang terhubung dengan user dan filter berdasarkan tanggal dalam bulan
        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });

        // Siapkan array untuk menyimpan hasil
        $attendanceData = [];

        for ($date = $startDate; $date <= $endDate; $date->addDay()) { 
            $dayOfWeek = strtolower($date->format('l')); // Mendapatkan hari dalam minggu

            $scheduleIn = $officeShift->{$dayOfWeek . '_in'} ? $officeShift->{$dayOfWeek . '_in'}.':00' : '-';
            $scheduleOut = $officeShift->{$dayOfWeek . '_out'} ? $officeShift->{$dayOfWeek . '_out'}.':00' : '-';
            $status = $scheduleIn === '-' && $scheduleOut === '-' ? 'day-off' : 'working day';

            $clockIn ='-';
            $clockOut ='-';
            if (isset($attendances[$date->toDateString()])) {
                $attendance = $attendances[$date->toDateString()];
                $clockIn = $attendance->clock_in ?? '-';
                $clockOut = $attendance->clock_out ?? '-';
                $status = $attendance->status;
            } elseif ($date < $today && $status !== 'day-off') {
                $status = 'absent';
            } elseif ($date >= $today && $status !== 'day-off') {
                $status = 'working day';
            }

            $attendanceData[] = [
                'date' => $date->format('d'),
                'day' => $date->format('l'),
                'schedule_in' => $scheduleIn,
                'schedule_out' => $scheduleOut,
                'clock_in' => $clockIn,
                'clock_out' => $clockOut,
                'status' => $status,
            ];
        }


        // Tampilkan hasil ke Blade
        return view('templates.hrm.attendance.my-attendance.index', [
            'attendances' => $attendanceData,
            'month' => $month,
        ]);
    }
}