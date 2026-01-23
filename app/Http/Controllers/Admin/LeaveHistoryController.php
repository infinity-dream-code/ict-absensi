<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;

class LeaveHistoryController extends Controller
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
        $query = Leave::with('user')->orderBy('leave_date', 'desc');

        // Filter by date
        if ($request->filled('date_from')) {
            $query->whereDate('leave_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('leave_date', '<=', $request->date_to);
        }

        // Filter by year and month
        if ($request->filled('year') && $request->filled('month')) {
            $query->whereYear('leave_date', $request->year)
                  ->whereMonth('leave_date', $request->month);
        } elseif ($request->filled('year')) {
            $query->whereYear('leave_date', $request->year);
        } elseif ($request->filled('month')) {
            $query->whereMonth('leave_date', $request->month);
        }

        // Filter by leave type
        if ($request->filled('leave_type') && $request->leave_type !== 'all') {
            $query->where('leave_type', $request->leave_type);
        }

        // Search by name or NIK
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $leaves = $query->paginate(20)->withQueryString();

        return view('admin.leave-history.index', compact('leaves'));
    }

    public function update(Request $request, Leave $leave)
    {
        $request->validate([
            'leave_date' => 'required|date',
            'leave_type' => 'required|in:cuti,izin,sakit',
        ]);

        // Check if the new date already has a leave request for this user (excluding current leave)
        $existing = Leave::where('user_id', $leave->user_id)
            ->where('leave_date', $request->leave_date)
            ->where('id', '!=', $leave->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan sudah memiliki perizinan pada tanggal tersebut!'
            ], 400);
        }

        $leave->update([
            'leave_date' => $request->leave_date,
            'leave_type' => $request->leave_type,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data perizinan berhasil diperbarui!'
        ]);
    }

    public function destroy(Leave $leave)
    {
        $leave->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data perizinan berhasil dihapus!'
        ]);
    }
}
