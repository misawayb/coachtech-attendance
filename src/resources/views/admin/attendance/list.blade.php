@extends('layouts.admin')

@section('title')
勤怠一覧
@endsection

@section('content')
<div class="pt-20 w-full bg-[#F0EFF2] min-h-screen">
    <div class="m-auto my-24 w-[900px] flex flex-col justify-center">
        <h1 class="px-8 text-4xl font-bold border-l-12 flex items-center">{{ $targetDate->isoFormat('YYYY年M月D日') }}の勤怠</h1>
        <div class="my-12 flex items-center h-[60px] bg-white border-4 border-[#F0EFF2] rounded-2xl justify-between px-8 py-4">
            <a href="{{ route('admin.index', ['date' => $targetDate->copy()->subDay()->format('Y-m-d')]) }}" class="font-bold text-[#737373] tracking-widest hover:underline">←前日</a>
            <span class="font-bold text-2xl">{{ $targetDate->format('Y/m/d') }}</span>
            <a href="{{ route('admin.index', ['date' => $targetDate->copy()->addDay()->format('Y-m-d')]) }}" class="font-bold text-[#737373] tracking-widest hover:underline">翌日→</a>
        </div>
        <div class="border-4 border-[#F0EFF2] rounded-2xl overflow-hidden">
            <table class="w-full text-center font-bold">
                <tr class="bg-white text-[#737373] border-b-8 border-[#F0EFF2]">
                    <th class="py-3">名前</th>
                    <th class="py-3">出勤</th>
                    <th class="py-3">退勤</th>
                    <th class="py-3">休憩</th>
                    <th class="py-3">合計</th>
                    <th class="py-3">詳細</th>
                </tr>
                @foreach($attendanceList as $item)
                <tr class="bg-white text-[#737373] border-b-4 border-[#F0EFF2]">
                    <td class="py-3">{{ $item['name'] }}</td>
                    <td class="py-3">{{ $item['clockIn'] }}</td>
                    <td class="py-3">{{ $item['clockOut'] }}</td>
                    <td class="py-3">{{ $item['breakTime'] }}</td>
                    <td class="py-3">{{ $item['workTime'] }}</td>
                    <td class="py-3">
                        @if($item['detailUrl'])
                        <a href="{{ $item['detailUrl'] }}" class="text-black font-bold hover:underline">詳細</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
