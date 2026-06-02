<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | coachtech 勤怠管理</title>
    <link rel="stylesheet" href="{{ asset('sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('common.css') }}">
    @yield('css')
</head>

<body>
    <header>
        <p class="header-logo">
            <a href="/">
                <img src=" {{ asset( 'image/coachtech_header_logo.png' )}}" alt="COACHTECHロゴ">
            </a>
        </p>
        @auth
            <nav class="header-nav">
            <a class="nav-login" href="/login">ログイン</a>
            <a class="nav-mypage" href="/mypage">マイページ</a>
            <a class="nav-sell" href="/sell">出品</a>
            </nav>
        @endauth
    </header>
    <main>
        @yield('content')
    </main>
</body>

</html>