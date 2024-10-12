<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Salary;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ManagerDashboardController extends Controller
{
    // Show manager dashboard
    public function index()
    {
        $manager = Auth::user(); // Get the logged-in manager

        // Get all employees excluding managers
        $employees = User::where('role', 'employee')->get();

        return view('manager-dashboard', compact('manager', 'employees'));
    }

    // Add Employee
    public function addEmployee(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'joining_date'=> 'required|date',
            'user_id' => 'required|integer|unique:users,user_id',
            'password' => 'required|string',
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'position' => $request->position,
            'joining_date'=> $request->joining_date,
            'user_id' => $request->user_id,
            'password' => Hash::make($request->password),
            'role' => 'employee',
        ]);

        return redirect()->back()->with('success', 'Employee added successfully.');
    }


    // Delete Employee
    public function deleteEmployee($id)
{
    $employee = User::find($id);

    if ($employee && $employee->role == 'employee') {
        // Delete all salary records associated with the employee
        Salary::where('user_id', $employee->user_id)->delete();

        // Delete the employee
        $employee->delete();

        return redirect()->route('manager.dashboard')->with('success', 'Employee and associated salaries deleted successfully.');
    }

    return redirect()->back()->with('error', 'Employee not found.');
}

    public function uploadSalariesCsv(Request $request)
{
    $request->validate([
        'csv_file' => 'required|file|mimes:csv,txt',
    ]);

    $file = $request->file('csv_file');
    $handle = fopen($file, 'r');

    if ($handle !== false) {
        // Skip the header row
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
            $deduction = $data[10];
            $bonus = $data[11];
            $salaryToBePaid = $data[12];

            // Update or create the salary entry
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
                    'deduction' => $deduction,
                    'bonus' => $bonus,
                    'salary_to_be_paid' => $salaryToBePaid,
                ]
            );
        }

        fclose($handle);
    }

    return redirect()->back()->with('success', 'Salaries uploaded successfully.');
}
public function editEmployee(Request $request, $id)
    {
        DB::beginTransaction();  // Start the transaction to ensure atomic operations

        try {
            // Find the user by ID
            $user = User::findOrFail($id);

            // Validate the request input
            $validatedData = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'joining_date'=> 'required|date',
                'user_id' => 'required|numeric|unique:users,user_id,' . $user->id,
                'password' => 'nullable', // Password is optional
            ]);

            // Only proceed if user_id is being changed
            if ($user->user_id != $request->user_id) {
                // Step 1: Fetch and store all salary records related to the current user_id in an array
                $salaries = Salary::where('user_id', $user->user_id)->get()->toArray();

                // Step 2: Delete all salary records for the current user_id
                Salary::where('user_id', $user->user_id)->delete();

                // Step 3: Update user details
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->position = $request->position;
                $user->joining_date = $request->joining_date;
                $user->user_id = $request->user_id;

                // Update password only if provided
                if ($request->filled('password')) {
                    $user->password = Hash::make($request->password);
                }

                // Save the updated user
                $user->save();

                // Step 4: Reinsert the stored salary records with the new user_id
                foreach ($salaries as $salary) {
                    Salary::create([
                        'user_id' => $user->user_id, // Set the new user_id
                        'year' => $salary['year'],
                        'month' => $salary['month'],
                        'gross_salary' => $salary['gross_salary'],
                        'commission' => $salary['commission'],
                        'salaf' => $salary['salaf'],
                        'salaf_deducted' => $salary['salaf_deducted'],
                        'working_days' => $salary['working_days'],
                        'unpaid_days' => $salary['unpaid_days'],
                        'sick_leave' => $salary['sick_leave'],
                        'deduction' => $salary['deduction'],
                        'bonus' => $salary['bonus'],
                        'salary_to_be_paid' => $salary['salary_to_be_paid']
                    ]);
                }
            } else {
                // If no change in user_id, just update other fields
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->position = $request->position;
                $user->joining_date = $request->joining_date; 
                
                if ($request->filled('password')) {
                    $user->password = Hash::make($request->password);
                }

                $user->save(); // Save the user without changing the user_id
            }

            DB::commit();  // Commit the transaction

            // Redirect to the dashboard with a success message
            return redirect()->route('manager.dashboard')->with('success', 'Employee details updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();  // Rollback the transaction in case of error

            // Log the error for debugging
            Log::error('Error updating employee: ' . $e->getMessage());

            // Redirect back with an error message
            return redirect()->back()->withErrors('Failed to update employee details. Please try again.');
        }
    }


public function editEmployeeForm($id)
{
    $employee = User::find($id);

    if ($employee && $employee->role == 'employee') {
        return view('edit', compact('employee')); // Use the 'edit' view in the main 'views' directory
    }

    return response()->view('errors.404', [], 404);
}

public function viewEmployee($id)
{
    // Find the employee by their ID
    $employee = User::where('id', $id)->where('role', 'employee')->firstOrFail();

    // Load employee's salary and other data
    $latestSalary = Salary::where('user_id', $employee->user_id)->orderBy('year', 'desc')->orderBy('month', 'desc')->first();
    $salaries = Salary::where('user_id', $employee->user_id)->orderBy('year', 'desc')->orderBy('month', 'desc')->get();

    // Return the employee dashboard view with the employee's data
    return view('employee-dashboard', compact('employee', 'latestSalary', 'salaries'));
}

}
