@extends('layouts.app')

@section('title')
ログイン
@endsection

@section('content')
<h1>ログイン</h1>
<form action="/login" method="post">
    @csrf
    <div>
        <label for="email">メールアドレス</label>
        <input name="email" type="email" id="email">
    </div>
    <div>
        <label for="password">パスワード</label>
        <input name="password" type="password" id="password">
    </div>
    <button type="submit">ログインする</button>
</form>
<a href="">会員登録はこちら</a>
@endsection