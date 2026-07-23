@extends('layouts.app')

@section('title')
申請一覧
@endsection

@section('content')
<div class="pt-20 w-full bg-[#F0EFF2] min-h-screen">
    <div class="m-auto my-24 w-[900px] flex flex-col justify-center">
        <h1 class="px-8 text-4xl font-bold border-l-12 flex items-center">申請一覧</h1>

        <div class="mt-12 flex border-b ">
            <a href="{{ route('request.index', ['status' => 'pending']) }}"
                class="px-6 py-3 font-bold hover:underline {{ $status === 'pending' ? 'text-black' : 'text-gray-400' }}">
                承認待ち
            </a>
            <a href="{{ route('request.index', ['status' => 'approved']) }}"
                class="px-6 py-3 font-bold hover:underline {{ $status === 'approved' ? 'text-black' : 'text-gray-400' }}">
                承認済み
            </a>
        </div>

        <div class="mt-12 rounded-2xl overflow-hidden">
            <table class="w-full text-center font-bold">
                <tr class="bg-white text-[#737373] border-b-6 border-[#F0EFF2]">
                    <th class="py-3">状態</th>
                    <th class="py-3">名前</th>
                    <th class="py-3">対象日時</th>
                    <th class="py-3">申請理由</th>
                    <th class="py-3">申請日時</th>
                    <th class="py-3">詳細</th>
                </tr>
                @foreach ($requestList as $item)
                <tr class="bg-white text-[#737373] border-b-4 border-[#F0EFF2] last:border-b-4-0">
                    <td class="py-3">{{ $item['statusLabel'] }}</td>
                    <td class="py-3">{{ $item['userName'] }}</td>
                    <td class="py-3">{{ $item['targetDate'] }}</td>
                    <td class="py-3">{{ $item['comment'] }}</td>
                    <td class="py-3">{{ $item['requestedAt'] }}</td>
                    <td class="py-3">
                        <a href="{{ $item['detailUrl'] }}" class="text-black hover:underline">詳細</a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection