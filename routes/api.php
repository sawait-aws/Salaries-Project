<?php

use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\ApiManagerDashboardController;
use App\Http\Controllers\ApiEmployeeDashboardController;
use Illuminate\Support\Facades\Route;

// Public routes (e.g., login)
Route::post('/login', [ApiAuthController::class, 'login']);

// Protected routes (require Sanctum token authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);

    // Manager routes
    Route::middleware(['auth', 'checkrole:manager'])->group(function () {
    Route::get('/manager/dashboard', [ApiManagerDashboardController::class, 'index']);
    Route::post('/manager/add-employee', [ApiManagerDashboardController::class, 'addEmployee']);
    Route::delete('/manager/delete-employee/{id}', [ApiManagerDashboardController::class, 'deleteEmployee']);
    Route::post('/manager/upload-salaries', [ApiManagerDashboardController::class, 'uploadSalariesCsv']);
    Route::put('/manager/edit-employee/{id}', [ApiManagerDashboardController::class, 'editEmployee']);
    Route::get('/manager/view-employee/{id}', [ApiManagerDashboardController::class, 'viewEmployee']); // View as employee
    });
    // Employee routes
    Route::middleware(['auth', 'checkrole:employee'])->group(function () {
    Route::get('/employee/dashboard', [ApiEmployeeDashboardController::class, 'index']);
    });
    Route::middleware(['auth'])->group(function () {
    Route::get('/employee/load-salary/{id}', [ApiEmployeeDashboardController::class, 'loadSalaryDetails']);
    });
});
