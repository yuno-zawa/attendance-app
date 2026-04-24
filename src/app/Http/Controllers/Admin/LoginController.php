<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth()->guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/admin/attendance');
        }

        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }

    public function create()
    {
        return view('auth.login', [
            'title' => '管理者ログイン',
            'action' => '/admin/login',
            'button' => '管理者ログインする',
            'isAdmin' => true,
        ]);
    }
}
