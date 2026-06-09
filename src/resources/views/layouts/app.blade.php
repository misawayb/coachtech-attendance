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
    <header class="flex row w-full fixed top-0 h-20 bg-black items-center justify-between">
        <p class="mx-4 shrink-0">
            <a href="/">
                <img src="{{ asset( 'image/coachtech_header_logo.png' )}}" alt="COACHTECHロゴ">
            </a>
        </p>
        @auth
        <nav class="flex items-center shrink-0">
            <a class="mr-8 text-2xl font-bold text-white" href="">勤怠</a>
            <a class="mr-8 text-2xl font-bold text-white" href="">勤怠一覧</a>
            <a class="mr-8 text-2xl font-bold text-white" href="">申請</a>
            <form action="/logout" method="post">
                @csrf
                <button class="mr-8 text-2xl font-bold text-white" type="submit">ログアウト</button>
            </form>
        </nav>
        @endauth
    </header>
    <main class="flex justify-center min-h-screen">
        @yield('content')
    </main>
</body>

</html>