@extends('layouts.admin')

@section('title')
勤怠詳細
@endsection

@section('content')
<div class="pt-20 w-full bg-[#F0EFF2] min-h-screen">
    <div class="m-auto my-24 w-[900px] flex flex-col justify-center">
        <h1 class="px-8 text-4xl font-bold border-l-12 flex items-center">勤怠詳細</h1>

        <form action="{{ route('admin.update', ['id' => $targetRecord->id]) }}" method="post" class="mt-12 font-bold">
            @csrf

            <div class="bg-white rounded-2xl overflow-hidden">
                <div class="flex border-b border-[#F0EFF2] px-8 py-8">
                    <span class="w-40 text-[#737373]">名前</span>
                    <span class="pl-2">{{ $targetRecord->user->name }}</span>
                </div>

                <div class="flex border-b border-[#F0EFF2] px-8 py-8">
                    <span class="w-40 text-[#737373]">日付</span>
                    <span class="pl-2">{{ \Carbon\Carbon::parse($targetRecord->date)->format('Y年') }}</span>
                    <span class="ml-8">{{ \Carbon\Carbon::parse($targetRecord->date)->format('n月j日') }}</span>
                </div>

                <div class="flex items-center border-b border-[#F0EFF2] px-8 py-8">
                    <span class="w-40 text-[#737373]">出勤・退勤</span>
                    <input type="time" name="clock_in" value="{{ $targetRecord->clock_in ? \Carbon\Carbon::parse($targetRecord->clock_in)->format('H:i') : '' }}" class="border border-gray-300 rounded px-2 py-1 w-32">
                    <span class="mx-4">〜</span>
                    <input type="time" name="clock_out" value="{{ $targetRecord->clock_out ? \Carbon\Carbon::parse($targetRecord->clock_out)->format('H:i') : '' }}" class="border border-gray-300 rounded px-2 py-1 w-32">
                </div>

                <div id="break-rows">
                    @foreach ($targetRecord->attendanceBreak as $index => $break)
                    <div class="flex items-center border-b border-[#F0EFF2] px-8 py-8 break-row">
                        <span class="w-40 text-[#737373]">休憩{{ $index + 1 > 1 ? $index + 1 : '' }}</span>
                        <input type="time" name="breaks[{{ $index }}][break_in]" value="{{ $break->break_in ? \Carbon\Carbon::parse($break->break_in)->format('H:i') : '' }}" class="border border-gray-300 rounded px-2 py-1 w-32">
                        <span class="mx-4">〜</span>
                        <input type="time" name="breaks[{{ $index }}][break_out]" value="{{ $break->break_out ? \Carbon\Carbon::parse($break->break_out)->format('H:i') : '' }}" class="border border-gray-300 rounded px-2 py-1 w-32">
                    </div>
                    @endforeach
                </div>

                <div class="px-8 py-8 border-b border-[#F0EFF2]">
                    <button type="button" id="add-break" class="text-[#737373] hover:underline">＋ 休憩を追加</button>
                </div>

                <div class="flex px-8 py-8 border-[#F0EFF2]">
                    <span class="w-40 text-[#737373] pt-2 flex-shrink-0">備考</span>
                    <textarea name="comment" class="border border-gray-300 rounded px-2 py-1 w-full m-0 h-[72px] resize-none">{{ $targetRecord->comment }}</textarea>
                </div>
            </div>

            <div class="flex justify-center mt-8">
                <button type="submit" class="bg-black text-white px-16 py-3 rounded-2xl">修正</button>
            </div>
        </form>
    </div>
</div>

<template id="break-row-template">
    <div class="flex items-center border-b border-[#F0EFF2] px-8 py-4 break-row">
        <span class="w-40 text-[#737373]">休憩__INDEX_LABEL__</span>
        <input type="time" name="breaks[__INDEX__][break_in]" class="border border-gray-300 rounded px-2 py-1 w-32">
        <span class="mx-4">〜</span>
        <input type="time" name="breaks[__INDEX__][break_out]" class="border border-gray-300 rounded px-2 py-1 w-32">
    </div>
</template>

<script>
    let breakIndex = {{ $targetRecord->attendanceBreak->count() }};

    document.getElementById('add-break')?.addEventListener('click', function() {
        const template = document.getElementById('break-row-template').innerHTML;
        const indexLabel = breakIndex + 1;
        const html = template
            .replaceAll('__INDEX__', breakIndex)
            .replaceAll('__INDEX_LABEL__', indexLabel);

        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        document.getElementById('break-rows').appendChild(wrapper.firstElementChild);

        breakIndex++;
    });
</script>
@endsection
