<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class DashboardController extends Controller
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
        $today = Carbon::today('Asia/Jakarta');
        
        // Total karyawan
        $totalEmployees = User::where('role', 'user')->count();
        
        // Total yang sudah check-in hari ini
        $totalCheckIn = Attendance::whereDate('attendance_date', $today)
            ->whereNotNull('check_in')
            ->count();
        
        // Total yang sudah check-out hari ini
        $totalCheckOut = Attendance::whereDate('attendance_date', $today)
            ->whereNotNull('check_out')
            ->count();
        
        // Get settings untuk menentukan tepat waktu atau terlambat
        $settings = \App\Models\Setting::getSettings();
        $checkInEndTime = $settings->check_in_end ?: '09:00:00';
        $checkInEnd = Carbon::parse($today->format('Y-m-d') . ' ' . $checkInEndTime, 'Asia/Jakarta');
        
        // Total tepat waktu (check-in sebelum atau sama dengan check_in_end)
        $totalOnTime = Attendance::whereDate('attendance_date', $today)
            ->whereNotNull('check_in')
            ->get()
            ->filter(function($attendance) use ($checkInEnd) {
                $checkInTime = Carbon::parse($attendance->check_in, 'Asia/Jakarta');
                return $checkInTime->lte($checkInEnd);
            })
            ->count();
        
        // Total terlambat (check-in setelah check_in_end)
        $totalLate = Attendance::whereDate('attendance_date', $today)
            ->whereNotNull('check_in')
            ->get()
            ->filter(function($attendance) use ($checkInEnd) {
                $checkInTime = Carbon::parse($attendance->check_in, 'Asia/Jakarta');
                return $checkInTime->gt($checkInEnd);
            })
            ->count();
        
        // Get 5 aktivitas terbaru (check-in dan check-out)
        $recentActivities = collect();
        
        // Get check-ins hari ini
        $checkIns = Attendance::with('user')
            ->whereDate('attendance_date', $today)
            ->whereNotNull('check_in')
            ->get()
            ->map(function($attendance) use ($checkInEnd) {
                $checkInTime = Carbon::parse($attendance->check_in, 'Asia/Jakarta');
                $isLate = $checkInTime->gt($checkInEnd);
                
                return [
                    'user_name' => $attendance->user->name,
                    'activity' => 'Check-In',
                    'time' => $attendance->check_in,
                    'status' => $isLate ? 'Terlambat' : 'Tepat Waktu',
                    'is_late' => $isLate,
                    'timestamp' => Carbon::parse($attendance->check_in, 'Asia/Jakarta')->timestamp
                ];
            });
        
        // Get check-outs hari ini
        $checkOuts = Attendance::with('user')
            ->whereDate('attendance_date', $today)
            ->whereNotNull('check_out')
            ->get()
            ->map(function($attendance) {
                return [
                    'user_name' => $attendance->user->name,
                    'activity' => 'Check-Out',
                    'time' => $attendance->check_out,
                    'status' => 'Selesai',
                    'is_late' => false,
                    'timestamp' => Carbon::parse($attendance->check_out, 'Asia/Jakarta')->timestamp
                ];
            });
        
        // Gabungkan dan urutkan berdasarkan timestamp terbaru
        $recentActivities = $checkIns->merge($checkOuts)
            ->sortByDesc('timestamp')
            ->take(5)
            ->values();
        
        return view('admin.dashboard.index', compact(
            'totalEmployees',
            'totalCheckIn',
            'totalCheckOut',
            'totalOnTime',
            'totalLate',
            'recentActivities'
        ));
    }
}
