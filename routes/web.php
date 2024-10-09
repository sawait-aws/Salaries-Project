<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\EmployeeDashboardController;
use App\Http\Controllers\ManagerDashboardController;
use Illuminate\Support\Facades\Auth;


// Show login form (GET)
Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');

// Handle login form submission (POST)
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');

//logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Middleware-protected route for manager dashboard
Route::middleware(['auth', 'checkrole:employee'])->group(function () {
    Route::get('/employee/dashboard', [EmployeeDashboardController::class, 'index'])->name('employee.dashboard');
});

// Manager Dashboard Route
Route::middleware(['auth', 'checkrole:manager'])->group(function () {
    Route::get('/manager/dashboard', [ManagerDashboardController::class, 'index'])->name('manager.dashboard');
    Route::post('/manager/add-employee', [ManagerDashboardController::class, 'addEmployee'])->name('add.employee');
    Route::delete('/manager/delete-employee/{id}', [ManagerDashboardController::class, 'deleteEmployee'])->name('delete.employee');
    Route::post('/manager/upload-salaries', [ManagerDashboardController::class, 'uploadSalariesCsv'])->name('upload.salaries.csv');
    Route::post('/manager/edit-employee/{id}', [ManagerDashboardController::class, 'editEmployee'])->name('edit.employee');
    Route::get('/manager/edit-employee-form/{id}', [ManagerDashboardController::class, 'editEmployeeForm'])->name('edit.employee.form');
    Route::get('/manager/view-employee/{id}', [ManagerDashboardController::class, 'viewEmployee'])->name('manager.view.employee');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/employee/load-salary/{id}', [EmployeeDashboardController::class, 'loadSalaryDetails']);
});
