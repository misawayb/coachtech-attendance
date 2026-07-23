<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\StampCorrectionRequestController as AdminStampCorrectionRequestController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StampCorrectionRequestController;


Route::get('/', [AttendanceController::class, 'index']);
Route::get('/login',[LoginController::class,'index'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::get('/admin/login', [AdminLoginController::class, 'index'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'store'])->name('admin.store');

Route::middleware('auth')->group(function(){
    Route::get('/attendance', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/list', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::get('/attendance/detail/{date}', [AttendanceController::class, 'edit'])->name('attendance.edit');
    Route::post('/attendance/detail/{date}', [StampCorrectionRequestController::class, 'store'])->name('request.store');
    Route::get('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'index'])->name('request.index');
    Route::post('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'show'])->name('request.show');
});

Route::middleware('admin')->group(function(){
    Route::get('/admin/attendance/list', [AdminAttendanceController::class, 'index'])->name('admin.index');
    Route::post('/admin/attendance/list', [AdminAttendanceController::class, 'show'])->name('admin.show');
    Route::get('/admin/attendance/{id}', [AdminAttendanceController::class, 'edit'])->name('admin.edit');
    Route::post('/admin/attendance/{id}', [AdminAttendanceController::class, 'update'])->name('admin.update');
    Route::get('/admin/staff/list', [AdminAttendanceController::class, 'staffIndex'])->name('staff.index');
    Route::get('/admin/attendance/staff/{id}', [AdminAttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminStampCorrectionRequestController::class, 'show'])->name('correction.show');
    Route::post('/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminStampCorrectionRequestController::class, 'store'])->name('correction.update');
});