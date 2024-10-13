<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salary;
use Illuminate\Support\Facades\Auth;

class ApiEmployeeDashboardController extends Controller
{
    // Show employee dashboard
    public function index()
    {
        $employee = Auth::user(); // Get the authenticated employee

        // Get the latest salary entry based on user_id
        $latestSalary = Salary::where('user_id', $employee->user_id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->first();

        // Get all previous salaries
        $salaries = Salary::where('user_id', $employee->user_id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return response()->json([
            'employee' => $employee,
            'latestSalary' => $latestSalary,
            'salaries' => $salaries,
        ]);
    }

    // Load salary details
    public function loadSalaryDetails($id)
    {
        $salary = Salary::find($id);
        $currentUser = Auth::user();

        if ($salary && $salary->user_id == $currentUser->user_id) {
            return response()->json($salary);
        }

        if ($salary && $currentUser->role == 'manager') {
            return response()->json($salary);
        }

        return response()->json([
            'message' => 'Unauthorized or salary not found.'
        ], 403);
    }
}
