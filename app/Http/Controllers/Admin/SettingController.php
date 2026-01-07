<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                return redirect()->route('admin.login');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $settings = Setting::getSettings();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'check_in_start' => 'required|date_format:H:i',
            'check_in_end' => 'required|date_format:H:i',
            'check_out_start' => 'required|date_format:H:i',
            'check_out_end' => 'required|date_format:H:i',
        ], [], [
            'check_in_start' => 'Waktu Mulai Check-In',
            'check_in_end' => 'Waktu Akhir Check-In',
            'check_out_start' => 'Waktu Mulai Check-Out',
            'check_out_end' => 'Waktu Akhir Check-Out',
        ]);

        // Custom validation
        if (strtotime($request->check_in_end) <= strtotime($request->check_in_start)) {
            return back()->withErrors(['check_in_end' => 'Waktu akhir check-in harus setelah waktu mulai check-in'])->withInput();
        }

        if (strtotime($request->check_out_end) <= strtotime($request->check_out_start)) {
            return back()->withErrors(['check_out_end' => 'Waktu akhir check-out harus setelah waktu mulai check-out'])->withInput();
        }

        $settings = Setting::getSettings();
        $settings->update([
            'check_in_start' => $request->check_in_start . ':00',
            'check_in_end' => $request->check_in_end . ':00',
            'check_out_start' => $request->check_out_start . ':00',
            'check_out_end' => $request->check_out_end . ':00',
        ]);

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan waktu berhasil diperbarui!');
    }
}
