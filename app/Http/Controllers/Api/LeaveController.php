<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class LeaveController extends Controller
{
    /**
     * Insert leave request via API
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'username_mobile' => 'required|string',
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
            'jenis' => 'required|in:cuti,izin,sakit',
            'keterangan' => 'nullable|string|max:500',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // Find user by username_mobile
        $user = User::where('username_mobile', $request->username_mobile)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User dengan username_mobile tersebut tidak ditemukan.'
            ], 404);
        }

        $dateFrom = Carbon::parse($request->tanggal_awal);
        $dateTo = Carbon::parse($request->tanggal_akhir);

        // Generate array of dates from tanggal_awal to tanggal_akhir
        $dates = [];
        $currentDate = $dateFrom->copy();
        while ($currentDate->lte($dateTo)) {
            $dates[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        // Check if any date already has leave request
        $existing = Leave::where('user_id', $user->id)
            ->whereIn('leave_date', $dates)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'User sudah mengajukan perizinan untuk salah satu tanggal dalam range ini!'
            ], 400);
        }

        // Handle foto upload to Cloudinary
        $attachmentUrl = null;
        if ($request->hasFile('foto')) {
            try {
                $uploadedFile = Cloudinary::upload($request->file('foto')->getRealPath(), [
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
                'user_id' => $user->id,
                'leave_date' => $date,
                'leave_type' => $request->jenis,
                'notes' => $request->keterangan,
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
            'message' => $message,
            'data' => [
                'user_id' => $user->id,
                'username_mobile' => $user->username_mobile,
                'days_inserted' => $dayCount,
                'date_from' => $request->tanggal_awal,
                'date_to' => $request->tanggal_akhir,
                'leave_type' => $request->jenis,
            ]
        ]);
    }
}
