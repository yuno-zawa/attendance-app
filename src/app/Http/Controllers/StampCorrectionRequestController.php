<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceCorrectRequest;
use App\Http\Requests\StampCorrectionRequest;

class StampCorrectionRequestController extends Controller
{
    public function store(StampCorrectionRequest $request, $id)
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

        AttendanceCorrectRequest::create([
            'attendance_id' => $attendance->id,
            'request_note' => $request->note,
            'status' => 'pending',
        ]);

        return redirect('/attendance/list');
    }
}