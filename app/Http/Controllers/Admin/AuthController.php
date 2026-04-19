<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('admin_authenticated')) {
            return redirect()->route('admin.jobs.index');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'password' => ['required'],
        ]);

        $adminId       = env('ADMIN_ID', 'admin');
        $adminPassword = env('ADMIN_PASSWORD', '');

        if (
            $request->input('id') === $adminId &&
            $request->input('password') === $adminPassword
        ) {
            session(['admin_authenticated' => true]);
            return redirect()->route('admin.jobs.index');
        }

        return back()->withErrors(['password' => 'IDまたはパスワードが正しくありません。']);
    }

    public function logout()
    {
        session()->forget('admin_authenticated');
        return redirect()->route('admin.login');
    }
}
