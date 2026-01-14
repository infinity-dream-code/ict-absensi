<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Leave;
use Carbon\Carbon;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class LeaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return $next($request);
        });
    }

    public function index()
    {
        return view('leave.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_date_from' => 'required|date',
            'leave_date_to' => 'required|date|after_or_equal:leave_date_from',
            'leave_type' => 'required|in:cuti,izin,sakit',
            'notes' => 'nullable|string|max:500',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $dateFrom = Carbon::parse($request->leave_date_from);
        $dateTo = Carbon::parse($request->leave_date_to);

        // Generate array of dates from date_from to date_to
        $dates = [];
        $currentDate = $dateFrom->copy();
        while ($currentDate->lte($dateTo)) {
            $dates[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        // Check if any date already has leave request
        $existing = Leave::where('user_id', Auth::id())
            ->whereIn('leave_date', $dates)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah mengajukan perizinan untuk salah satu tanggal dalam range ini!'
            ], 400);
        }

        // Handle attachment upload to Cloudinary
        $attachmentUrl = null;
        if ($request->hasFile('attachment')) {
            try {
                $uploadedFile = Cloudinary::upload($request->file('attachment')->getRealPath(), [
                    'folder' => 'leave_attachments',
                    'resource_type' => 'image'
                ]);
                $attachmentUrl = $uploadedFile->getSecurePath();
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupload foto: ' . $e->getMessage()
                ], 500);
            }
        }

        // Insert one row per date
        $inserted = 0;
        foreach ($dates as $date) {
            Leave::create([
                'user_id' => Auth::id(),
                'leave_date' => $date,
                'leave_type' => $request->leave_type,
                'notes' => $request->notes,
                'attachment' => $attachmentUrl,
            ]);
            $inserted++;
        }

        $dayCount = count($dates);
        $message = $dayCount == 1 
            ? 'Perizinan berhasil diajukan!'
            : "Perizinan berhasil diajukan untuk {$dayCount} hari!";

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
}
