@extends('layouts.app')

@section('title')
勤怠登録
@endsection

@section('content')
<div>
    <span>出勤中</span>
    <p>今日の日付</p>
    <!-- $today = now()->isoFormat('YYYY年m月d日(ddd)') をコントローラに-->
    <span>時刻</span>
    <div>
        <form action="">
            <button>退勤</button>
        </form>
        <form action="">
            <button>休憩入</button>
        </form>
    </div>
</div>
@endsection