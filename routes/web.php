<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\HR\HRController;
use App\Http\Controllers\HR\HREmployeeController;

// Public Routes (Login / Logout)
Route::get('/', function () {
    return view('auth.login');
})->name('login'); 

Route::post('/login', [AuthController::class, 'login'])->name('login.post'); // POST login form
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');    // POST logout

// Admin Routes
Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin/dashboard',[AdminController::class,'dashboard'])->name('admin.dashboard');
    
});

// HR Routes
Route::middleware(['role:hr'])->group(function () {
    Route::get('/hr/dashboard', [HRController::class, 'dashboard'])->name('hr.dashboard');
    Route::resource('employees', HREmployeeController::class)->parameters([
        'employees' => 'employee'
    ]);

});

// Employee Routes
Route::middleware(['role:employee'])->group(function () {
    Route::get('/employee/dashboard', [EmployeeController::class, 'dashboard'])->name('employee.dashboard');
   
});
