<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\HR\HRController;
use App\Http\Controllers\HR\HREmployeeController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Auth;

// Root route
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route(
            optional(Auth::user()->role)->name === 'admin' ? 'admin.dashboard' : 
            (optional(Auth::user()->role)->name === 'hr' ? 'hr.dashboard' : 'employee.dashboard')
        )
        : redirect()->route('login');
});

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ==================== ADMIN ROUTES ====================
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // THIS LINE WAS BROKEN — NOW FIXED!
        Route::resource('users', UserController::class)
             ->parameters(['users' => 'user']); // THIS FIXES edit/update/destroy

        // Admin can view employees (read-only)
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    });

    // ==================== HR ROUTES ====================
    Route::prefix('hr')
        ->name('hr.')
        ->middleware('role:hr')
        ->group(function () {

            Route::get('/dashboard', [HRController::class, 'dashboard'])->name('dashboard');

            Route::resource('employees', HREmployeeController::class)
                ->parameters(['employees' => 'employee'])
                ->only(['index', 'create', 'store', 'show', 'edit', 'destroy']);

            // Extra safety for update
            Route::match(['put', 'patch', 'post'], 'employees/{employee}', [HREmployeeController::class, 'update'])
                ->name('employees.update');
        });

    // ==================== EMPLOYEE ROUTES ====================
    Route::prefix('employee')
        ->name('employee.')
        ->middleware(['auth', 'role:employee'])
        ->group(function () {
            Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');
            Route::get('/profile', [EmployeeController::class, 'myProfile'])->name('profile');
        });
});