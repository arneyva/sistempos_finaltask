<?php

namespace App\Http\Controllers\hrm;

use Carbon\Carbon;
use App\Http\Requests;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\OfficeShift;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ClockController extends Controller
{
    public function index() {

        return view('templates.hrm.attendance.webclock.index');
        // return view('templates.hrm.attendance.my-attendance.ph');
    }

    public function clocking(Request $request) {
        if ($request->pin == null) 
        {
            return response()->json([
                "error" => trans("Please enter your PIN number")
            ]);
        }

        if($request->type ==  null) 
        {
            return response()->json([
                "error" => trans("Please click the click-in or clock-out button")
            ]);
        }

        
        $pin = strtoupper($request->pin);
        $type = $request->type;
        $now = Carbon::now('WIB');
        $today = Carbon::today('WIB');
        $day = strtolower($now->format('l'));

        
        //employee
        $user = User::where('pin', $pin)->first();
        if (! $user) {
        return response()->json([
            "error" => trans("Employee not found")
            ]);
        }
        // Dapatkan nama role yang dimiliki oleh user
        $roles = $user->getRoleNames(); // Mengembalikan koleksi

        // Jika Anda hanya ingin menampilkan atau menggunakan role pertama
        $roleName = $roles->first();

        //mulai menghitung parameter jarak lokasi saat clocking 
        //hanya berlaku untuk role staff dan inventaris
        if ($roleName === 'staff' || $roleName === 'inventaris') {
            //ambil lokasi user bekerja
            $wareshouseUser = $user->warehouses->first();
            //ambil lat dan long lokasi kerja
            $targetLat = $wareshouseUser->latitude;
            $targetLng = $wareshouseUser->longitude;
            //cek lat dan long user dapat tidak
            if($request->input('latitude') ==  null) 
            {
                return response()->json([
                    "error" => trans("Activate your GPS and give location access to the app.")
                ]);
            }
            // ambil lat dan long posisi user
            $userLat = $request->input('latitude');
            $userLgt = $request->input('longitude');
            //ukur jarak dengan calculateDistance function
            $distance = $this->calculateDistance($targetLat, $targetLng, $userLat, $userLgt);
            
            //cek jarak berhasil didapat tidak
            if($distance ==  null) 
            {
                return response()->json([
                    "error" => trans("your work location doesn't established yet.")
                ]);
            }
            //errorkan jika jarak lebih dari 100 meter
            if ($distance > 0.1) {
                return response()->json([
                    "error" => trans("Your distance to work is too far, it's over 100 meters."),
                    "targetLat" => $targetLat,
                    "targetLng" => $targetLng,
                ]);
            }
        }

        // Ambil instance OfficeShift yang terhubung dengan user
        $officeShift = $user->office_shifts->first();
        if (!$officeShift) {
            return response()->json([
                "error" => trans("You doesn't have shift yet")
            ]);
        }
        //ambil jadwal masuk dan keluar user di hari ini
        $scheduleIn = $officeShift->{$day . '_in'}.":00";
        $scheduleOut = $officeShift->{$day . '_out'}.":00";

        //attendance user on that day
        $attendance_user_on_that_day = Attendance::where([['user_id', $user->id],['date', $today->toDateString()]])->first();
        $hasIn=$attendance_user_on_that_day->clock_in ?? null;
        $hasOut=$attendance_user_on_that_day->clock_out ?? null;
        
        if ($type == 'clockin') 
        {
            // cek attendance terakhir yang tidak ada admin_id nya, soalnya clock in harus lewat webclock
            $latestAttendance = Attendance::where('user_id', $user->id)
                                            ->where('created_at', '<', $today)
                                            ->latest()
                                            ->first();
            if ($latestAttendance) {
                $latestAttendance_date=$latestAttendance->date;
                //ambil date setelahnya, jadikan start date
                $startDate = $latestAttendance_date->addDay();
                //maju satu2 hingga sebelum date sekarang
                for ($date = $startDate; $date < $today; $date->addDay()) { 

                    $dayOfWeek = strtolower($date->format('l'));
                    if ($officeShift->{$dayOfWeek . '_in'} == null) {
                        //lanjutkan ke loop berikutnya
                        continue;
                    };

                    //periksa apakah sudah ada attendance sesuai datenya
                    if (! Attendance::where([['user_id', $user->id],['date', $date->toDateString()]])->first()) {
                        //jika tidak buat att dengan clock in dan out '-' dan status absent
                        Attendance::create([
                                'user_id' => $user->id,
                                'date' => $date->toDateString(),
                                'clock_in' => '-',
                                'clock_out' => '-',
                                'late_in' => 'yes',
                                'late_out' => 'yes',
                                'status' => "absent",
                        ]);
                    }
                    
                }
                
            }

            // cek attendance user yang belum clock out
            $attendance_where_clock_out_null=Attendance::where([['user_id', $user->id],['clock_out', null]])->get();
            if ($attendance_where_clock_out_null) {
                foreach ($attendance_where_clock_out_null as $attendance) { 
                    //ubah value clock-out menjadi '-' dan status present
                    $attendance->update(array(
                        'clock_out' => '-',
                        'late_out' => 'yes',
                        'status' => 'present',
                    ));
                }
            }
            
            //periksa apakah hari libur
            if($scheduleIn == ':00' || $scheduleOut == ':00' ){
                return response()->json([
                    "error" => trans("It's a Day-Off")
                ]);
            }
            //periksa apakah sudah clock in belum
            if($hasIn){
                return response()->json([
                    "error" => trans("You are clocked-in today")
                ]);
            }

            // Membuat objek waktu untuk jadwal masuk
            $scheduleTime = Carbon::createFromFormat('H:i:s', $scheduleIn, 'Asia/Jakarta');
            // Menambahkan 30 menit lebih awal ke jadwal
            $schedule_in_begin_time = $scheduleTime->copy()->subMinutes(30);
            // Menambahkan 20 menit ke jadwal
            $schedule_in_end_time = $scheduleTime->copy()->addMinutes(20);
            // inisiasi waktu telat
            $schedule_in_late_begin_time = $schedule_in_end_time->addSecond();
            // inisiasi berakhir waktu masuk
            $schedule_in_late_end_time = Carbon::createFromFormat('H:i:s', $scheduleOut, 'Asia/Jakarta');

            // Jika waktu keluar lebih kecil dari waktu masuk, tambahkan satu hari ke waktu keluar
            if ($schedule_in_late_end_time->lessThan($scheduleTime)) {
                $schedule_in_late_end_time->addDay();
            }
            
            if ($now->lessThan($schedule_in_begin_time)){
                return response()->json([
                    "error" => trans("It's too early to clock in.")
                ]);
            };

            // Jika waktu saat ini berada pada rentang jadwal masuk 30 menit sebelum hingga 20 menit kedepan
            if ($now->between($schedule_in_begin_time, $schedule_in_end_time)) {

                Attendance::create([
                        'user_id' => $user->id,
                        'date' => $today->toDateString(),
                        'clock_in' => $now->toTimeString(),
                        'status' => 'present',
                    
                ]);
                if ($roleName === 'staff' || $roleName === 'inventaris') {
                    return response()->json([
                        "type" => $type,
                        "time" => $now->toTimeString(),
                        "firstname" => $user->firstname,
                        "lastname" => $user->lastname,
                        "targetLat" => $targetLat,
                        "targetLng" => $targetLng,
                    ]);
                } else {
                    return response()->json([
                        "type" => $type,
                        "time" => $now->toTimeString(),
                        "firstname" => $user->firstname,
                        "lastname" => $user->lastname,
                    ]);
                }

            // Jika waktu saat ini berada pada rentang setelah jadwal masuk hingga jadwal keluar
            } elseif ($now->between($schedule_in_late_begin_time, $schedule_in_late_end_time)) {

                Attendance::create([
                        'user_id' => $user->id,
                        'date' => $today->toDateString(),
                        'clock_in' => $now->toTimeString(),
                        'late_in' => "yes",
                        'status' => "present",
                ]);
                if ($roleName === 'staff' || $roleName === 'inventaris') {
                    return response()->json([
                        "type" => $type,
                        "time" => $now->toTimeString(),
                        "firstname" => $user->firstname,
                        "lastname" => $user->lastname,
                        "targetLat" => $targetLat,
                        "targetLng" => $targetLng,
                    ]);
                } else {
                    return response()->json([
                        "type" => $type,
                        "time" => $now->toTimeString(),
                        "firstname" => $user->firstname,
                        "lastname" => $user->lastname,
                    ]);
                }

            } elseif ($now->greaterThan($schedule_in_late_end_time)) {
                return response()->json([
                    "error" => trans("It's too late to clock in.")
                ]);
            }
        }

        if ($type == 'clockout') 
        {
            // Membuat objek waktu untuk jadwal masuk
            $scheduleInTime = Carbon::createFromFormat('H:i:s', $scheduleIn, 'Asia/Jakarta');
            // Membuat objek waktu untuk jadwal keluar
            $scheduleTime = Carbon::createFromFormat('H:i:s', $scheduleOut, 'Asia/Jakarta');
            // Jika waktu keluar lebih kecil dari waktu masuk, tambahkan satu hari ke waktu keluar
            if ($scheduleTime->lessThan($scheduleInTime)) {
                $scheduleTime->addDay();
                //attendance user on that day kurangkan juga satu hari
                $attendance_user_on_that_day = Attendance::where([['user_id', $user->id],['date', $scheduleTime->subDay()->toDateString()]])->first();
                $hasIn=$attendance_user_on_that_day->clock_in ?? null;
                $hasOut=$attendance_user_on_that_day->clock_out ?? null;
            }
            // Menambahkan 30 menit ke jadwal
            $schedule_out_end_time = $scheduleTime->copy()->addMinutes(30);
            
            if(!$hasIn){
                return response()->json([
                    "error" => trans("You are not clocked-in today")
                ]);
            }
            if($hasOut){
                return response()->json([
                    "error" => trans("You were clocked-out today.")
                ]);
            }

            // Jika waktu saat ini kurang dari jadwal
            if ($now->lessThan($scheduleTime)) {
                return response()->json([
                    "error" => trans("It's too early to clock out.")
                ]);
            // Jika waktu saat ini berada pada rentang jadwal hingga 30 menit ke depan
            } elseif ($now->between($scheduleTime, $schedule_out_end_time)) {
                $attendance_user_on_that_day->update(array(
                    'clock_out' => $now->toTimeString(),
                ));
                if ($roleName === 'staff' || $roleName === 'inventaris') {
                    return response()->json([
                        "type" => $type,
                        "time" => $now->toTimeString(),
                        "firstname" => $user->firstname,
                        "lastname" => $user->lastname,
                        "targetLat" => $targetLat,
                        "targetLng" => $targetLng,
                    ]);
                } else {
                    return response()->json([
                        "type" => $type,
                        "time" => $now->toTimeString(),
                        "firstname" => $user->firstname,
                        "lastname" => $user->lastname,
                    ]);
                }
            // Jika waktu saat ini di luar dua kondisi di atas
            } else {
                $attendance_user_on_that_day->update(array(
                    'clock_out' => $now->toTimeString(),
                    'late_out' => 'yes',
                ));
                if ($roleName === 'staff' || $roleName === 'inventaris') {
                    return response()->json([
                        "type" => $type,
                        "time" => $now->toTimeString(),
                        "firstname" => $user->firstname,
                        "lastname" => $user->lastname,
                        "targetLat" => $targetLat,
                        "targetLng" => $targetLng,
                    ]);
                } else {
                    return response()->json([
                        "type" => $type,
                        "time" => $now->toTimeString(),
                        "firstname" => $user->firstname,
                        "lastname" => $user->lastname,
                    ]);
                }
            }

            if($hasIn && $hasOut){
                $attendance_user_on_that_day->update(array(
                    'status' => 'present',
                ));
            }
        }
    }

    public function calculateDistance($targetLat, $targetLng, $userLat, $userLgt)
    {
        $earthRadius = 6371; // Radius bumi dalam kilometer

        // Validasi range nilai lintang dan bujur
        if ($targetLat < -90 || $targetLat > 90 || $userLat < -90 || $userLat > 90 ||
            $targetLng < -180 || $targetLng > 180 || $userLgt < -180 || $userLgt > 180) {
            return null;
        }

        // Konversi derajat ke radian
        $targetLat = deg2rad($targetLat);
        $targetLng = deg2rad($targetLng);
        $userLat = deg2rad($userLat);
        $userLgt = deg2rad($userLgt);

        // Haversine formula
        $dlat = $userLat - $targetLat;
        $dlon = $userLgt - $targetLng;

        $a = sin($dlat / 2) * sin($dlat / 2) +
            cos($targetLat) * cos($userLat) * 
            sin($dlon / 2) * sin($dlon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return $distance;
    }
}




