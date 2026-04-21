@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/request_list.css') }}">
@endsection

@section('content')
<div class="request-container">
    <h1 class="request-title">申請一覧</h1>
    <div class="tab-switch">
        <label><input type="radio" name="TAB">承認待ち</label>
        <div class="tab-content">
            <tr>
                <td></td>
            </tr>
        </div>
    </div>