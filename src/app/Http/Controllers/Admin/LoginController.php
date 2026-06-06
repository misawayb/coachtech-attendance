<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;

class LoginController extends Controller
{
    public function index()
    {
        return view('admin.login');
    }

    public function store(AdminLoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => 'ログイン情報が登録されていません',
            ]);
        }

        if (!Auth::user()->admin_status) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => '管理者権限がありません',
            ]);
        }

        return redirect('/admin/attendance/list');
    }
}
