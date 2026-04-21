@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_detail.css') }}">
@endsection

@section('content')
<div class="detail-container">
    <h1 class="detail-title">勤怠詳細</h1>

    @php
        $isPending = $attendance->correctRequest && $attendance->correctRequest->status === 'pending';
    @endphp

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
                    <div class="time-wrapper">
                        <span class="date-year">{{ $attendance->check_in->format('Y年') }}</span>
                        <span class="time-separator"></span>
                        <span class="date-day">{{ $attendance->check_in->format('n月j日') }}</span>
                    </div>
                </td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td>
                    <div class="time-wrapper">
                        <input type="time" name="check_in" value="{{ $isPending ? $attendance->correctRequest->updated_check_in : $attendance->check_in->format('H:i') }}" {{ $isPending ? 'readonly' : '' }}>
                        <span class="time-separator">～</span>
                        <input type="time" name="check_out" value="{{ $isPending ? $attendance->correctRequest->updated_check_out : ($attendance->check_out ? $attendance->check_out->format('H:i') : '') }}" {{ $isPending ? 'readonly' : '' }}>
                    </div>
                    @error('check_out')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </td>
            </tr>

            @if($isPending)
                @foreach($attendance->correctRequest->updated_break_times as $index => $break)
                <tr>
                    <th>休憩{{ count($attendance->correctRequest->updated_break_times) > 1 ? $index + 1 : '' }}</th>
                    <td>
                        <div class="time-wrapper">
                            <input type="time" name="break_in[]" value="{{ $break['break_in'] }}" readonly>
                            <span class="time-separator">～</span>
                            <input type="time" name="break_out[]" value="{{ $break['break_out'] ?? '' }}" readonly>
                        </div>
                    </td>
                </tr>
                @endforeach
            @else
                @foreach($attendance->breakTimes as $index => $breakTime)
                <tr>
                    <th>休憩{{ $attendance->breakTimes->count() > 1 ? $index + 1 : '' }}</th>
                    <td>
                        <div class="time-wrapper">
                            <input type="time" name="break_in[]" value="{{ $breakTime->break_in->format('H:i') }}">
                            <span class="time-separator">～</span>
                            <input type="time" name="break_out[]" value="{{ $breakTime->break_out ? $breakTime->break_out->format('H:i') : '' }}">
                        </div>
                        @error("break_in.{$index}")
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        @error("break_out.{$index}")
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </td>
                </tr>
                @endforeach
                <tr>
                    @php $newIndex = $attendance->breakTimes->count(); @endphp
                    <th>休憩{{ $attendance->breakTimes->count() > 0 ? $attendance->breakTimes->count() + 1 : '' }}</th>
                    <td>
                        <div class="time-wrapper">
                            <input type="time" name="break_in[]" value="">
                            <span class="time-separator">～</span>
                            <input type="time" name="break_out[]" value="">
                        </div>
                        @error("break_in.{$newIndex}")
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        @error("break_out.{$newIndex}")
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </td>
                </tr>
            @endif

            <tr>
                <th>備考</th>
                <td>
                    <textarea name="note" {{ $isPending ? 'readonly' : '' }}>{{ $isPending ? $attendance->correctRequest->request_note : $attendance->note }}</textarea>
                    @error('note')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </td>
            </tr>
        </table>

        @if($isPending)
            <p class="pending-message">*承認待ちのため修正はできません。</p>
        @else
            <button type="submit" class="detail-button">修正</button>
        @endif
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