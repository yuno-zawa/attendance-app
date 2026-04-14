@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}">
@endsection

@section('content')
<div class="list-container">
    <h1 class="list-title">勤怠一覧</h1>

    <div class="month-nav">
        <a href="/attendance/list?month={{ $prevMonth }}">← 前月</a>
        <span><i class="fa-regular fa-calendar"></i> {{ $currentMonth->format('Y/m') }}</span>
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
            @foreach ($dailyAttendances as $day)
            <tr>
                <td>{{ $day['date']->format('m/d') }}({{ $day['date']->isoFormat('ddd') }})</td>
                <td>{{ $day['attendance'] ? $day['attendance']->check_in->format('H:i') : '' }}</td>
                <td>{{ $day['attendance'] && $day['attendance']->check_out ? $day['attendance']->check_out->format('H:i') : '' }}</td>
                <td>{{ $day['attendance'] ? $day['attendance']->break_total : '' }}</td>
                <td>{{ $day['attendance'] ? $day['attendance']->work_total : '' }}</td>
                <td>
                    @if($day['attendance'])
                            <a href="/attendance/detail/{{ $day['attendance']->id }}">詳細</a>
                    @endif
                </td>
            </tr>
@endforeach
        </tbody>
    </table>
</div>
@endsection