@extends('layouts.admin')

@section('title')
修正申請勤怠詳細
@endsection

@section('content')
<div class="pt-20 w-full bg-[#F0EFF2] min-h-screen">
    <div class="m-auto my-24 w-[900px] flex flex-col justify-center">
        <h1 class="px-8 text-4xl font-bold border-l-12 flex items-center">勤怠詳細</h1>

        <div class="mt-12 font-bold">
            <div class="bg-white rounded-2xl overflow-hidden">
                <div class="flex border-b border-[#F0EFF2] px-8 py-8">
                    <span class="w-40 text-[#737373]">名前</span>
                    <span class="pl-2">{{ $correctRequest->attendanceRecord->user->name }}</span>
                </div>

                <div class="flex border-b border-[#F0EFF2] px-8 py-8">
                    <span class="w-40 text-[#737373]">日付</span>
                    <span class="pl-2">{{ \Carbon\Carbon::parse($correctRequest->attendanceRecord->date)->format('Y年') }}</span>
                    <span class="ml-8">{{ \Carbon\Carbon::parse($correctRequest->attendanceRecord->date)->format('n月j日') }}</span>
                </div>

                <div class="flex items-center border-b border-[#F0EFF2] px-8 py-8">
                    <span class="w-40 text-[#737373]">出勤・退勤</span>
                    <span class="pl-2">{{ $correctRequest->clock_in ? \Carbon\Carbon::parse($correctRequest->clock_in)->format('H:i') : '' }}</span>
                    <span class="mx-4">〜</span>
                    <span>{{ $correctRequest->clock_out ? \Carbon\Carbon::parse($correctRequest->clock_out)->format('H:i') : '' }}</span>
                </div>

                @forelse ($correctRequest->attendanceCorrectBreak as $index => $break)
                <div class="flex items-center border-b border-[#F0EFF2] px-8 py-8">
                    <span class="w-40 text-[#737373]">休憩{{ $index + 1 > 1 ? $index + 1 : '' }}</span>
                    <span class="pl-2">{{ $break->break_in ? \Carbon\Carbon::parse($break->break_in)->format('H:i') : '' }}</span>
                    <span class="mx-4">〜</span>
                    <span>{{ $break->break_out ? \Carbon\Carbon::parse($break->break_out)->format('H:i') : '' }}</span>
                </div>
                @empty
                <div class="flex items-center border-b border-[#F0EFF2] px-8 py-8">
                    <span class="w-40 text-[#737373]">休憩</span>
                </div>
                @endforelse

                <div class="flex px-8 py-8 border-[#F0EFF2]">
                    <span class="w-40 text-[#737373] pt-2 flex-shrink-0">備考</span>
                    <span class="pl-2 pt-2">{{ $correctRequest->comment }}</span>
                </div>
            </div>

            <div class="flex justify-end mt-8">
                @if ($correctRequest->status === \App\Enums\CorrectRequestStatus::Pending->value)
                <form action="{{ route('correction.update', ['attendance_correct_request_id' => $correctRequest->id]) }}" method="post">
                    @csrf
                    <button type="submit" class="bg-black text-white px-16 py-3 rounded-2xl hover:cursor-pointer">承認</button>
                </form>
                @else
                <button type="button" disabled class="bg-gray-400 text-white px-16 py-3 rounded-2xl cursor-not-allowed">承認済み</button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
