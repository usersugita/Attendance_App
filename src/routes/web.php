<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// メール確認ページ（ユーザーに「確認してね」と表示するページ）
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// メール内のリンクから遷移する確認用ルート（← これが無いとエラーになる）
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // メール認証を完了
    return redirect('/attendance'); // 認証後のリダイレクト先
})->middleware(['auth', 'signed'])->name('verification.verify');

// メール再送用
Route::post('/email/verification-notification', function (Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '確認用メールを再送しました！');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/admin/login', [AdminController::class, 'adminlogin']);
Route::post('/admin/login', [AdminController::class, 'login']);
Route::post('/admin/logout', [AdminController::class, 'logout']);

Route::middleware(['auth', 'admin'])->group(
    function () {
        Route::get('/admin/attendance/list', [AdminController::class, 'dailyList'])->name('attendance.daily');
        Route::get('/stamp_correction_request/approve/{id}', [AdminController::class, 'adminCorrection']);
        Route::post('/stamp_correction_request/approve/{id}', [AdminController::class, 'processCorrection']);
        Route::get('/admin/staff/list', [AdminController::class, 'adminStaffList']);
        Route::get('/admin/attendance/staff/{id}', [AdminController::class, 'adminAttendanceStaff']);
        Route::get('/admin/attendance/csv/{id}', [AdminController::class, 'exportCsv'])->name('admin.attendance.csv');
    }
);
Route::middleware(['auth'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index']);
    Route::post('/attendance/start', [AttendanceController::class, 'startWork']);
    Route::post('/attendance/workend', [AttendanceController::class, 'endWork']);
    Route::post('/attendance/breakstart', [AttendanceController::class, 'startbreak']);
    Route::post('/attendance/breakend', [AttendanceController::class, 'endbreak']);
    Route::post('/logout', [AttendanceController::class, 'logout']);
    Route::get('/attendance/list', [AttendanceController::class, 'attendancelist']);
    Route::get('/attendance/{id}', [AttendanceController::class, 'attendancedetail']);
    Route::post('/attendance-request', [AttendanceController::class, 'store']);
    Route::get('/stamp_correction_request/list', [AttendanceController::class, 'correction']);
});
