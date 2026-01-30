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
        
        // Ambil semua absensi hari ini yang punya check_in, lalu ambil CHECK-IN PALING AWAL per user
        $todayAttendancesWithCheckIn = Attendance::with('user')
            ->whereDate('attendance_date', $today)
            ->whereNotNull('check_in')
            ->get();
        
        $earliestCheckInPerUser = $todayAttendancesWithCheckIn
            ->groupBy('user_id')
            ->map(function ($attendances) {
                return $attendances->sortBy('check_in')->first();
            });
        
        // Total yang sudah check-in hari ini = jumlah user unik yang punya check-in
        $totalCheckIn = $earliestCheckInPerUser->count();
        
        // Total yang sudah check-out hari ini (distinct user)
        $totalCheckOut = Attendance::whereDate('attendance_date', $today)
            ->whereNotNull('check_out')
            ->select('user_id')
            ->groupBy('user_id')
            ->get()
            ->count();
        
        // Get settings untuk menentukan tepat waktu atau terlambat
        $settings = \App\Models\Setting::getSettings();
        $checkInEndTime = $settings->check_in_end ?: '09:00:00';
        $checkInEnd = Carbon::parse($today->format('Y-m-d') . ' ' . $checkInEndTime, 'Asia/Jakarta');
        
        // Tepat waktu / terlambat dihitung dari CHECK-IN PALING AWAL per user
        $totalOnTime = $earliestCheckInPerUser->filter(function ($attendance) use ($checkInEnd) {
            $checkInTime = Carbon::parse($attendance->check_in, 'Asia/Jakarta');
            return $checkInTime->lte($checkInEnd);
        })->count();
        
        $totalLate = $earliestCheckInPerUser->filter(function ($attendance) use ($checkInEnd) {
            $checkInTime = Carbon::parse($attendance->check_in, 'Asia/Jakarta');
            return $checkInTime->gt($checkInEnd);
        })->count();
        
        // 5 aktivitas terbaru: pakai check-in PALING AWAL per user (satu per user), gabung dengan check-out
        $checkIns = $earliestCheckInPerUser->map(function ($attendance) use ($checkInEnd) {
            $checkInTime = Carbon::parse($attendance->check_in, 'Asia/Jakarta');
            $isLate = $checkInTime->gt($checkInEnd);
            return [
                'user_name' => $attendance->user->name,
                'activity' => 'Check-In',
                'time' => $attendance->check_in,
                'status' => $isLate ? 'Terlambat' : 'Tepat Waktu',
                'is_late' => $isLate,
                'timestamp' => $checkInTime->timestamp
            ];
        });
        
        $checkOuts = Attendance::with('user')
            ->whereDate('attendance_date', $today)
            ->whereNotNull('check_out')
            ->get()
            ->map(function ($attendance) {
                return [
                    'user_name' => $attendance->user->name,
                    'activity' => 'Check-Out',
                    'time' => $attendance->check_out,
                    'status' => 'Selesai',
                    'is_late' => false,
                    'timestamp' => Carbon::parse($attendance->check_out, 'Asia/Jakarta')->timestamp
                ];
            });
        
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
