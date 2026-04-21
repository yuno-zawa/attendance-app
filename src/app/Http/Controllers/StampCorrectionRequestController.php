<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\AttendanceCorrectRequest;
use App\Http\Requests\StampCorrectionRequest;

class StampCorrectionRequestController extends Controller
{
    public function store(StampCorrectionRequest $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $breakIns = $request->input('break_in', []);
        $breakOuts = $request->input('break_out', []);
        $breakTimesData = [];

        foreach ($breakIns as $index => $breakIn) {
            if (!$breakIn) continue;

            $breakTimesData[] = [
                'break_in' => $breakIn,
                'break_out' => $breakOuts[$index] ?? null,
            ];
        }

        AttendanceCorrectRequest::create([
            'user_id' => $request->user()->id,
            'attendance_id' => $attendance->id,
            'updated_check_in' => $request->check_in,
            'updated_check_out' => $request->check_out,
            'updated_break_times' => $breakTimesData,
            'request_note' => $request->note,
            'status' => 'pending',
        ]);

        return redirect()->back();
    }

    public function index(){
        $requests = AttendanceCorrectRequest::where('user_id', Auth::id())->with('attendance')->latest()->get();

        $pendingRequests = $requests->where('status', 'pending');
        $approvedRequests = $requests->where('status', 'approved');

        return view('request_list', compact('pendingRequests', 'approvedRequests'));
    }
}