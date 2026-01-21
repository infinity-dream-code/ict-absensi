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
            'notes' => 'nullable|string',
        ]);

        $date = Carbon::parse($request->date);
        
        Holiday::create([
            'date' => $date->format('Y-m-d'),
            'description' => $request->description,
            'notes' => $request->notes,
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

    public function syncFromApi(Request $request)
    {
        $year = $request->filled('year') ? (int) $request->year : Carbon::now()->year;

        try {
            // Fetch data from API
            $response = file_get_contents('https://libur.deno.dev/api');
            
            if ($response === false) {
                return redirect()->route('admin.holiday.index', ['year' => $year])
                    ->with('error', 'Gagal mengambil data dari API!');
            }

            $holidays = json_decode($response, true);
            
            if (!is_array($holidays)) {
                return redirect()->route('admin.holiday.index', ['year' => $year])
                    ->with('error', 'Format data dari API tidak valid!');
            }

            // Filter by year
            $filteredHolidays = array_filter($holidays, function($holiday) use ($year) {
                return Carbon::parse($holiday['date'])->year == $year;
            });

            $imported = 0;
            $skipped = 0;

            foreach ($filteredHolidays as $holidayData) {
                $date = Carbon::parse($holidayData['date']);
                
                // Check if holiday already exists
                $exists = Holiday::where('date', $date->format('Y-m-d'))->exists();
                
                if (!$exists) {
                    Holiday::create([
                        'date' => $date->format('Y-m-d'),
                        'description' => $holidayData['name'],
                        'year' => $date->year,
                    ]);
                    $imported++;
                } else {
                    $skipped++;
                }
            }

            $message = "Berhasil mengimpor {$imported} hari libur.";
            if ($skipped > 0) {
                $message .= " ({$skipped} data sudah ada, dilewati)";
            }

            return redirect()->route('admin.holiday.index', ['year' => $year])
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('admin.holiday.index', ['year' => $year])
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $holiday = Holiday::findOrFail($id);
        $holiday->update([
            'description' => $request->description,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.holiday.index', ['year' => $holiday->year])
            ->with('success', 'Data libur berhasil diperbarui!');
    }
}
