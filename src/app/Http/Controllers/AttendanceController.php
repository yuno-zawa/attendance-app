<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendance = Attendance::where('user_id', Auth::id())
        ->WhereDate('check_in', Carbon::today())
        ->first();

        if (!$attendance) {
            $status = 'off';
        }elseif($attendance->check_out) {
            $status = 'finished';
        }elseif($attendance->breakTimes()->whereNull('break_out')->exists()) {
            $status = 'break';
        } else {
            $status = 'working';
        }

        return view('attendance', compact('attendance', 'status'));
    }

    public function checkIn(Request $request)
    {
        Attendance::create([
            'user_id' => Auth::id(),
            'check_in' => Carbon::now(),
        ]);

        return redirect('/attendance');
    }

    public function checkOut(Request $request)
    {
        $attendance = Attendance::where('user_id', Auth::id())
        ->WhereDate('check_in', Carbon::today())
        ->first();

        if ($attendance && !$attendance->check_out) {
            $attendance->update([
                'check_out' => Carbon::now(),
            ]);
        }

        return redirect('/attendance');
    }

    public function breakIn(Request $request)
    {
        $attendance = Attendance::where('user_id', Auth::id())
        ->WhereDate('check_in', Carbon::today())
        ->first();

        if ($attendance && !$attendance->check_out) {
            $attendance->breakTimes()->create([
                'break_in' => Carbon::now(),
            ]);
        }

        return redirect('/attendance');
    }

    public function breakOut(Request $request)
    {
        $attendance = Attendance::where('user_id', Auth::id())
        ->WhereDate('check_in', Carbon::today())
        ->first();

        if ($attendance) {
            $breakTime = $attendance->breakTimes()->whereNull('break_out')->first();
            if ($breakTime) {
                $breakTime->update([
                    'break_out' => Carbon::now(),
                ]);
            }
        }

        return redirect('/attendance');
    }
}
