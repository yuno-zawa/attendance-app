<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StampCorrectionRequestController;
use App\Http\Controllers\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StampCorrectionRequestController as AdminStampCorrectionRequestController;

Route::get('/', function () {
    return redirect('/attendance');
});

Route::post('/register', [RegisterController::class, 'store']);
Route::post('/login', [LoginController::class, 'store']);

Route::middleware('auth')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index']);
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);
    Route::post('/attendance/break-in', [AttendanceController::class, 'breakIn']);
    Route::post('/attendance/break-out', [AttendanceController::class, 'breakOut']);

    Route::get('/attendance/list', [AttendanceController::class, 'list']);
    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'detail']);
    Route::post('/stamp_correction_request/{id}', [StampCorrectionRequestController::class, 'store']);
});

Route::get('/admin/login', [AdminLoginController::class, 'create']);
Route::post('/admin/login', [AdminLoginController::class, 'store']);

Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/attendance/list', [AdminAttendanceController::class, 'list'])->name('admin.attendance.index');
        Route::post('/logout', [AdminLoginController::class, 'destroy']);
        Route::get('/staff/list', [StaffController::class, 'index']);
        Route::get('/attendance/staff/{id}', [AdminAttendanceController::class, 'stafflist'])->name('admin.staff.attendance');
        Route::get('/attendance/{id}', [AttendanceController::class, 'detail'])->name('admin.attendance.detail');
        Route::post('/attendance/{id}', [AdminAttendanceController::class, 'update']);
        Route::post('/stamp_correction_request/{id}', [AdminStampCorrectionRequestController::class, 'store']);
        Route::get('/stamp_correction_request/approve/{id}', [AdminStampCorrectionRequestController::class, 'approve']);
        Route::post('/stamp_correction_request/approve/{id}', [AdminStampCorrectionRequestController::class, 'executeApprove']);
        Route::get('/attendance/staff/{id}/csv', [AdminAttendanceController::class, 'exportCsv']);
    });

Route::get('/stamp_correction_request/list', function () {
    if (auth()->guard('admin')->check()) {
        return app(AdminStampCorrectionRequestController::class)->index();
    }
    if (auth()->guard('web')->check()) {
        return app(StampCorrectionRequestController::class)->index();
    }
    return redirect('/register');
});