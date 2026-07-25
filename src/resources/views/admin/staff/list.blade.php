@extends('layouts.admin')

@section('title')
スタッフ一覧
@endsection

@section('content')
<div class="pt-20 w-full bg-[#F0EFF2] min-h-screen">
    <div class="m-auto my-24 w-[900px] flex flex-col justify-center">
        <h1 class="px-8 text-4xl font-bold border-l-12 flex items-center">スタッフ一覧</h1>
        <div class="my-12 border-4 border-[#F0EFF2] rounded-2xl overflow-hidden">
            <table class="w-full text-center font-bold">
                <tr class="bg-white text-[#737373] border-b-8 border-[#F0EFF2]">
                    <th class="py-3">名前</th>
                    <th class="py-3">メールアドレス</th>
                    <th class="py-3">月次勤怠</th>
                </tr>
                @foreach($users as $user)
                <tr class="bg-white text-[#737373] border-b-4 border-[#F0EFF2]">
                    <td class="py-3">{{ $user->name }}</td>
                    <td class="py-3">{{ $user->email }}</td>
                    <td class="py-3">
                        <a href="{{ route('attendance.index', ['id' => $user->id]) }}" class="text-black font-bold hover:underline">詳細</a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
