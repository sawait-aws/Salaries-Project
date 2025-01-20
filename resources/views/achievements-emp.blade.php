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
            <p>Email Address: {{ $employee->email_address }}</p>
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
<ul class="nav-list">
    <li><a href="{{route ('employee.dashboard')}}">Salary</a></li>
    <li><a href="{{route ('tasks.dashboard')}}">Tasks</a></li>
    <li><a href="{{route ('emp.achievements')}}">Achievements</a></li>
    <li><a href="{{route ('emp.daysOff')}}">Days Off</a></li>
  </ul>
        
{{-- employee/achievements.blade.php --}}
<div class="container">
    <h2>Your Achievements</h2>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Achievement</th>
                        <th>Month/Year</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Employee of the Month</td>
                        <td>{{ !empty($employeeOfTheMonth) ? implode(', ', $employeeOfTheMonth) : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Top ATV</td>
                        <td>{{ !empty($topAtv) ? implode(', ', $topAtv) : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Top Performer</td>
                        <td>{{ !empty($topPerformer) ? implode(', ', $topPerformer) : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Top Quality</td>
                        <td>{{ !empty($topQuality) ? implode(', ', $topQuality) : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Top Upselling</td>
                        <td>{{ !empty($topUpselling) ? implode(', ', $topUpselling) : 'N/A' }}</td>
                    </tr>
                </tbody>
            </table>
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