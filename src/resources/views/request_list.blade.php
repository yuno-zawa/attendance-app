@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/request_list.css') }}">
@endsection

@section('content')
<div class="request-list-container">
    <h1 class="request-list-title">申請一覧</h1>

    <div class="tab-switch">
        <input type="radio" name="tab" id="tab-pending" checked>
        <input type="radio" name="tab" id="tab-approved">
        <div class="tab-labels">
            <label for="tab-pending">承認待ち</label>
            <label for="tab-approved">承認済み</label>
        </div>

        <div class="tab-content" id="content-pending">
            <table class="request-table">
                <thead>
                    <tr>
                        <th>状態</th>
                        <th>名前</th>
                        <th>対象日時</th>
                        <th>申請理由</th>
                        <th>申請日時</th>
                        <th>詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingRequests as $request)
                    <tr>
                        <td>承認待ち</td>
                        <td>{{ $request->attendance->user->name }}</td>
                        <td>{{ $request->attendance->check_in->format('Y/m/d') }}</td>
                        <td>{{ $request->request_note }}</td>
                        <td>{{ $request->created_at->format('Y/m/d') }}</td>
                        <td><a href="/attendance/detail/{{ $request->attendance_id }}">詳細</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="tab-content" id="content-approved">
            <table class="request-table">
                <thead>
                    <tr>
                        <th>状態</th>
                        <th>名前</th>
                        <th>対象日時</th>
                        <th>申請理由</th>
                        <th>申請日時</th>
                        <th>詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($approvedRequests as $request)
                    <tr>
                        <td>承認済み</td>
                        <td>{{ $request->attendance->user->name }}</td>
                        <td>{{ $request->attendance->check_in->format('Y/m/d') }}</td>
                        <td>{{ $request->request_note }}</td>
                        <td>{{ $request->created_at->format('Y/m/d') }}</td>
                        <td><a href="/attendance/detail/{{ $request->attendance_id }}">詳細</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection