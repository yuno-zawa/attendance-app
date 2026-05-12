@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="attendance-container">
    <div class="attendance-status">
        @if($status === 'off')
            勤務外
        @elseif($status === 'working')
            出勤中
        @elseif($status === 'break')
            休憩中
        @elseif($status === 'finished')
            退勤済
        @endif
    </div>
    <div id="current-date" class="attendance-date"></div>
    <div id="current-time" class="attendance-time"></div>

    @if($status === 'off')
        <form method="POST" action="/attendance/check-in">
            @csrf
            <button type="submit" class="attendance-button">出勤</button>
        </form>
    @elseif($status === 'working')
        <div class="attendance-buttons">
            <form method="POST" action="/attendance/check-out">
                @csrf
                <button type="submit" class="attendance-button">退勤</button>
            </form>
        <form method="POST" action="/attendance/break-in">
                @csrf
                <button type="submit" class="break-button">休憩入</button>
            </form>
        </div>
    @elseif($status === 'break')
        <form method="POST" action="/attendance/break-out">
            @csrf
            <button type="submit" class="break-button">休憩戻</button>
        </form>
    @elseif($status === 'finished')
        <p class="attendance-message">お疲れ様でした。</p>
    @endif
</div>

<script>
    function updateDateTime() {
        const now = new Date();
        const year = now.getFullYear();
        const month = now.getMonth() + 1;
        const day = now.getDate();
        const weekdays = ['日', '月', '火', '水', '木', '金', '土'];
        const weekday = weekdays[now.getDay()];

        document.getElementById('current-date').textContent =
            year + '年' + month + '月' + day + '日(' + weekday + ')';

        document.getElementById('current-time').textContent =
            String(now.getHours()).padStart(2, '0') + ':' +
            String(now.getMinutes()).padStart(2, '0');
    }

    updateDateTime();
    setInterval(updateDateTime, 1000);
</script>
@endsection