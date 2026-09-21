<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $loginInput = trim($request->input('email'));
        $password = $request->input('password');

        // Khusus Akun Resmi HSE: HSEadmin / hsemurinda123
        if (strcasecmp($loginInput, 'HSEadmin') === 0 && $password === 'hsemurinda123') {
            $user = \App\Models\User::firstOrCreate(
                ['name' => 'HSEadmin'],
                ['email' => 'hseadmin@murinda.com', 'password' => \Illuminate\Support\Facades\Hash::make('hsemurinda123')]
            );
            Auth::login($user);
            $request->session()->regenerate();
            $request->session()->put('user_name', 'HSEadmin');
            $request->session()->put('is_logged_in', true);
            return redirect()->route('dashboard')->with('success', 'Selamat datang di Sistem Informasi HSE PT Murinda Iron Steel!');
        }

        // Cek login via Database Auth (bisa email atau name/username)
        if (
            Auth::attempt(['email' => $loginInput, 'password' => $password]) ||
            Auth::attempt(['name' => $loginInput, 'password' => $password])
        ) {
            $request->session()->regenerate();
            $request->session()->put('user_name', Auth::user()->name);
            $request->session()->put('is_logged_in', true);
            return redirect()->route('dashboard')->with('success', 'Selamat datang di Sistem Informasi HSE PT Murinda Iron Steel!');
        }

        // Fallback demo credentials
        if (($loginInput === 'hse.kalijati@murinda.com' && $password === 'password')) {
            $request->session()->regenerate();
            $request->session()->put('user_name', 'Petugas HSE');
            $request->session()->put('is_logged_in', true);
            return redirect()->route('dashboard')->with('success', 'Selamat datang di Sistem Informasi HSE PT Murinda Iron Steel!');
        }

        return back()->withErrors([
            'email' => 'Username / Email atau Password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda telah keluar dari Sistem HSE.');
    }
}
