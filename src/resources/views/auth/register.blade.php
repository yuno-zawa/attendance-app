@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="auth-container">
    <h1 class="auth-title">会員登録</h1>
    <form method="POST" action="{{ route('register') }}" class="register-form" novalidate>
        @csrf
        <div class="form-group">
            <label for="name" class="form-label">名前</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" class="form-input">
            @error('name')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="email" class="form-label">メールアドレス</label>
            <input id="email" type="text" name="email" value="{{ old('email') }}"class="form-input">
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
        <div class="form-group">
            <label for="password_confirmation" class="form-label">パスワード確認</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-input">
            @error('password_confirmation')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="register-button">登録する</button>
    </form>
    <a href="{{ route('login') }}" class="login-link">ログインはこちら</a>
</div>
@endsection