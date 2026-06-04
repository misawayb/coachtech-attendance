@extends('layouts.app')

@section('title')
メール認証
@endsection

@section('content')
<div class="flex flex-col justify-center items-center w-full max-w-[680px] my-12">
    <span class="block text-center text-2xl font-bold whitespace-nowrap">
        登録していただいたメールアドレスに認証メールを送付しました。<br />
        メール認証を完了してください
    </span>
    <a class="my-16 px-8 py-4 border rounded-[10px] bg-gray-300 text-2xl font-bold" href="https://mailtrap.io/home">
        認証はこちらから
    </a>
    <form action="/email/verification-notification" method="post">
        @csrf
        <button class="text-xl text-[#0073CC]" type="submit">
            認証メールを再送する
        </button>
    </form>
</div>
@endsection