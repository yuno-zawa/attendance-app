<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceCorrectRequest;

class StampCorrectionRequestController extends Controller
{
    public function index()
    {
        $requests = AttendanceCorrectRequest::with('attendance.user')->latest()->get();
        $pendingRequests = $requests->where('status', 'pending');
        $approvedRequests = $requests->where('status', 'approved');

        return view('request_list', compact('pendingRequests', 'approvedRequests') + ['isAdmin' => true]);
    }

    public function approve(int $id)
    {
        $correctRequest = AttendanceCorrectRequest::with('attendance.user','attendance.breakTimes')->findOrFail($id);

        return view('attendance_detail', [
            'attendance' => $correctRequest->attendance,
            'corectRequest' => $correctRequest,
            'isAdmin' => true,
            'isApproval' => $correctRequest->status === 'pending',
        ]);
    }

    public function executeApprove(int $id)
    {

        $correctRequest = AttendanceCorrectRequest::findOrFail($id);

        if ($correctRequest->status === 'approved') {
            return redirect('/admin/stamp_correction_request/approve/' . $id);
        }

        $correctRequest = AttendanceCorrectRequest::findOrFail($id);
        $correctRequest->update(['status' => 'approved']);

        $attendance = $correctRequest->attendance;
        $date = $attendance->check_in->format('Y-m-d');

        $attendance->update([
            'check_in' => $date . ' ' . $correctRequest->updated_check_in,
            'check_out' => $correctRequest->updated_check_out ? $date . ' ' . $correctRequest->updated_check_out : null,
            'note' => $correctRequest->request_note,
        ]);

        $attendance->breakTimes()->delete();

        if ($correctRequest->updated_break_times) {
            foreach ($correctRequest->updated_break_times as $break) {
                if ($break['break_in']) {
                    $attendance->breakTimes()->create([
                        'break_in' => $date . ' ' . $break['break_in'],
                        'break_out' => !empty($break['break_out']) ? $date . ' ' . $break['break_out'] : null,
                    ]);
                }
            }
        }

        return redirect('/admin/stamp_correction_request/approve/' . $id);
    }

}