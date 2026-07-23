@extends('layouts.app')

@section('title')
勤怠一覧
@endsection

@section('content')
<div class="pt-20 w-full bg-[#F0EFF2] min-h-screen">
    <div class="m-auto my-24 w-[900px] flex flex-col justify-center ">
        <h1 class="px-8  text-4xl font-bold border-l-12 flex items-center">勤怠一覧</h1>
        <div class="my-12 flex items-center h-[60px] bg-white border-4 border-[#F0EFF2] rounded-2xl  justify-between px-8 py-4">
            <a href="{{ route('attendance.show', ['month' => $targetMonth->copy()->subMonth()->format('Y-m')]) }}" class="font-bold text-[#737373] tracking-widest hover:underline">←前月</a>
            <span class="font-bold text-2xl">{{ $targetMonth->format('Y/m') }}</span>
            <a href="{{ route('attendance.show', ['month' => $targetMonth->copy()->addMonth()->format('Y-m')]) }}" class="font-bold text-[#737373] tracking-widest hover:underline">翌月→</a>
        </div>
        <div class="border-4 border-[#F0EFF2] rounded-2xl overflow-hidden">
            <table class="w-full text-center font-bold">
                <tr class="bg-white text-[#737373] border-b-8 border-[#F0EFF2]">
                    <th class="py-3">日付</th>
                    <th class="py-3">出勤</th>
                    <th class="py-3">退勤</th>
                    <th class="py-3">休憩</th>
                    <th class="py-3">合計</th>
                    <th class="py-3">詳細</th>
                </tr>
                @foreach($attendanceList as $item)
                <tr class="bg-white text-[#737373] border-b-4 border-[#F0EFF2]">
                    <td class="py-3">{{ $item['date'] }}</td>
                    <td class="py-3">{{ $item['clockIn'] }}</td>
                    <td class="py-3">{{ $item['clockOut'] }}</td>
                    <td class="py-3">{{ $item['breakTime'] }}</td>
                    <td class="py-3">{{ $item['workTime'] }}</td>
                    <td class="py-3">
                        <a href="{{ $item['editUrl'] }}" class="text-black font-bold hover:underline">詳細</a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>




@endsection