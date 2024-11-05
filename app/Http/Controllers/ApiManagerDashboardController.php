<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Salary;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ApiManagerDashboardController extends Controller
{
    // Show manager dashboard with employee list
    public function index()
    {
        $manager = Auth::user(); // Get the authenticated manager

        // Get all employees excluding managers
        $employees = User::where('role', 'employee')->get();

        return response()->json([
            'manager' => $manager,
            'employees' => $employees,
        ]);
    }

    // Add Employee
    public function addEmployee(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'joining_date'=> 'required|date',
            'email_address'=> 'required|string|max:255',
            'user_id' => 'required|integer|unique:users,user_id',
            'password' => 'required|string',
        ]);

        // Create new employee
        $employee = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'position' => $request->position,
            'joining_date'=> $request->joining_date,
            'email_address'=> $request->email_address,
            'user_id' => $request->user_id,
            'password' => Hash::make($request->password),
            'role' => 'employee',
        ]);

        return response()->json([
            'message' => 'Employee added successfully.',
            'employee' => $employee,
        ]);
    }

    // Delete Employee
    public function deleteEmployee($id)
    {
        $employee = User::find($id);

        if ($employee && $employee->role == 'employee') {
            // Delete associated salaries
            Salary::where('user_id', $employee->user_id)->delete();

            // Delete the employee
            $employee->delete();

            return response()->json([
                'message' => 'Employee and associated salaries deleted successfully.'
            ]);
        }

        return response()->json([
            'message' => 'Employee not found or unauthorized.'
        ], 404);
    }

    // Upload salaries via CSV
    public function uploadSalariesCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file, 'r');

        if ($handle !== false) {
            // Skip header row
            fgetcsv($handle);

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $userId = $data[0];
                $month = $data[1];
                $year = $data[2];
                $grossSalary = $data[3];
                $commission = $data[4];
                $salaf = $data[5];
                $salafDeducted = $data[6];
                $working_days = $data[7];
                $unpaid_days = $data[8];
                $sick_leave = $data[9];
                $remaining_annual_days_off = $data[10];
                $deduction = $data[11];
                $bonus = $data[12];
                $salaryToBePaid = $data[13];

                // Update or create salary record
                Salary::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'month' => $month,
                        'year' => $year,
                    ],
                    [
                        'gross_salary' => $grossSalary,
                        'commission' => $commission,
                        'salaf' => $salaf,
                        'salaf_deducted' => $salafDeducted,
                        'working_days' => $working_days,
                        'unpaid_days' => $unpaid_days,
                        'sick_leave' => $sick_leave,
                        'remaining_annual_days_off' => $remaining_annual_days_off,
                        'deduction' => $deduction,
                        'bonus' => $bonus,
                        'salary_to_be_paid' => $salaryToBePaid,
                    ]
                );
            }

            fclose($handle);

            return response()->json([
                'message' => 'Salaries uploaded successfully.',
            ]);
        }

        return response()->json([
            'message' => 'Failed to process CSV file.'
        ], 500);
    }

    // Edit Employee
    public function editEmployee(Request $request, $id)
    {
        DB::beginTransaction(); // Begin the transaction

        try {
            // Find the employee by ID
            $employee = User::findOrFail($id);

            // Validate request input
            $validatedData = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'joining_date' => 'required|date',
                'email_address'=> 'required|string|max:255',
                'user_id' => 'required|numeric|unique:users,user_id,' . $employee->id,
                'password' => 'nullable|string', // Password is optional
            ]);

            // Check if the user_id is being changed
            if ($employee->user_id != $request->user_id) {
                // Step 1: Fetch and store all salary records related to the current user_id
                $salaries = Salary::where('user_id', $employee->user_id)->get()->toArray();

                // Step 2: Delete all salary records associated with the current user_id
                Salary::where('user_id', $employee->user_id)->delete();

                // Step 3: Update the employee details (including the user_id)
                $employee->update([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'position' => $request->position,
                    'joining_date' => $request->joining_date,
                    'email_address' => $request->email_address,
                    'user_id' => $request->user_id,
                    'password' => $request->filled('password') ? Hash::make($request->password) : $employee->password,
                ]);

                // Step 4: Reinsert the salary records with the new user_id
                foreach ($salaries as $salary) {
                    Salary::create([
                        'user_id' => $employee->user_id, // Use the new user_id
                        'year' => $salary['year'],
                        'month' => $salary['month'],
                        'gross_salary' => $salary['gross_salary'],
                        'commission' => $salary['commission'],
                        'salaf' => $salary['salaf'],
                        'salaf_deducted' => $salary['salaf_deducted'],
                        'working_days' => $salary['working_days'],
                        'unpaid_days' => $salary['unpaid_days'],
                        'sick_leave' => $salary['sick_leave'],
                        'remaining_annual_days_off' => $salary['remaining_annual_days_off'],
                        'deduction' => $salary['deduction'],
                        'bonus' => $salary['bonus'],
                        'salary_to_be_paid' => $salary['salary_to_be_paid'],
                    ]);
                }
            } else {
                // If user_id is not being changed, simply update the employee's details
                $employee->update([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'user_id' => $request->user_id,
                    'position' => $request->position,
                    'joining_date' => $request->joining_date,
                    'email_address' => $request->email_address,
                    'password' => $request->filled('password') ? Hash::make($request->password) : $employee->password,
                ]);
            }

            DB::commit(); // Commit the transaction

            return response()->json([
                'message' => 'Employee and salaries updated successfully.',
                'employee' => $employee,
            ]);

        } catch (\Exception $e) {
            DB::rollBack(); // Roll back the transaction if an error occurs
            Log::error('Error updating employee: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update employee and salaries. Please try again.',
            ], 500);
        }
    }
    public function viewEmployee($id)
    {
        // Find the employee by their ID
        $employee = User::where('id', $id)->where('role', 'employee')->firstOrFail();

        // Get the latest salary entry for this employee
        $latestSalary = Salary::where('user_id', $employee->user_id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->first();

        // Get all previous salaries for this employee
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
}
