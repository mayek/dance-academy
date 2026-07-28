<?php

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\DanceCategoryController;
use App\Http\Controllers\Admin\DanceGroupController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\StudentPaymentController;
use App\Http\Controllers\TeacherAttendanceController;
use App\Http\Controllers\TeacherDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('teachers', TeacherController::class)->except(['show']);
    Route::resource('students', StudentController::class)->except(['show']);
    Route::resource('categories', DanceCategoryController::class)->except(['show']);
    Route::resource('groups', DanceGroupController::class)->except(['show']);
    Route::get('/groups/{group}/assign', [DanceGroupController::class, 'assignStudents'])->name('groups.assign');
    Route::put('/groups/{group}/students', [DanceGroupController::class, 'updateStudents'])->name('groups.update-students');
    Route::resource('payments', PaymentController::class)->except(['show']);
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/students/{student}/absences', [AttendanceController::class, 'studentAbsences'])->name('students.absences');
});

Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/groups/{group}/assign', [TeacherDashboardController::class, 'assignStudents'])->name('assign');
    Route::put('/groups/{group}/students', [TeacherDashboardController::class, 'updateStudents'])->name('update-students');
    Route::get('/attendance/create', [TeacherAttendanceController::class, 'create'])->name('attendance.create');
    Route::post('/attendance', [TeacherAttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/groups/{group}/attendance', [TeacherAttendanceController::class, 'history'])->name('attendance.history');
});

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/payments', [StudentPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [StudentPaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [StudentPaymentController::class, 'store'])->name('payments.store');
});
