<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StampCorrectionRequest;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

    public function detail($id)
    {
        $attendance = Attendance::with(['user', 'breakTimes', 'correctRequest'])->findOrFail($id);

        return view('attendance_detail', [
            'attendance' => $attendance,
            'isAdmin' => true,
        ]);
    }

    public function update(StampCorrectionRequest $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $date = $attendance->check_in->format('Y-m-d');

        $attendance->update([
            'check_in' => $date . ' ' . $request->check_in,
            'check_out' => $request->check_out ? $date . ' ' . $request->check_out : null,
            'note' => $request->note,
        ]);

        $attendance->breakTimes()->delete();

        $breakIns = $request->input('break_in', []);
        $breakOuts = $request->input('break_out', []);

        foreach ($breakIns as $index => $breakIn) {
            if ($breakIn) {
                $attendance->breakTimes()->create([
                    'break_in' => $date . ' ' . $breakIn,
                    'break_out' => !empty($breakOuts[$index]) ? $date . ' ' . $breakOuts[$index] : null,
                ]);
            }
        }

        return redirect('/admin/attendance/' . $attendance->id)->with('success', '勤怠情報を修正しました。');
    }

    public function stafflist(Request $request, $id)
    {
        $month = $request->query('month', Carbon::now()->format('Y-m'));
        $currentMonth = Carbon::parse($month);

        $attendances = Attendance::where('user_id', $id)
            ->whereYear('check_in', $currentMonth->year)
            ->whereMonth('check_in', $currentMonth->month)
            ->with('breakTimes')
            ->orderBy('check_in', 'asc')
            ->get();

        $daysInMonth = $currentMonth->daysInMonth;
        $dailyAttendances = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $currentMonth->copy()->day($day);
            $attendance = $attendances->first(function ($att) use ($date) {
                return $att->check_in->format('Y-m-d') === $date->format('Y-m-d');
            });

            if ($attendance) {
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

            $dailyAttendances[] = [
                'date' => $date,
                'attendance' => $attendance,
            ];
        }

        $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');

        $user = User::findOrFail($id);

        return view('attendance_list', compact('dailyAttendances', 'currentMonth', 'prevMonth', 'nextMonth', 'user'));
    }

    public function exportCsv(Request $request, $id)
    {
        $month = $request->query('month', Carbon::now()->format('Y-m'));
        $currentMonth = Carbon::parse($month);
        $user = User::findOrFail($id);

        $attendances = Attendance::where('user_id', $id)
            ->whereYear('check_in', $currentMonth->year)
            ->whereMonth('check_in', $currentMonth->month)
            ->with('breakTimes')
            ->orderBy('check_in', 'asc')
            ->get();

        return response()->streamDownload(function () use ($attendances) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['日付', '出勤', '退勤', '休憩', '合計']);

            foreach ($attendances as $attendance) {
                $breakTotal = $attendance->breakTimes->reduce(function ($carry, $breakTime) {
                    if ($breakTime->break_out) {
                        return $carry + (int) $breakTime->break_in->diffInMinutes($breakTime->break_out, true);
                    }
                    return $carry;
                }, 0);

                $breakFormatted = floor($breakTotal / 60) . ':' . str_pad($breakTotal % 60, 2, '0', STR_PAD_LEFT);

                $workFormatted = '';
                if ($attendance->check_out) {
                    $workTotal = (int) $attendance->check_in->diffInMinutes($attendance->check_out, true) - $breakTotal;
                    $workFormatted = floor($workTotal / 60) . ':' . str_pad($workTotal % 60, 2, '0', STR_PAD_LEFT);
                }

                fputcsv($handle, [
                    $attendance->check_in->format('Y/m/d'),
                    $attendance->check_in->format('H:i'),
                    $attendance->check_out ? $attendance->check_out->format('H:i') : '',
                    $breakFormatted,
                    $workFormatted,
                ]);
            }

            fclose($handle);
        }, $user->name . '_' . $currentMonth->format('Y_m') . '_勤怠.csv');
    }
}
