<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function list(Request $request)
    {
        $date = $request->query('date', Carbon::now()->format('Y-m-d'));
        $currentDate = Carbon::parse($date);

        $attendances = Attendance::with(['user', 'breakTimes'])
        ->whereDate('check_in', $currentDate)
        ->orderBy('check_out', 'asc')
        ->get();

        foreach ($attendances as $attendance) {
            $breakTotal = $attendance->breakTimes->reduce(function ($carry, $breakTime) {
                if ($breakTime->break_out) {
                    return $carry + (int) $breakTime->break_in->diffInMinutes($breakTime->break_out, true);
                }
                return $carry;
            }, 0);

            $attendance->break_total = floor($breakTotal / 60) . ':' . str_pad($breakTotal % 60, 2, '0', STR_PAD_LEFT);

            if ($attendance->check_out) {
                $workTotal = (int) $attendance->check_in->diffInMinutes($attendance->check_out, true) - $breakTotal;
                $attendance->work_total = floor($workTotal / 60) . ':' . str_pad($workTotal % 60, 2, '0', STR_PAD_LEFT);
            } else {
                $attendance->work_total = '';
            }
        }

        $prevDate = $currentDate->copy()->subDay()->format('Y-m-d');
        $nextDate = $currentDate->copy()->addDay()->format('Y-m-d');

        return view('attendance_list', compact('attendances', 'currentDate', 'prevDate', 'nextDate') + [
            'isAdmin' => true,
        ]);
    }
}
