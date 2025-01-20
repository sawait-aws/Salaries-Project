<?php

namespace App\Http\Controllers;

use App\Models\DaysOffRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Salary;
use App\Models\Task;
use App\Models\Achievement;
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
            return response()->json($salary);
        }

        // Check if the current user is a manager (allow viewing any salary)
        if ($currentUser->role == 'manager') {
            return response()->json($salary);
        }
    }

    // If unauthorized or salary not found, return error
    return response()->json(['error' => 'Unauthorized'], 403);
    }

    public function tasksFetching(){
        $employee = Auth::user();

        // Fetch tasks where the employee ID exists in the emp array
        $tasks = Task::whereJsonContains('emp', $employee->user_id)
            ->orderByRaw("
                CASE 
                    WHEN status IN ('pending', 'in progress') AND priority = 'high' THEN 1
                    WHEN status IN ('pending', 'in progress') AND priority = 'normal' THEN 2
                    WHEN status = 'completed' THEN 3
                END
            ")
            ->orderBy('created_at', 'asc') // Secondary sorting by creation time
            ->get();

        // Pass tasks to the view
        return view('tasks-dashboard', compact(
            'employee',
            'tasks',
        ));
    }
    public function updateStatus(Request $request, $id)
{
    $task = Task::findOrFail($id);

    // Current status
    $currentStatus = $task->status;

    // Define the workflow
    $statusWorkflow = [
        'pending' => 'in progress',
        'in progress' => 'in review',
        'in review' => 'complete',
    ];

    // Handle left and right directions
    if ($request->input('direction') === 'right') {

        $nextStatus = $statusWorkflow[$currentStatus] ?? $currentStatus;
    } elseif ($request->input('direction') === 'left') {
        // Move to the previous status
        $previousStatus = array_search($currentStatus, $statusWorkflow);
        $nextStatus = $previousStatus ?? $currentStatus;
    }

    // Update status
    if ($nextStatus !== $currentStatus) {
        $task->status = $nextStatus;
        $task->save();
    }

    return redirect()->back()->with('success', 'Task status updated successfully.');
}
public function indexEDaysOff()
{
    $employee = Auth::user(); // Retrieve the authenticated employee
    $remainingAnnualDaysOff = Salary::where('user_id', $employee->user_id)
                                    ->latest('created_at')
                                    ->value('remaining_annual_days_off'); // Fetch latest entry

    $daysOffRequests = DaysOffRequest::where('user_id', $employee->user_id)->get(); // Fetch requests by user ID

    return view('days-off-dashboard', compact('employee', 'remainingAnnualDaysOff', 'daysOffRequests'));
}
public function storeDaysOff(Request $request)
{
   // Validate the form inputs
   $request->validate([
    'date' => 'required|date',
    'day_off_kind' => 'required|string|in:Not Paid,Yearly,Sick', // Restrict to the dropdown values
    'proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Optional, limit file types and size
    'emp_notes' => 'nullable|string|max:500', // Optional notes
]);

// Handle the optional file upload
$proofPath = null;
if ($request->hasFile('proof')) {
    $proofPath = $request->file('proof')->store('proofs', 'public'); // Store in public/proofs
}
 $employee = Auth::user();
// Create a new day-off request
DaysOffRequest::create([
    'user_id' => $employee->user_id, // Authenticated user's ID
    'first_name' => $employee->first_name,
    'last_name'=> $employee->last_name,
    'date' => $request->date,
    'position'=> $employee->position,
    'day_off_kind' => $request->day_off_kind,
    'proof' => $proofPath, // Save the file path if provided
    'emp_notes' => $request->emp_notes,
    'status' => 'Requested', // Default status
]);

// Redirect back with a success message
return redirect()->route('emp.daysOff')->with('success', 'Day-off request submitted successfully.');
}
public function destroyDaysOff($id)
{
    $daysOffRequest = DaysOffRequest::where('id', $id)
                             ->where('user_id', Auth::user()->user_id) // Ensure the request belongs to the user
                             ->firstOrFail();

                             if (!empty($daysOffRequest->proof) && file_exists(public_path('storage/' . $daysOffRequest->proof))) {
                                unlink(public_path('storage/' . $daysOffRequest->proof)); // Delete the file
                            }

    $daysOffRequest->delete(); // Delete the request

    return redirect()->route('emp.daysOff')->with('success', 'Days off request deleted.');
}
public function showAchievements()
{
    // Get the logged-in user's ID
    $employee=Auth::user();

    // Fetch all achievements where the user is in any of the achievement categories
    $achievements = Achievement::where(function($query) use ($employee) {
        $query->where('employee_of_the_month', $employee->user_id)
              ->orWhere('top_atv', $employee->user_id)
              ->orWhere('top_performer', $employee->user_id)
              ->orWhere('top_quality', $employee->user_id)
              ->orWhere('top_upselling', $employee->user_id);
    })
    ->orderBy('year', 'desc')  // Sort by most recent first
    ->orderBy('month', 'desc')
    ->get();

    // Initialize arrays for storing months/years for each category
    $employeeOfTheMonth = [];
    $topAtv = [];
    $topPerformer = [];
    $topQuality = [];
    $topUpselling = [];

    // Loop through achievements and group months/years by category
    foreach ($achievements as $achievement) {
        if ($achievement->employee_of_the_month == $employee->user_id) {
            $employeeOfTheMonth[] = $achievement->month . '/' . $achievement->year;
        }
        if ($achievement->top_atv == $employee->user_id) {
            $topAtv[] = $achievement->month . '/' . $achievement->year;
        }
        if ($achievement->top_performer == $employee->user_id) {
            $topPerformer[] = $achievement->month . '/' . $achievement->year;
        }
        if ($achievement->top_quality == $employee->user_id) {
            $topQuality[] = $achievement->month . '/' . $achievement->year;
        }
        if ($achievement->top_upselling == $employee->user_id) {
            $topUpselling[] = $achievement->month . '/' . $achievement->year;
        }
    }

    // Pass the grouped achievements to the view
    return view('achievements-emp', compact(
        'employee',
        'employeeOfTheMonth',
        'topAtv',
        'topPerformer',
        'topQuality',
        'topUpselling'
    ));
}
}
