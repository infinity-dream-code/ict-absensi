<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Holiday;
use Carbon\Carbon;

class HolidayController extends Controller
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
        $year = $request->filled('year') ? (int) $request->year : Carbon::now()->year;

        $holidays = Holiday::where('year', $year)
            ->orderBy('date', 'asc')
            ->get();

        $availableYears = Holiday::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (!in_array($year, $availableYears)) {
            $availableYears[] = $year;
            sort($availableYears);
            $availableYears = array_reverse($availableYears);
        }

        return view('admin.holiday.index', compact('holidays', 'year', 'availableYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|unique:holidays,date',
            'description' => 'required|string|max:255',
        ]);

        $date = Carbon::parse($request->date);
        
        Holiday::create([
            'date' => $date->format('Y-m-d'),
            'description' => $request->description,
            'year' => $date->year,
        ]);

        return redirect()->route('admin.holiday.index', ['year' => $date->year])
            ->with('success', 'Libur berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $holiday = Holiday::findOrFail($id);
        $year = $holiday->year;
        $holiday->delete();

        return redirect()->route('admin.holiday.index', ['year' => $year])
            ->with('success', 'Libur berhasil dihapus!');
    }
}
