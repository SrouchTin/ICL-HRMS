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
use Illuminate\Support\Facades\Auth;
// ==================== 1. Root Route - កែឲ្យមាំមួន 100% ====================
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route(match (strtolower(trim(Auth::user()?->role?->name ?? 'employee'))) {
            'admin', 'super_admin' => 'admin.dashboard',
            'hr'                   => 'hr.dashboard',
            default                => 'employee.dashboard',
        })
        : redirect()->route('login');
})->name('home');

// ==================== 2. Login Routes ====================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// ==================== 3. Authenticated Routes ====================
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ==================== ADMIN (admin + super_admin) ====================
    Route::prefix('admin')
        ->name('admin.')
        ->middleware('auth', 'role:admin,super_admin')
        ->group(function () {

            Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

            // Users
            Route::resource('users', UserController::class)
                ->parameters(['users' => 'user'])
                ->except(['show']);

            Route::patch('users/{user}/toggle', [UserController::class, 'toggle'])
                ->name('users.toggle');
            Route::patch('users/{user}/reset-password', [UserController::class, 'resetPassword'])
                ->name('users.reset-password');
            Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
                ->name('users.toggleStatus');

            Route::get('/leave/requests', [AdminLeaveController::class, 'index'])->name('leave.requests');
            Route::patch('/leave/{leave}/approve', [AdminLeaveController::class, 'approve'])->name('leave.approve');
            Route::patch('/leave/{leave}/reject', [AdminLeaveController::class, 'reject'])->name('aleave.reject');
        });

    // ==================== HR ====================
    Route::prefix('hr')->name('hr.')->middleware('role:hr')->group(function () {
        Route::get('/dashboard', [HRController::class, 'dashboard'])->name('dashboard');

        Route::resource('employees', HREmployeeController::class)
            ->parameters(['employees' => 'employee'])
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        Route::get('/leave-requests', [HRLeaveController::class, 'index'])
            ->name('leave.requests');

        Route::patch('/leave-requests/{leave}/approve', [HRLeaveController::class, 'approve'])
            ->name('leave.approve');

        Route::post('/leave/{leave}/reject', [HRLeaveController::class, 'reject'])
        ->name('leave.reject');
        });

    // ==================== EMPLOYEE ====================
    Route::prefix('employee')
        ->name('employee.')
        ->middleware(['auth', 'role:employee,user'])  // ← Added 'auth' here
        ->group(function () {

            Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');
            Route::get('/profile', [EmployeeController::class, 'myProfile'])->name('profile');

            Route::prefix('leaves')->name('leaves.')->group(function () {
                Route::get('/', [EmployeeLeaveController::class, 'index'])->name('index');
                Route::get('/create', [EmployeeLeaveController::class, 'create'])->name('create');
                Route::post('/store', [EmployeeLeaveController::class, 'store'])->name('store');
                Route::get('/{leave}/edit', [EmployeeLeaveController::class, 'edit'])->name('edit');
                Route::put('/{leave}', [EmployeeLeaveController::class, 'update'])->name('update');
                Route::delete('/{leave}', [EmployeeLeaveController::class, 'destroy'])->name('destroy');
            });

            Route::get('/leave-balance', [EmployeeLeaveController::class, 'getBalance'])
                ->name('leave.balance');

            Route::get('/employee/person-incharge-available', [EmployeeLeaveController::class, 'getAvailablePersonInCharge'])
                ->name('person.incharge.available')
                ->middleware('auth');
        });
});

// ==================== 4. Fallback Route (សំខាន់ណាស់!) ====================
Route::fallback(function () {
    return redirect()->route('employee.dashboard');
})->middleware('auth')->name('fallback');
