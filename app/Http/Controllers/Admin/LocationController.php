<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class LocationController extends Controller
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
        return view('admin.location.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'location_name' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|integer|min:10|max:10000',
        ]);

        $settings = Setting::getSettings();
        $settings->update([
            'location_name' => $request->location_name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius ?? 100,
        ]);

        return redirect()->route('admin.location.index')->with('success', 'Pengaturan lokasi berhasil diperbarui!');
    }
}
