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
  <h1>Task List</h1>
  <div class="mb-3">
    <label for="filter-priority" class="form-label">Filter by Priority:</label>
    <select id="filter-priority" class="form-select" onchange="filterTasks()">
        <option value="all">All</option>
        <option value="high">High</option>
        <option value="normal">Normal</option>
        <option value="low">Low</option>
    </select>
</div>

<div class="mb-3">
    <label for="filter-status" class="form-label">Filter by Status:</label>
    <select id="filter-status" class="form-select" onchange="filterTasks()">
        <option value="all">All</option>
        <option value="pending">Pending</option>
        <option value="in progress">In Progress</option>
        <option value="in review">In Review</option>
        <option value="complete">Complete</option>
    </select>
</div>
  <table>
      <thead>
          <tr>
              <th>Name</th>
              <th>Description</th>
              <th>Status</th>
              <th>Priority</th>
              <th>Review Details</th>
          </tr>
      </thead>
      <tbody>
          @foreach ($tasks as $task)
              <tr>
                  <td>{{ $task->name }}</td>
                  <td>{{ $task->description ?? 'No description' }}</td>
                  <td>{{ ucfirst($task->status) }}</td>
                  <td class="{{ $task->priority }}">{{ ucfirst($task->priority) }}</td>
                  <td>{{ $task->review_details ?? 'N/A' }}</td>
                  <td> @if ($task->status != 'pending' && $task->status != 'complete')
                    <form action="{{ route('tasks.updateStatus', ['id' => $task->id, 'direction' => 'left']) }}" method="POST" style="display:inline;">
                        @csrf
                        <button class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left"></i>
                        </button>
                    </form>
                @endif
                @if ($task->status != 'complete' && 
                ($task->status != 'in review' || (Auth::user()->position == 'manager' && $task->status == 'in review')))
               <form action="{{ route('tasks.updateStatus', ['id' => $task->id, 'direction' => 'right']) }}" method="POST" style="display:inline;">
                   @csrf
                   <button class="btn btn-sm btn-primary">
                       <i class="fa fa-arrow-right"></i>
                   </button>
               </form>
           @endif
                  </td>
              </tr>
          @endforeach
      </tbody>
  </table>
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
 <script>
    function filterTasks() {
    // Get the selected values from the dropdowns
    const priorityFilter = document.getElementById('filter-priority').value;
    const statusFilter = document.getElementById('filter-status').value;

    // Get all the table rows
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        // Get the priority and status cells
        const priority = row.querySelector('td:nth-child(4)').textContent.trim().toLowerCase();
        const status = row.querySelector('td:nth-child(3)').textContent.trim().toLowerCase();

        // Check if the row matches the filters
        const matchesPriority = priorityFilter === 'all' || priority === priorityFilter;
        const matchesStatus = statusFilter === 'all' || status === statusFilter;

        // Show or hide the row based on the filters
        row.style.display = matchesPriority && matchesStatus ? '' : 'none';
    });
}
</script>
</body>
</html>