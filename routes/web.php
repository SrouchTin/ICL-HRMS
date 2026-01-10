<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\HR\HRController;
use App\Http\Controllers\HR\HREmployeeController;
use App\Http\Controllers\Employee\EmployeeLeaveController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HR\HRLeaveController;
use App\Http\Controllers\Admin\AdminLeaveController;
use App\Http\Controllers\HR\HROwnLeaveController;
use App\Http\Controllers\HR\HRAttendanceController;
use App\Http\Controllers\Employee\AttendanceController;
use App\Http\Controllers\HR\HRAttendanceReportController;  
use Illuminate\Support\Facades\Auth;

// ==================== 1. Root Route - Redirect based on role ====================
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $role = strtolower(trim(Auth::user()?->role?->name ?? 'employee'));

    return redirect()->route(match ($role) {
        'admin', 'super_admin' => 'admin.dashboard',
        'hr'                   => 'hr.dashboard',
        default                => 'employee.dashboard',
    });
})->name('home');

// ==================== 2. Guest Routes (Login) ====================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// ==================== 3. Authenticated Global Routes ====================
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ==================== Global API Routes (used by both Employee & HR) ====================
    // Leave Balance API
    Route::get('/leave/balance', [EmployeeLeaveController::class, 'getBalance'])
        ->name('employee.leave.balance');

    // Person In Charge Available API - accessible to all authenticated users
    Route::get('/person-incharge/available', [HROwnLeaveController::class, 'availablePersonInCharge'])
        ->name('employee.person.incharge.available');

    // ==================== ADMIN ====================
    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:admin,super_admin')
        ->group(function () {

            Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

            // Users Management
            Route::resource('users', UserController::class)
                ->parameters(['users' => 'user'])
                ->except(['show']);

            Route::patch('users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
            Route::patch('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
            Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');

            // Admin Leave Approval
            Route::get('/leave/requests', [AdminLeaveController::class, 'index'])->name('leave.requests');
            Route::patch('/leave/{leave}/approve', [AdminLeaveController::class, 'approve'])->name('leave.approve');
            Route::post('/leave/{leave}/reject', [AdminLeaveController::class, 'reject'])->name('leave.reject');
        });

    // ==================== HR ====================
    Route::prefix('hr')
        ->name('hr.')
        ->middleware('role:hr')
        ->group(function () {

            Route::get('/dashboard', [HRController::class, 'dashboard'])->name('dashboard');

            // HR Employee Management
            Route::resource('employees', HREmployeeController::class)
                ->parameters(['employees' => 'employee'])
                ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

            // HR Leave Approval (for all employees)
            Route::get('/leave-requests', [HRLeaveController::class, 'index'])->name('leave.requests');
            Route::patch('/leave-requests/{leave}/approve', [HRLeaveController::class, 'approve'])->name('leave.approve');
            Route::post('/leave-requests/{leave}/reject', [HRLeaveController::class, 'reject'])->name('leave.reject');

            // ==================== HR Personal Leave (My Leaves) ====================
            Route::prefix('my-leaves')->name('own-leave.')->group(function () {
                Route::get('/', [HROwnLeaveController::class, 'index'])->name('index');
                Route::get('/create', [HROwnLeaveController::class, 'create'])->name('create');
                Route::post('/', [HROwnLeaveController::class, 'store'])->name('store');
                Route::get('/{leave}/edit', [HROwnLeaveController::class, 'edit'])->name('edit');
                Route::put('/{leave}', [HROwnLeaveController::class, 'update'])->name('update');
                Route::delete('/{leave}', [HROwnLeaveController::class, 'destroy'])->name('destroy');

                Route::post('/{leave}/reject', [HROwnLeaveController::class, 'reject'])
                    ->name('reject');
            });
            Route::get('/attendance', [HRAttendanceController::class, 'index'])->name('attendance.index');
            Route::post('/attendance/check-in', [HRAttendanceController::class, 'checkIn'])->name('attendance.checkIn');
            Route::post('/attendance/check-out', [HRAttendanceController::class, 'checkOut'])->name('attendance.checkOut');
            Route::post('/attendance/manual-check-in', [HRAttendanceController::class, 'manualCheckIn'])->name('attendance.manualCheckIn');
            Route::get('/attendance/export', [HRAttendanceController::class, 'export'])->name('attendance.export');
            Route::get('/attendance/employee/{employeeId}', [HRAttendanceController::class, 'employeeAttendance'])->name('attendance.employee');

                // Monthly Report
            Route::get('/attendance/report/monthly', [HRAttendanceReportController::class, 'monthlyReport'])
                ->name('attendance.report.monthly');
            Route::get('/attendance/report/monthly/export', [HRAttendanceReportController::class, 'exportMonthly'])
                ->name('attendance.report.monthly.export');
        });

    // ==================== EMPLOYEE  ====================
    Route::prefix('employee')
        ->name('employee.')
        ->middleware(['role:employee,user'])
        ->group(function () {

            Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');
            Route::get('/profile', [EmployeeController::class, 'myProfile'])->name('profile');

            // Employee Leave Management
            Route::prefix('leaves')->name('leaves.')->group(function () {
                Route::get('/', [EmployeeLeaveController::class, 'index'])->name('index');
                Route::get('/create', [EmployeeLeaveController::class, 'create'])->name('create');
                Route::post('/store', [EmployeeLeaveController::class, 'store'])->name('store');
                Route::get('/{leave}/edit', [EmployeeLeaveController::class, 'edit'])->name('edit');
                Route::put('/{leave}', [EmployeeLeaveController::class, 'update'])->name('update');
                Route::delete('/{leave}', [EmployeeLeaveController::class, 'destroy'])->name('destroy');

                // Supervisor Approval
                Route::get('/pending-approvals', [EmployeeLeaveController::class, 'pendingApprovals'])->name('pending');
                Route::patch('/{leave}/approve', [EmployeeLeaveController::class, 'approve'])->name('approve');
                Route::post('/{leave}/reject', [EmployeeLeaveController::class, 'reject'])->name('reject');
                Route::get('/{leave}/details', [EmployeeLeaveController::class, 'details'])->name('details');
            });

            // Employee Attendance
            Route::prefix('attendance')->name('attendance.')->group(function () {
                Route::get('/', [AttendanceController::class, 'index'])->name('index');
                Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('checkIn');
                Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('checkOut');
                Route::get('/export', [AttendanceController::class, 'export'])->name('export');
            })->middleware(['auth']);
        });
});

// ==================== 4. Fallback Route ====================
Route::fallback(function () {
    if (Auth::check()) {
        $role = strtolower(trim(Auth::user()?->role?->name ?? 'employee'));
        return redirect()->route(match ($role) {
            'admin', 'super_admin' => 'admin.dashboard',
            'hr'                   => 'hr.dashboard',
            default                => 'employee.dashboard',
        });
    }
    return redirect()->route('login');
})->name('fallback');
