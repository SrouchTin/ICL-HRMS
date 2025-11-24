<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\HR\HRController;
use App\Http\Controllers\HR\HREmployeeController;
use Illuminate\Support\Facades\Auth;

// Root route — ដោះស្រាយទាំង guest និង authenticated នៅទីនេះតែមួយកន្លែង
// Root route — ដោះស្រាយទាំងអស់នៅទីនេះ
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route(
            optional(Auth::user()->role)->name === 'admin' ? 'admin.dashboard' : (optional(Auth::user()->role)->name === 'hr' ? 'hr.dashboard' : 'employee.dashboard')
        )
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    });

    // HR Routes
    Route::prefix('hr')->name('hr.')->middleware('role:hr')->group(function () {
        Route::get('/dashboard', [HRController::class, 'dashboard'])->name('dashboard');
        Route::resource('employees', HREmployeeController::class)
            ->parameters(['employees' => 'employee'])
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy', 'show']);
            });

    // Employee Routes
    Route::prefix('employee')->name('employee.')->middleware('role:employee')->group(function () {
        Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');
    });
});
