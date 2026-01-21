<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\User;
use Carbon\Carbon;
use App\Exports\AttendanceExport;
use App\Exports\MonthlyAttendanceSummaryExport;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceHistoryController extends Controller
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
        $query = Attendance::with('user')->orderBy('attendance_date', 'desc')->orderBy('check_in', 'desc');

        // Filter by date
        if ($request->filled('date_from')) {
            $query->whereDate('attendance_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('attendance_date', '<=', $request->date_to);
        }

        // Filter by year and month
        if ($request->filled('year') && $request->filled('month')) {
            $query->whereYear('attendance_date', $request->year)
                  ->whereMonth('attendance_date', $request->month);
        } elseif ($request->filled('year')) {
            $query->whereYear('attendance_date', $request->year);
        } elseif ($request->filled('month')) {
            $query->whereMonth('attendance_date', $request->month);
        }

        // Filter by work type
        if ($request->filled('work_type') && $request->work_type !== 'all') {
            $query->where('work_type', $request->work_type);
        }

        // Search by name or NIK
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Get all attendances first
        $allAttendances = $query->get();
        
        // Group by date
        $groupedByDate = $allAttendances->groupBy(function($attendance) {
            return Carbon::parse($attendance->attendance_date)->format('Y-m-d');
        });
        
        // Get unique dates and sort descending
        $dates = $groupedByDate->keys()->sortDesc()->values();
        
        // Paginate dates (per day)
        $perPage = 10; // 10 hari per halaman
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $datesForPage = $dates->slice($offset, $perPage);
        
        // Get attendances for these dates only
        $attendances = collect();
        foreach ($datesForPage as $date) {
            if ($groupedByDate->has($date)) {
                $attendances = $attendances->merge($groupedByDate->get($date));
            }
        }
        
        // Sort by date desc, then by check_in desc
        $attendances = $attendances->sortByDesc(function($attendance) {
            return Carbon::parse($attendance->attendance_date)->format('Y-m-d') . ' ' . 
                   ($attendance->check_in ? Carbon::parse($attendance->check_in)->format('H:i:s') : '00:00:00');
        })->values();

        // Create paginator manually
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $datesForPage->values(),
            $dates->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Get holidays for dates in current page
        $holidaysByDate = [];
        if ($datesForPage->isNotEmpty()) {
            $holidays = Holiday::whereIn('date', $datesForPage->toArray())->get();
            foreach ($holidays as $holiday) {
                $holidaysByDate[$holiday->date->format('Y-m-d')] = $holiday;
            }
        }

        return view('admin.attendance-history.index', compact('attendances', 'paginator', 'datesForPage', 'holidaysByDate'));
    }

    public function export(Request $request)
    {
        $filters = [
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'year' => $request->year,
            'month' => $request->month,
            'work_type' => $request->work_type,
            'search' => $request->search,
        ];

        $filename = 'Export_Absensi_' . date('Y-m-d_His') . '.xlsx';
        
        return Excel::download(new AttendanceExport($filters), $filename);
    }

    public function exportMonthlySummary(Request $request)
    {
        $year = $request->filled('year') && $request->year !== '' ? (int) $request->year : null;
        $month = $request->filled('month') && $request->month !== '' ? (int) $request->month : null;
        
        // Validasi jika dipilih harus valid
        if ($year !== null && ($year < 2020 || $year > 2100)) {
            return back()->withErrors(['year' => 'Tahun harus antara 2020 dan 2100']);
        }
        
        if ($month !== null && ($month < 1 || $month > 12)) {
            return back()->withErrors(['month' => 'Bulan harus antara 1 dan 12']);
        }
        
        // Generate filename
        if ($year && $month) {
            $monthName = Carbon::create($year, $month, 1)->locale('id')->isoFormat('MMMM_YYYY');
            $filename = 'Rekap_Absensi_' . $monthName . '.xlsx';
        } elseif ($year) {
            $filename = 'Rekap_Absensi_Tahun_' . $year . '.xlsx';
        } elseif ($month) {
            $monthName = Carbon::create(null, $month, 1)->locale('id')->isoFormat('MMMM');
            $filename = 'Rekap_Absensi_Bulan_' . $monthName . '.xlsx';
        } else {
            $filename = 'Rekap_Absensi_Semua.xlsx';
        }
        
        return Excel::download(new MonthlyAttendanceSummaryExport($year, $month), $filename);
    }
}
