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

    public function list(Request $request)
{
    $month = $request->query('month', Carbon::now()->format('Y-m'));
    $currentMonth = Carbon::parse($month);

    $attendances = Attendance::where('user_id', Auth::id())
        ->whereYear('check_in', $currentMonth->year)
        ->whereMonth('check_in', $currentMonth->month)
        ->with('breakTimes')
        ->get();

    foreach ($attendances as $attendance) {
        $breakTotal = $attendance->breakTimes->reduce(function ($carry, $breakTime) {
            if ($breakTime->break_out) {
                return $carry + $breakTime->break_out->diffInMinutes($breakTime->break_in);
            }
            return $carry;
        }, 0);

        $attendance->break_total = floor($breakTotal / 60) . ':' . str_pad($breakTotal % 60, 2, '0', STR_PAD_LEFT);

        if ($attendance->check_out) {
            $workTotal = $attendance->check_out->diffInMinutes($attendance->check_in) - $breakTotal;
            $attendance->work_total = floor($workTotal / 60) . ':' . str_pad($workTotal % 60, 2, '0', STR_PAD_LEFT);
        } else {
            $attendance->work_total = '';
        }
    }

    $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
    $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');

    return view('attendance_list', compact('attendances', 'currentMonth', 'prevMonth', 'nextMonth'));
}
}
