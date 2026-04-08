@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="verify-container">
    <p class="verify-message">登録していただいたメールアドレスに認証メールを送付しました。<br>メール認証を完了してください。</p>
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <a href="http://localhost:8025" class="mail-button" target="_blank">認証はこちらから</a>
    </form>
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="resend-link">認証メールを再送する</button>
    </form>
</div>
@endsection