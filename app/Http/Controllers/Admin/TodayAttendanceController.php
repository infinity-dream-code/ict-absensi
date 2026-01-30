<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Leave;
use Carbon\Carbon;

class TodayAttendanceController extends Controller
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

    public function index(Request $request)
    {
        // Filter tanggal: default hari ini, bisa pilih tanggal lain (dalam 1 bulan atau kapan saja)
        $selectedDate = $request->filled('date')
            ? Carbon::parse($request->date, 'Asia/Jakarta')->startOfDay()
            : Carbon::today('Asia/Jakarta');

        $totalEmployees = User::where('role', 'user')->count();

        // User ID yang sudah absen (punya attendance) di tanggal tersebut - pakai earliest check_in per user
        $attendanceRecords = Attendance::whereDate('attendance_date', $selectedDate)
            ->whereNotNull('check_in')
            ->get();
        $presentUserIds = $attendanceRecords
            ->groupBy('user_id')
            ->map(fn ($rows) => $rows->sortBy('check_in')->first())
            ->keys()
            ->flip()
            ->all();

        // User ID yang izin/leave di tanggal tersebut
        $leaveUserIds = Leave::whereDate('leave_date', $selectedDate)
            ->pluck('user_id')
            ->unique()
            ->flip()
            ->all();

        // Hitung: Sudah Absen, Izin, Tidak Absen (prioritas: jika izin = On Leave; jika ada absen = Present; else Absent)
        $countPresent = 0;
        $countOnLeave = 0;
        $countAbsent = 0;
        $employeeCards = [];

        $employees = User::where('role', 'user')->orderBy('name')->get();

        foreach ($employees as $user) {
            $userId = $user->id;
            $hasLeave = isset($leaveUserIds[$userId]);
            $hasAttendance = isset($presentUserIds[$userId]);

            if ($hasLeave) {
                $countOnLeave++;
                $leaveRecord = Leave::where('user_id', $userId)->whereDate('leave_date', $selectedDate)->first();
                $leaveTypeLabel = $leaveRecord ? ($leaveRecord->leave_type === 'cuti' ? 'Cuti' : ($leaveRecord->leave_type === 'sakit' ? 'Sakit' : 'Izin')) : '-';
                $employeeCards[] = [
                    'user' => $user,
                    'status' => 'leave',
                    'label' => 'Izin',
                    'detail' => 'Jenis: ' . $leaveTypeLabel,
                ];
            } elseif ($hasAttendance) {
                $countPresent++;
                $att = $attendanceRecords->where('user_id', $userId)->sortBy('check_in')->first();
                $checkInTime = $att && $att->check_in
                    ? Carbon::parse($att->check_in, 'Asia/Jakarta')->format('H:i')
                    : '-';
                $employeeCards[] = [
                    'user' => $user,
                    'status' => 'present',
                    'label' => 'Sudah Absen',
                    'detail' => 'Check In: ' . $checkInTime,
                    'work_type' => $att ? $att->work_type : '-',
                ];
            } else {
                $countAbsent++;
                $employeeCards[] = [
                    'user' => $user,
                    'status' => 'absent',
                    'label' => 'Tidak Absen',
                    'detail' => 'Tidak ada catatan absensi',
                ];
            }
        }

        return view('admin.today-attendance.index', compact(
            'selectedDate',
            'totalEmployees',
            'countPresent',
            'countOnLeave',
            'countAbsent',
            'employeeCards'
        ));
    }
}
