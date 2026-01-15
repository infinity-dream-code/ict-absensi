<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Attendance;
use App\Models\Leave;
use Carbon\Carbon;

class HistoryController extends Controller
{
    public function __construct()
    {
        // Middleware handled at route level (api.key or auth:api)
    }

    /**
     * Get attendance history for authenticated user
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAttendanceHistory(Request $request)
    {
        // Check if authenticated via static API key
        if ($request->has('_api_key_authenticated')) {
            // For static API key, user_id must be provided
            $userId = $request->input('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'user_id parameter is required when using static API key.'
                ], 400);
            }
            $user = \App\Models\User::find($userId);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.'
                ], 404);
            }
        } else {
            // Use JWT authentication
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please provide valid JWT token or static API key.'
                ], 401);
            }
        }

        $query = Attendance::where('user_id', $user->id)
            ->orderBy('attendance_date', 'desc')
            ->orderBy('check_in', 'desc');

        // Filter by date range
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

        // Pagination
        $perPage = $request->get('per_page', 20);
        $attendances = $query->paginate($perPage);

        // Format response
        $data = $attendances->map(function ($attendance) {
            return [
                'id' => $attendance->id,
                'attendance_date' => $attendance->attendance_date->format('Y-m-d'),
                'work_type' => $attendance->work_type,
                'check_in' => $attendance->check_in ? $attendance->check_in->format('Y-m-d H:i:s') : null,
                'check_out' => $attendance->check_out ? $attendance->check_out->format('Y-m-d H:i:s') : null,
                'notes' => $attendance->notes,
                'image' => $attendance->image,
                'location_name' => $attendance->location_name,
                'location_valid' => $attendance->location_valid,
                'latitude' => $attendance->latitude,
                'longitude' => $attendance->longitude,
                'created_at' => $attendance->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $attendances->currentPage(),
                'last_page' => $attendances->lastPage(),
                'per_page' => $attendances->perPage(),
                'total' => $attendances->total(),
                'from' => $attendances->firstItem(),
                'to' => $attendances->lastItem(),
            ],
        ]);
    }

    /**
     * Get leave history for authenticated user
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLeaveHistory(Request $request)
    {
        // Check if authenticated via static API key
        if ($request->has('_api_key_authenticated')) {
            // For static API key, user_id must be provided
            $userId = $request->input('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'user_id parameter is required when using static API key.'
                ], 400);
            }
            $user = \App\Models\User::find($userId);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.'
                ], 404);
            }
        } else {
            // Use JWT authentication
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please provide valid JWT token or static API key.'
                ], 401);
            }
        }

        $query = Leave::where('user_id', $user->id)
            ->orderBy('leave_date', 'desc');

        // Filter by date range
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

        // Pagination
        $perPage = $request->get('per_page', 20);
        $leaves = $query->paginate($perPage);

        // Format response
        $data = $leaves->map(function ($leave) {
            return [
                'id' => $leave->id,
                'leave_date' => $leave->leave_date->format('Y-m-d'),
                'leave_type' => $leave->leave_type,
                'leave_type_label' => ucfirst($leave->leave_type),
                'notes' => $leave->notes,
                'attachment' => $leave->attachment,
                'created_at' => $leave->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $leaves->currentPage(),
                'last_page' => $leaves->lastPage(),
                'per_page' => $leaves->perPage(),
                'total' => $leaves->total(),
                'from' => $leaves->firstItem(),
                'to' => $leaves->lastItem(),
            ],
        ]);
    }
}
