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
    Route::get('/tasks-dashboard', [EmployeeDashboardController::class, 'tasksFetching'])->name('tasks.dashboard');
    Route::post('/statusChange/{id}',[EmployeeDashboardController::class, 'updateStatus'])->name('tasks.updateStatus');
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


Route::get('/tasks/create', [ManagerDashboardController::class, 'createTasksPage'])->name('tasks.create');
Route::post('/tasks', [ManagerDashboardController::class, 'storeTasks'])->name('tasks.store');

Route::get('/manager/days-off', [ManagerDashboardController::class, 'indexMDaysOff'])->name('manager.daysOff');
Route::post('/days-off/approve/{id}', [ManagerDashboardController::class, 'approveDaysOff'])->name('daysOffRequests.approve');
Route::post('/days-off/reject/{id}', [ManagerDashboardController::class, 'rejectDaysOff'])->name('daysOffRequests.reject');


Route::get('/employee/days-off', [EmployeeDashboardController::class, 'indexEDaysOff'])->name('emp.daysOff');
Route::post('/days-off/request', [EmployeeDashboardController::class, 'storeDaysOff'])->name('daysOffRequests.store');
Route::delete('/days-off/delete/{id}', [EmployeeDashboardController::class, 'destroyDaysOff'])->name('daysOffRequests.destroy');

Route::get('/employee/achievements', [EmployeeDashboardController::class, 'showAchievements'])->name('emp.achievements');

Route::get('/accounting', [ManagerDashboardController::class, 'indexAcc'])->name('accounting.money');
Route::post('/boxes', [ManagerDashboardController::class, 'BoxesStore'])->name('boxes.store');
Route::post('/transactions', [ManagerDashboardController::class, 'TransactionStore'])->name('transactions.store');
Route::delete('/boxes/{id}/delete', [ManagerDashboardController::class, 'deleteBox'])->name('boxes.delete');
