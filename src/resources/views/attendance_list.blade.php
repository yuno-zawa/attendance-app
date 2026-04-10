@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}">
@endsection

@section('content')
<div class="attendance-list-container">
    <h1 class="attendance-list-title">勤怠一覧</h1>

    <div class="month-nav">
        <a href="/attendance/list?month={{ $prevMonth }}">← 前月</a>
        <span>{{ $currentMonth->format('Y年m月') }}</span>
        <a href="/attendance/list?month={{ $nextMonth }}">翌月 →</a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $attendance)
            <tr>
                <td>{{ $attendance->check_in->format('m/d') }}</td>
                <td>{{ $attendance->check_in->format('H:i') }}</td>
                <td>{{ $attendance->check_out ? $attendance->check_out->format('H:i') : '' }}</td>
                <td>
                    @php
                        $breakTotal = $attendance->breakTimes->reduce(function ($carry, $breakTime) {
                            if ($breakTime->break_out) {
                                return $carry + $breakTime->break_out->diffInMinutes($breakTime->break_in);
                            }
                            return $carry;
                        }, 0);
                        echo floor($breakTotal / 60) . ':' . str_pad($breakTotal % 60, 2, '0', STR_PAD_LEFT);
                    @endphp
                </td>
                <td>
                    @php
                        if ($attendance->check_out) {
                            $workTotal = $attendance->check_out->diffInMinutes($attendance->check_in) - $breakTotal;
                            echo floor($workTotal / 60) . ':' . str_pad($workTotal % 60, 2, '0', STR_PAD_LEFT);
                        }
                    @endphp
                </td>
                <td><a href="/attendance/detail/{{ $attendance->id }}">詳細</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection