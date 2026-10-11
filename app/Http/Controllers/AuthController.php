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

        // Khusus Akun Resmi HSE Admin: HSEadmin / hsemurinda123
        if (strcasecmp($loginInput, 'HSEadmin') === 0 && $password === 'hsemurinda123') {
            $user = \App\Models\User::firstOrCreate(
                ['name' => 'HSEadmin'],
                [
                    'email' => 'hseadmin@murinda.com',
                    'password' => \Illuminate\Support\Facades\Hash::make('hsemurinda123'),
                    'role' => 'admin'
                ]
            );
            if ($user->role !== 'admin') {
                $user->update(['role' => 'admin']);
            }
            Auth::login($user);
            $request->session()->regenerate();
            $request->session()->put('user_name', 'HSEadmin');
            $request->session()->put('user_role', 'admin');
            $request->session()->put('is_read_only', false);
            $request->session()->put('is_logged_in', true);
            return redirect()->route('dashboard')->with('success', 'Selamat datang di Sistem Informasi HSE PT Murinda Iron Steel!');
        }

        // Khusus Akun Resmi HSE Manager (Read-Only): HSEManager / hsemurinda123
        if (strcasecmp($loginInput, 'HSEManager') === 0 && $password === 'hsemurinda123') {
            $user = \App\Models\User::firstOrCreate(
                ['name' => 'HSEManager'],
                [
                    'email' => 'hsemanager@murinda.com',
                    'password' => \Illuminate\Support\Facades\Hash::make('hsemurinda123'),
                    'role' => 'manager'
                ]
            );
            if ($user->role !== 'manager') {
                $user->update(['role' => 'manager']);
            }
            Auth::login($user);
            $request->session()->regenerate();
            $request->session()->put('user_name', 'HSEManager');
            $request->session()->put('user_role', 'manager');
            $request->session()->put('is_read_only', true);
            $request->session()->put('is_logged_in', true);
            return redirect()->route('dashboard')->with('success', 'Selamat datang Bapak/Ibu HSE Manager di Sistem Informasi HSE (Mode Read-Only)!');
        }

        // Cek login via Database Auth (bisa email atau name/username)
        if (
            Auth::attempt(['email' => $loginInput, 'password' => $password]) ||
            Auth::attempt(['name' => $loginInput, 'password' => $password])
        ) {
            $user = Auth::user();
            $isManager = ($user->role === 'manager' || strcasecmp($user->name, 'HSEManager') === 0);
            $request->session()->regenerate();
            $request->session()->put('user_name', $user->name);
            $request->session()->put('user_role', $isManager ? 'manager' : ($user->role ?? 'admin'));
            $request->session()->put('is_read_only', $isManager);
            $request->session()->put('is_logged_in', true);
            return redirect()->route('dashboard')->with('success', 'Selamat datang di Sistem Informasi HSE PT Murinda Iron Steel!');
        }

        // Fallback demo credentials
        if (($loginInput === 'hse.kalijati@murinda.com' && $password === 'password')) {
            $request->session()->regenerate();
            $request->session()->put('user_name', 'Petugas HSE');
            $request->session()->put('user_role', 'admin');
            $request->session()->put('is_read_only', false);
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
