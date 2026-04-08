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
        // チェックイン処理の実装
    }

    public function checkOut(Request $request)
    {
        // チェックアウト処理の実装
    }

    public function breakIn(Request $request)
    {
        // 休憩開始処理の実装
    }

    public function breakOut(Request $request)
    {
        // 休憩終了処理の実装
    }
}
