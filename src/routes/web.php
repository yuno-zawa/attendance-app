<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AttendanceController;

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
});