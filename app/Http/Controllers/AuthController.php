<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('attendance.index');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nik' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('nik', $request->nik)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // Prevent admin from logging in through employee login
            if ($user->role === 'admin') {
                return back()->withErrors([
                    'nik' => 'Silakan login melalui halaman admin.',
                ])->onlyInput('nik');
            }
            
            Auth::login($user, $request->filled('remember'));
            $request->session()->regenerate();
            return redirect()->intended(route('attendance.index'))->with('success', 'Login berhasil!');
        }

        return back()->withErrors([
            'nik' => 'NIK atau password tidak valid.',
        ])->onlyInput('nik');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Logout berhasil!');
    }
}
