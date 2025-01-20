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
  <div class="card mb-4">
    <div class="card-body">
        <h5>Rules for Taking Days Off</h5>
        <p>
            Employees are entitled to request days off based on their allocated quota for the year. 
            - **Paid Days Off**: Can only be used if available in your quota. 
            - **Sick Leave**: Requires valid proof to be submitted. 
            - **Not Paid**: Can be requested without impacting your paid quota.
            All requests are subject to approval by your manager or the top manager. 
        </p>
    </div>
</div>
<div class="card mb-4">
    <div class="card-body">
        <h5>Available Paid Days Off </h5>
        <div class="d-flex flex-wrap">
            @if ($remainingAnnualDaysOff <= 0)
            <p>No paid days off available.</p>
        @else
            @for ($i = 0; $i < $remainingAnnualDaysOff; $i++)
                <div class="day-off bg-success text-white text-center m-1" style="width: 80px; height: 40px; line-height: 40px;">
                    Available
                </div>
            @endfor
        @endif
        </div>
    </div>
</div>
<div class="card mb-4">
    <div class="card-body">
        <h5>Request a Day Off</h5>
        <form action="{{ route('daysOffRequests.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" name="date" id="date" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="day_off_kind">Kind of Day Off</label>
                <select name="day_off_kind" id="day_off_kind" class="form-control" required>
                    <option value="Not Paid">Not Paid</option>
                    <option value="Yearly">Yearly</option>
                    <option value="Sick">Sick</option>
                </select>
            </div>
            <div class="form-group">
                <label for="proof">Proof (Optional)</label>
                <input type="file" name="proof" id="proof" class="form-control">
            </div>
            <div class="form-group">
                <label for="emp_notes">Notes</label>
                <textarea name="emp_notes" id="emp_notes" rows="3" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Request Day Off</button>
        </form>
    </div>
</div>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Date</th>
            <th>Day Off Kind</th>
            <th>Emp's notes</th>
            <th>Manager's notes</th>
            <th>Top Manager's notes</th>
            <th>Status</th>
            <th>proof</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($daysOffRequests as $request)
            <tr>
                <!-- Display user details -->
                <td>{{ $request->first_name }} {{ $request->last_name }}</td>
                <td>{{ $request->date }}</td>
                <td>{{ $request->day_off_kind }}</td>
                <!-- Display employee notes -->
                <td>{{ $request->emp_notes ?? 'No notes provided' }}</td>
                
                <!-- Display manager notes -->
                <td>{{ $request->manager_notes ?? 'N/A' }}</td>
                
                <!-- Display top manager notes -->
                <td>{{ $request->top_manager_notes ?? 'N/A' }}</td>
                
                <!-- Display status -->
                <td>{{ ucfirst($request->status) }}</td>
                
                <!-- Display proof -->
                <td>
                    @if ($request->proof)
                        <a href="{{ asset('storage/' . $request->proof) }}" target="_blank">View Proof</a>
                    @else
                        No Proof
                    @endif
                </td>
                
                <!-- Status update buttons -->
                <td>
                    <form action="{{ route('daysOffRequests.destroy', ['id' => $request->id]) }}" 
                          method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE') 
                        <button class="btn btn-sm btn-danger">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
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
