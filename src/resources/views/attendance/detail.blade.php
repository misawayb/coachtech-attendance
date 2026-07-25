@extends('layouts.app')

@section('title')
勤怠詳細
@endsection

@section('content')
<div class="pt-20 w-full bg-[#F0EFF2] min-h-screen">
    <div class="m-auto my-24 w-[900px] flex flex-col justify-center">
        <h1 class="px-8 text-4xl font-bold border-l-12 flex items-center">勤怠詳細</h1>

        <form action="{{ route('request.store', ['date' => $targetDate->format('Y-m-d')]) }}" method="post" class="mt-12 font-bold">
            @csrf

            @if ($isPending)
            <p class="text-black mb-4 text-right">＊申請中の内容を表示しています。</p>
            @endif

            <div class="bg-white rounded-2xl overflow-hidden">
                <div class="flex border-b border-[#F0EFF2] px-8 py-8">
                    <span class="w-40 text-[#737373]">名前</span>
                    <span class="pl-2">{{ $targetRecord->user->name }}</span>
                </div>

                <div class="flex border-b border-[#F0EFF2] px-8 py-8">
                    <span class="w-40 text-[#737373]">日付</span>
                    <span class="pl-2">{{ $targetDate->format('Y年') }}</span>
                    <span class="ml-8">{{ $targetDate->format('n月j日') }}</span>
                </div>

                <div class="flex items-start border-b border-[#F0EFF2] px-8 py-8">
                    <span class="w-40 text-[#737373] pt-1">出勤・退勤</span>
                    <div class="flex flex-col">
                        <div class="flex items-center">
                            <input type="time" name="clock_in" value="{{ old('clock_in', $displayClockIn ? \Carbon\Carbon::parse($displayClockIn)->format('H:i') : '') }}" class="border border-gray-300 rounded px-2 py-1 w-32" {{ $isPending ? 'disabled' : '' }}>
                            <span class="mx-4">〜</span>
                            <input type="time" name="clock_out" value="{{ old('clock_out', $displayClockOut ? \Carbon\Carbon::parse($displayClockOut)->format('H:i') : '') }}" class="border border-gray-300 rounded px-2 py-1 w-32" {{ $isPending ? 'disabled' : '' }}>
                        </div>
                        @error('clock_in')
                        <p class="text-red-500 pt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>


                @php
                    $breaksInput = old('breaks');
                    if ($breaksInput === null) {
                        $breaksInput = $displayBreaks->map(fn($break) => [
                            'break_in' => $break->break_in ? \Carbon\Carbon::parse($break->break_in)->format('H:i') : '',
                            'break_out' => $break->break_out ? \Carbon\Carbon::parse($break->break_out)->format('H:i') : '',
                        ])->toArray();
                    }
                @endphp

                <div id="break-rows">
                    @foreach ($breaksInput as $index => $break)
                    <div class="flex items-start border-b border-[#F0EFF2] px-8 py-8 break-row">
                        <span class="w-40 text-[#737373] pt-1">休憩{{ $index + 1 > 1 ? $index + 1 : '' }}</span>
                        <div class="flex flex-col">
                            <div class="flex items-center">
                                <input type="time" name="breaks[{{ $index }}][break_in]" value="{{ $break['break_in'] ?? '' }}" class="border border-gray-300 rounded px-2 py-1 w-32" {{ $isPending ? 'disabled' : '' }}>
                                <span class="mx-4">〜</span>
                                <input type="time" name="breaks[{{ $index }}][break_out]" value="{{ $break['break_out'] ?? '' }}" class="border border-gray-300 rounded px-2 py-1 w-32" {{ $isPending ? 'disabled' : '' }}>
                            </div>
                            @error("breaks.$index.break_in")
                            <p class="text-red-500 pt-2">{{ $message }}</p>
                            @enderror
                            @error("breaks.$index.break_out")
                            <p class="text-red-500 pt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    @endforeach
                </div>

                @unless ($isPending)
                <div class="px-8 py-8 border-b border-[#F0EFF2]">
                    <button type="button" id="add-break" class="text-[#737373] hover:underline">＋ 休憩を追加</button>
                </div>
                @endunless

                <div class="flex px-8 py-8 border-[#F0EFF2]">
                    <span class="w-40 text-[#737373] pt-2 flex-shrink-0">備考</span>
                    <div class="flex flex-col w-full">
                        <textarea name="comment" class="border border-gray-300 rounded px-2 py-1 w-full m-0 h-[72px] resize-none" {{ $isPending ? 'disabled' : '' }}>{{ old('comment', $displayComment) }}</textarea>
                        @error('comment')
                        <p class="text-red-500 pt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            @if ($isPending)
            <p class="text-red-500 mt-4 text-right">＊承認待ちのため修正はできません。</p>
            @else
            <div class="flex justify-center mt-8">
                <button type="submit" class="bg-black text-white  px-16 py-3 rounded-2xl">修正</button>
            </div>
            @endif
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
    let breakIndex = {{ count($breaksInput) }};

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