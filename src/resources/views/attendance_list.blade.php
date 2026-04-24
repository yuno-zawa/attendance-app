@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}">
@endsection

@section('content')
<div class="list-container">
    <h1 class="list-title">
        @isset($isAdmin)
            {{ $currentDate->format('Y年n月j日') }}の勤怠
        @else
            勤怠一覧
        @endisset
    </h1>

    <div class="month-nav">
        @isset($isAdmin)
            <a href="/admin/attendance/list?date={{ $prevDate }}">← 前日</a>
            <span><i class="fa-regular fa-calendar"></i> {{ $currentDate->format('Y/m/d') }}</span>
            <a href="/admin/attendance/list?date={{ $nextDate }}">翌日 →</a>
        @else
            <a href="/attendance/list?month={{ $prevMonth }}">← 前月</a>
            <span><i class="fa-regular fa-calendar"></i> {{ $currentMonth->format('Y/m') }}</span>
            <a href="/attendance/list?month={{ $nextMonth }}">翌月 →</a>
        @endisset>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>{{ isset($isAdmin) ? '名前' : '日付' }}</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @isset($isAdmin)
                @forelse ($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->user->name }}</td>
                    <td>{{ $attendance->check_in->format('H:i') }}</td>
                    <td>{{ $attendance->check_out ? $attendance->check_out->format('H:i') : '' }}</td>
                    <td>{{ $attendance->break_total }}</td>
                    <td>{{ $attendance->work_total }}</td>
                    <td><a href="/admin/attendance/{{ $attendance->id }}">詳細</a></td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">この日の勤怠データはありません</td>
                </tr>
                @endforelse
            @else
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
            @endisset
        </tbody>
    </table>
</div>
@endsection