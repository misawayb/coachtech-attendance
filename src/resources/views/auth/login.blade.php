@extends('layouts.app')

@section('title')
ログイン
@endsection

@section('content')
<div class="w-full max-w-[680px] my-24">
    <h1 class="mb-12 text-center text-4xl font-bold">ログイン</h1>
    <form action="/login" method="post">
        @csrf
        <div class="flex flex-col mb-8">
            <label class="text-2xl font-bold" for="email">メールアドレス</label>
            <input class="px-4 h-[45px] my-2 border rounded-[4px] text-2xl" name="email" type="email" id="email">
            @error('email')
            <p class="text-2xl text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex flex-col mb-8">
            <label class="text-2xl font-bold" for="password">パスワード</label>
            <input class="px-4 h-[45px] my-2 border rounded-[4px] text-2xl" name="password" type="password" id="password">
            @error('password')
            <p class="text-2xl text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <button class="mt-12 mb-4 w-full h-[60px] bg-black text-white text-[26px] font-bold rounded-[5px]" type="submit">ログインする</button>
    </form>
    <a class="block text-center text-xl text-[#0073CC]" href="/register">会員登録はこちら</a>
</div>
@endsection