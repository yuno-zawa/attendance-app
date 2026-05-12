@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="auth-container">
    <h1 class="auth-title">{{ $title ?? 'ログイン' }}</h1>
    <form method="POST" action="{{ $action ?? '/login'}}" class="login-form">
        @csrf
        <div class="form-group">
            <label for="email" class="form-label">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-input">
            @error('email')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="password" class="form-label">パスワード</label>
            <input id="password" type="password" name="password" class="form-input">
            @error('password')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="login-button">{{ $button ?? 'ログインする' }}</button>
    </form>
    @if(!@isset($isAdmin))
        <a href="{{ route('register') }}" class="register-link">会員登録はこちら</a>
    @endif
</div>
@endsection