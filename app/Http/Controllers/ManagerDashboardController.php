<?php

namespace App\Http\Controllers;

use App\Models\DaysOffRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Salary;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Task;
use App\Models\Achievement;
use App\Models\Box;
use App\Models\Transaction;

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
            'email_address'=> 'required|string|max:255',
            'user_id' => 'required|integer|unique:users,user_id',
            'password' => 'required|string',
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'position' => $request->position,
            'joining_date'=> $request->joining_date,
            'email_address'=> $request->email_address,
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
    $achievementProcessed = false; // Flag to track if the achievement data is already processed

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
            $remaining_annual_days_off = $data[10];
            $deduction = $data[11];
            $bonus = $data[12];
            $salaryToBePaid = $data[13];

            if (!$achievementProcessed && isset($data[17], $data[18], $data[19], $data[20], $data[21], $data[22], $data[23])) {
                $achievementData = [
                    'year' => $data[17],
                    'month' => $data[18],
                    'top_atv' => $data[19],
                    'top_performer' => $data[20],
                    'top_quality' => $data[21],
                    'top_upselling' => $data[22],
                    'employee_of_the_month' => $data[23],
                ];

                // Update or create the achievement entry
                Achievement::updateOrCreate(
                    [
                        'year' => $achievementData['year'],
                        'month' => $achievementData['month'],
                    ],
                    [
                        'employee_of_the_month' => $achievementData['employee_of_the_month'],
                        'top_atv' => $achievementData['top_atv'],
                        'top_performer' => $achievementData['top_performer'],
                        'top_quality' => $achievementData['top_quality'],
                        'top_upselling' => $achievementData['top_upselling'],
                    ]
                );

                $achievementProcessed = true; // Mark achievement as processed
            }

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
                    'remaining_annual_days_off' => $remaining_annual_days_off,
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
                'email_address'=> 'required|string|max:255',
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
                $user->email_address = $request->email_address;
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
                        'remaining_annual_days_off' => $salary['remaining_annual_days_off'],
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
                $user->email_address = $request->email_address;

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

public function createTasksPage()
    {
        $manager = Auth::user();
        // Filter employees based on position and role
        $employees = User::where('role', 'employee') // Filter by role
            ->where('position', $manager->position) // Allowed positions
            ->get(['user_id', 'first_name', 'last_name']);

        // Pass employees to the view

        return view('tasks-manager', compact('manager','employees'));
    }

    // Store the task
    public function storeTasks(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:high,normal,low',
            'emp' => 'required|json',
        ]);

        // Create a new task
        Task::create([
            'name' => $request->name,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => 'pending', // Default status
            'emp' => json_decode($request->emp), // Employee IDs as JSON
        ]);

        return redirect()->route('tasks.create')->with('success', 'Task added successfully.');
    }

    public function indexMDaysOff()
{
    $manager = Auth::user(); // Retrieve the authenticated manager
    $daysOffRequests = DaysOffRequest::where('position', $manager->position)->get(); // Match by position

    return view('days-off-manager', compact('manager', 'daysOffRequests'));
}

public function approveDaysOff(Request $request, $id)
{
    $daysOffRequest = DaysOffRequest::findOrFail($id); // Find the request
    
    // Update status based on role
    if (Auth::user()->role == 'manager') {
        $daysOffRequest->status = 'managerApprove'; // Mark as approved
    } else if (Auth::user()->role == 'topManager') {
        $daysOffRequest->status = 'TopManagerApprove'; // Mark as approved
    }
    
    // Update manager notes if provided
    if ($request->has('manager_notes')) {
        $daysOffRequest->manager_notes = $request->input('manager_notes');
    }

    $daysOffRequest->save(); // Save changes

    return redirect()->route('manager.daysOff')->with('success', 'Days off request approved.');
}

public function rejectDaysOff(Request $request, $id)
{
    $daysOffRequest = DaysOffRequest::findOrFail($id); // Find the request
    $request->validate([
        'manager_notes' => 'nullable|string|max:255',
    ]);
    // Update status based on role
    if (Auth::user()->role == 'manager') {
        $daysOffRequest->status = 'managerReject'; // Mark as rejected
    } else if (Auth::user()->role == 'topManager') {
        $daysOffRequest->status = 'TopManagerReject'; // Mark as rejected
    }
    
    // Update manager notes if provided
    if ($request->has('manager_notes')) {
        $daysOffRequest->manager_notes = $request->input('manager_notes');
    }

    $daysOffRequest->save(); // Save changes

    return redirect()->route('manager.daysOff')->with('success', 'Days off request rejected.');
}

public function BoxesStore(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0',
    ]);

    Box::create([
        'name' => $request->input('name'),
        'amount' => $request->input('amount'),
    ]);

    return redirect()->route('accounting.money')->with('success', 'Box created.');
}

public function TransactionStore(Request $request)
{
    $request->validate([
        'sender_box' => 'required|exists:boxes,id',
        'receiver_box' => 'required|exists:boxes,id|different:sender_box',
        'amount' => 'required|numeric|min:0.01',
        'commission_kind' => 'required|in:percentage,static',
        'commission_amount' => 'required|numeric|min:0',
    ]);

    $senderBox = Box::findOrFail($request->input('sender_box'));
    $receiverBox = Box::findOrFail($request->input('receiver_box'));
    $amount = $request->input('amount');
    $commissionKind = $request->input('commission_kind');
    $commissionAmount = $request->input('commission_amount');

    // Calculate commission
    $totalCommission = 0;
    if ($commissionKind === 'percentage') {
        $totalCommission = $amount * ($commissionAmount / 100);
    } elseif ($commissionKind === 'static') {
        $totalCommission = $commissionAmount;
    }

    // Total deduction includes the transaction amount and commission
    $totalDeduction = $amount + $totalCommission;

    // Prevent processing if insufficient funds
    if ($senderBox->amount < $totalDeduction) {
        abort(400, 'Insufficient funds in the sender box.');
    }

    // Deduct from sender and add to receiver
    $senderBox->amount -= $totalDeduction;
    $receiverBox->amount += $amount;

    // Save the updated amounts
    $senderBox->save();
    $receiverBox->save();

    $emp = Auth::user();

    // Record the transaction
    Transaction::create([
        'sender_box' => $senderBox->name,
        'sender_box_amount' => $senderBox->amount + $totalDeduction,
        'receiver_box' => $receiverBox->name,
        'receiver_box_amount' => $receiverBox->amount - $amount,
        'amount' => $amount,
        'commission_kind' => $commissionKind,
        'commission_amount' => $totalCommission,
        'user_id' => $emp->user_id,
        'user_first_name' => $emp->first_name,
        'user_last_name' => $emp->last_name,
        'transaction_date' => now(),
    ]);

    return redirect()->route('accounting.money')->with('success', 'Transaction completed with commission applied.');
}

    public function deleteBox($id)
    {
        $box = Box::findOrFail($id);
        $box->delete();

        return redirect()->route('accounting.money')->with('success', 'Box deleted successfully');
    }
    public function indexAcc()
    {
        $manager = Auth::user();
        $boxes = Box::all();  // Fetch all box names and amounts
        // Fetch all transactions
        $transactions = Transaction::orderBy('transaction_date', 'desc')->get();  
        return view('manager-acc', compact('manager','boxes', 'transactions'));
    }
}
