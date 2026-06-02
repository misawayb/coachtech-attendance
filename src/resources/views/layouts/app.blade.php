<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | coachtech 勤怠管理</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
            <a class="nav-login" href="">勤怠</a>
            <a class="nav-mypage" href="">勤怠一覧</a>
            <a class="nav-sell" href="">申請</a>
            <form class="logout" action="/logout" method="post">
                @csrf
                <button class="nav-logout" type="submit">ログアウト</button>
            </form>
        </nav>
        @endauth
    </header>
    <main>
        @yield('content')
    </main>
</body>

</html>