<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Employee Profile Section -->
        <div class="profile-section">
            <h2>{{ $employee->first_name }} {{ $employee->last_name }}</h2>
            <p>User ID: {{ $employee->user_id }}</p>
            <p>Position: {{ $employee->position }}</p>
            <p>Joining Date: {{ $employee->joining_date }}</p>
            @if(Auth::user()->role == 'employee')
    <!-- Show logout button for employee -->
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-logout">Logout</button>
    </form>
@else
    <!-- Show return to manager dashboard for manager -->
    <div class="manager-view-alert">
        <p>You are viewing this dashboard <span>as a manager.</span></p>
        <a href="{{ route('manager.dashboard') }}" class="btn-return-dashboard">Return to Manager Dashboard</a>
    </div>
    <a href="{{ route('edit.employee.form', $employee->id) }}" class="btn-action">
        <i class="fas fa-edit"></i> Edit
    </a>
    <form action="{{ route('delete.employee', $employee->id) }}" method="POST" style="display: inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-action delete-employee-btn">
            <i class="fas fa-trash-alt"></i> Delete
        </button>
    </form>
@endif
        <!-- Latest Salary Details Section -->
        @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
        <div class="salary-section">
            <h3 class="salary-title">Salary Details ({{ $latestSalary->year }}-{{ $latestSalary->month }})</h3>
            <div class="salary-card">
                <div class="salary-item">
                    <h4>Gross Salary</h4>
                    <p>{{ $latestSalary->gross_salary }}</p>
                </div>
                <div class="salary-item">
                    <h4>Commission</h4>
                    <p>{{ $latestSalary->commission }}</p>
                </div>
                <div class="salary-item">
                    <h4>Salaf</h4>
                    <p>{{ $latestSalary->salaf }}</p>
                </div>
                <div class="salary-item">
                    <h4>Salaf Deducted</h4>
                    <p>{{ $latestSalary->salaf_deducted }}</p>
                </div>
                <div class="salary-item">
                    <h4>Working Days</h4>
                    <p>{{ $latestSalary->working_days }}</p>
                </div>
                <div class="salary-item">
                    <h4>Unpaid Days</h4>
                    <p>{{ $latestSalary->unpaid_days }}</p>
                </div>
                <div class="salary-item">
                    <h4>Sick Leave</h4>
                    <p>{{ $latestSalary->sick_leave }}</p>
                </div>
                <div class="salary-item">
                    <h4>Deduction</h4>
                    <p>{{ $latestSalary->deduction }}</p>
                </div>
                <div class="salary-item">
                    <h4>Bonus</h4>
                    <p>{{ $latestSalary->bonus }}</p>
                </div>
                <div class="salary-item">
                    <h4>Salary to Be Paid</h4>
                    <p>{{ $latestSalary->salary_to_be_paid }}</p>
                </div>
            </div>
        </div>
        

        <!-- Previous Salaries Section -->
        <div class="list-section">
            <h3>Previous Salaries</h3>
            <div class="list-boxes">
                @foreach($salaries as $salary)
                    <div class="list-box" data-salary-id="{{ $salary->id }}">
                        <h4>{{ $salary->year }}-{{ $salary->month }}</h4>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script src="{{ asset('js/dashboard.js') }}"></script>

    @if(session('showUnauthorizedModal'))
        @include('components.unauthorized-modal')
    @endif

    <script src="{{ asset('js/modal.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
             var alert = document.querySelector('.alert-success');
             if (alert) {
                 setTimeout(function() {
                     alert.classList.add('alert-fade-out');
                     setTimeout(function() {
                         alert.remove();
                     }, 1000); // Match this duration with the CSS transition duration
                 }, 3500); // 3500 milliseconds = 3.5 seconds
             }
         });
     </script>
</body>
</html>
