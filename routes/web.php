<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\EmployeeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// CSRF Token Route untuk auto-refresh
Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
});

// Route untuk clear session message setelah ditampilkan
Route::post('/clear-session-message', function (Illuminate\Http\Request $request) {
    $type = $request->input('type');
    if ($type === 'success') {
        $request->session()->forget('success');
    } elseif ($type === 'error') {
        $request->session()->forget('error');
    }
    return response()->json(['success' => true]);
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get'); // Fallback untuk expired token

// Attendance Routes (Protected - Only for Employees)
Route::middleware(['auth'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/checkin', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/checkout', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');

    // Leave Routes
    Route::get('/leave', [LeaveController::class, 'index'])->name('leave.index');
    Route::post('/leave', [LeaveController::class, 'store'])->name('leave.store');

    // Profile Routes
    Route::get('/profile/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('profile.change-password');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword']);
    Route::get('/profile/change-username', [ProfileController::class, 'showChangeUsernameForm'])->name('profile.change-username');
    Route::post('/profile/change-username', [ProfileController::class, 'changeUsername']);
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Auth
    Route::get('/ict-login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/ict-login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AdminAuthController::class, 'logout'])->name('logout.get'); // Fallback untuk expired token

    // Admin Protected Routes
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/today-attendance', [\App\Http\Controllers\Admin\TodayAttendanceController::class, 'index'])->name('today-attendance.index');
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::resource('employees', EmployeeController::class);
        Route::post('/employees/{employee}/reset-password', [EmployeeController::class, 'resetPassword'])->name('employees.reset-password');
        Route::get('/attendance-history', [\App\Http\Controllers\Admin\AttendanceHistoryController::class, 'index'])->name('attendance-history.index');
        Route::get('/attendance-history/export', [\App\Http\Controllers\Admin\AttendanceHistoryController::class, 'export'])->name('attendance-history.export');
        Route::get('/attendance-history/export-monthly', [\App\Http\Controllers\Admin\AttendanceHistoryController::class, 'exportMonthlySummary'])->name('attendance-history.export-monthly');
        Route::get('/attendance-history/{attendanceId}/logs', [\App\Http\Controllers\Admin\AttendanceHistoryController::class, 'getLogs'])->name('attendance-history.logs');
        Route::get('/leave-history', [\App\Http\Controllers\Admin\LeaveHistoryController::class, 'index'])->name('leave-history.index');
        Route::put('/leave-history/{leave}', [\App\Http\Controllers\Admin\LeaveHistoryController::class, 'update'])->name('leave-history.update');
        Route::delete('/leave-history/{leave}', [\App\Http\Controllers\Admin\LeaveHistoryController::class, 'destroy'])->name('leave-history.destroy');
        Route::get('/location-settings', [\App\Http\Controllers\Admin\LocationController::class, 'index'])->name('location.index');
        Route::put('/location-settings', [\App\Http\Controllers\Admin\LocationController::class, 'update'])->name('location.update');
        Route::get('/holiday', [\App\Http\Controllers\Admin\HolidayController::class, 'index'])->name('holiday.index');
        Route::post('/holiday', [\App\Http\Controllers\Admin\HolidayController::class, 'store'])->name('holiday.store');
        Route::post('/holiday/sync', [\App\Http\Controllers\Admin\HolidayController::class, 'syncFromApi'])->name('holiday.sync');
        Route::put('/holiday/{id}', [\App\Http\Controllers\Admin\HolidayController::class, 'update'])->name('holiday.update');
        Route::delete('/holiday/{id}', [\App\Http\Controllers\Admin\HolidayController::class, 'destroy'])->name('holiday.destroy');
    });
});
