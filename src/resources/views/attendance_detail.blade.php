@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_detail.css') }}">
@endsection

@section('content')
<div class="detail-container">
    <h1 class="detail-title">勤怠詳細</h1>

    <form method="POST" action="/stamp_correction_request/{{ $attendance->id }}">
        @csrf
        <table class="detail-table">
            <tr>
                <th>名前</th>
                <td>{{ $attendance->user->name }}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td>{{ $attendance->check_in->format('Y年m月d日') }}</td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td>
                    <input type="time" name="check_in" value="{{ $attendance->check_in->format('H:i') }}">
                    ～
                    <input type="time" name="check_out" value="{{ $attendance->check_out ? $attendance->check_out->format('H:i') : '' }}">
                </td>
            </tr>
            @foreach($attendance->breakTimes as $index => $breakTime)
            <tr>
                <th>休憩{{ $index + 1 }}</th>
                <td>
                    <input type="time" name="break_in[]" value="{{ $breakTime->break_in->format('H:i') }}">
                    ～
                    <input type="time" name="break_out[]" value="{{ $breakTime->break_out ? $breakTime->break_out->format('H:i') : '' }}">
                </td>
            </tr>
            @endforeach
            <tr>
                <th>休憩{{ $attendance->breakTimes->count() + 1 }}</th>
                <td>
                    <input type="time" name="break_in[]" value="">
                    ～
                    <input type="time" name="break_out[]" value="">
                </td>
            </tr>
            <tr>
                <th>備考</th>
                <td><textarea name="note">{{ $attendance->note }}</textarea></td>
            </tr>
        </table>

        <button type="submit" class="detail-button">修正申請</button>
    </form>
</div>
@endsection