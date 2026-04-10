@extends('layouts.app')

@section('content')
<div class="container">
    <div>
        @if($status === 'off')
            <p>勤務外</p>
        @elseif($status === 'working')
            <p>出勤中</p>
        @elseif($status === 'break')
            <p>休憩中</p>
        @elseif($status === 'finished')
            <p>退勤済</p>
        @endif
    </div>
    <div id="current-date"></div>
    <div id="current-time"></div>

    @if($status === 'off')
        <form method="POST" action="/attendance/check-in">
            @csrf
            <button type="submit">出勤</button>
        </form>
    @elseif($status === 'working')
        <form method="POST" action="/attendance/break-in">
            @csrf
            <button type="submit">休憩入</button>
        </form>
        <form method="POST" action="/attendance/check-out">
            @csrf
            <button type="submit">退勤</button>
        </form>
    @elseif($status === 'break')
        <form method="POST" action="/attendance/break-out">
            @csrf
            <button type="submit">休憩戻</button>
        </form>
    @elseif($status === 'finished')
        <p>お疲れ様でした。</p>
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