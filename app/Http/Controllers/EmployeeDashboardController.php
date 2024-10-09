<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Salary;
use Illuminate\Support\Facades\Auth;

class EmployeeDashboardController extends Controller
{
    // Show employee dashboard
    public function index()
    {
        $employee = Auth::user(); // Get the logged-in user

        // Get the latest salary entry based on user_id, not id
        $latestSalary = Salary::where('user_id', $employee->user_id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->first();

        // Get all previous salaries based on user_id
        $salaries = Salary::where('user_id', $employee->user_id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('employee-dashboard', compact('employee', 'latestSalary', 'salaries'));
    }

    // Load salary details via AJAX
    public function loadSalaryDetails($id)
    {
        $salary = Salary::find($id);

    // If salary exists
    if ($salary) {
        $currentUser = Auth::user();

        // Check if the current user is an employee and the salary belongs to them
        if ($currentUser->role == 'employee' && $salary->user_id == $currentUser->user_id) {
            session()->flash('success', 'Salary details loaded successfully.');
            return response()->json($salary);
        }

        // Check if the current user is a manager (allow viewing any salary)
        if ($currentUser->role == 'manager') {
            session()->flash('success', 'Salary details loaded successfully.');
            return response()->json($salary);
        }
    }

    // If unauthorized or salary not found, return error
    return response()->json(['error' => 'Unauthorized'], 403);
    }
}
