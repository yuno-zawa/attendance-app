<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理アプリ</title>
    <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @yield('css')
</head>
<body>
    <header class="header">
        <img src="{{ asset('logo.png') }}" alt="ロゴ" class="header__logo">

        @auth('admin')
        <nav class="header__nav">
            <a href="/admin/attendance/list" class="header__link">勤怠一覧</a>
            <a href="/admin/staff/list" class="header__link">スタッフ一覧</a>
            <a href="/stamp_correction_request/list" class="header__link">申請一覧</a>
            <form method="POST" action="/admin/logout" class="header__logout-form">
                @csrf
                <button type="submit" class="header__link header__logout-button">ログアウト</button>
            </form>
        </nav>
        @elseauth('web')
        <nav class="header__nav">
            <a href="/attendance" class="header__link">勤怠</a>
            <a href="/attendance/list" class="header__link">勤怠一覧</a>
            <a href="/stamp_correction_request/list" class="header__link">申請</a>
            <form method="POST" action="{{ route('logout') }}" class="header__logout-form">
                @csrf
                <button type="submit" class="header__link header__logout-button">ログアウト</button>
            </form>
        </nav>
        @endauth
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>