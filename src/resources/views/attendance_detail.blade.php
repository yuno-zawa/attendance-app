@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_detail.css') }}">
@endsection

@section('content')
<div class="detail-container">
    <h1 class="detail-title">勤怠詳細</h1>

    <form method="POST" action="/stamp_correction_request/{{ $attendance->id }}" novalidate>
        @csrf
        <table class="detail-table">
            <tr>
                <th>名前</th>
                <td>{{ $attendance->user->name }}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td>
                    <div class="date-container">
                        <span class="date-year">{{ $attendance->check_in->format('Y年') }}</span>
                        <span class="date-day">{{ $attendance->check_in->format('n月j日') }}</span>
                    </div>
                </td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td>
                    <input type="time" name="check_in" value="{{ $attendance->check_in->format('H:i') }}">
                    <span class="time-separator">～</span>
                    <input type="time" name="check_out" value="{{ $attendance->check_out ? $attendance->check_out->format('H:i') : '' }}">
                    @error('check_out')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </td>
            </tr>
            @foreach($attendance->breakTimes as $index => $breakTime)
            <tr>
                <th>休憩{{ $attendance->breakTimes->count() > 1 ? $index + 1 : '' }}</th>
                <td>
                    <input type="time" name="break_in[]" value="{{ $breakTime->break_in->format('H:i') }}">
                    <span class="time-separator">～</span>
                    <input type="time" name="break_out[]" value="{{ $breakTime->break_out ? $breakTime->break_out->format('H:i') : '' }}">
                    @error('break_in.*')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    @error('break_out.*')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </td>
            </tr>
            @endforeach
            <tr>
                <th>休憩{{ $attendance->breakTimes->count() > 0 ? $attendance->breakTimes->count() + 1 : '' }}</th>
                <td>
                    <input type="time" name="break_in[]" value="">
                    <span class="time-separator">～</span>
                    <input type="time" name="break_out[]" value="">
                    @error('break_in.*')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    @error('break_out.*')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </td>
            </tr>
            <tr>
                <th>備考</th>
                <td><textarea name="note">{{ $attendance->note }}</textarea>
                    @error('note')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </td>
            </tr>
        </table>

        <button type="submit" class="detail-button">修正申請</button>
    </form>
</div>

<style>
    .detail-table input[type="time"].empty {
        color: transparent;
    }
    .detail-table input[type="time"].empty:focus {
        color: initial;
    }
</style>

<script>
    document.querySelectorAll('input[type="time"]').forEach(input => {
        if (!input.value) {
            input.classList.add('empty');
        }
        input.addEventListener('input', () => {
            if (input.value) {
                input.classList.remove('empty');
            } else {
                input.classList.add('empty');
            }
        });
    });
</script>
@endsection