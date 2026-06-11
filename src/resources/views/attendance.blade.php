@extends('layouts.app')

@section('title')
勤怠登録
@endsection

@section('content')
<div class="flex flex-col justify-center items-center pt-20 w-full bg-[#F0EFF2] ">
    <span class="px-4 py-1 rounded-full bg-[#C8C8C8] text-lg font-bold text-[#696969] tracking-widest">{{ $status }}</span>
    <span class="mt-8 text-4xl">{{ $today }}</span>
    <span class="mt-8 mb-24 font-bold text-7xl" id="time">{{ $time }}</span>
    <div>
        <form action="{{ route('attendance.store')}}" method="post">
            @csrf
            @if($status === '勤務外')
            <button class="px-16 py-4 bg-black rounded-2xl text-3xl font-bold text-white">出勤</button>
            @elseif($status === '出勤中')
            <div class="flex flex-row gap-8">
                <button class="px-16 py-4 bg-black rounded-2xl text-3xl font-bold text-white" name="action" value="clock_out">退勤</button>
                <button class=" px-16 py-4 bg-white rounded-2xl text-3xl font-bold" name="action" value="break_in">休憩入</button>
            </div>
            @elseif($status === '休憩中')
            <button class="px-16 py-4 bg-white rounded-2xl text-3xl font-bold">休憩戻</button>
            @endif
        </form>
        @if($status === '退勤済')
        <p class="text-2xl tracking-wide">お疲れ様でした。</p>
        @endif
    </div>
</div>
<script>
    function updateTime() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        document.getElementById('time').textContent = hours + ':' + minutes;
    }

    setInterval(updateTime, 1000);
</script>
@endsection