<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Manager Profile Section -->
        <div class="profile-section">
            <h2>{{ $manager->first_name }} {{ $manager->last_name }}</h2>
            <p>User ID: {{ $manager->user_id }}</p>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <ul class="nav-list">
        <li><a href="{{route ('manager.dashboard')}}">Salary</a></li>
        <li><a href="{{route ('tasks.create')}}">Tasks</a></li>
        <li><a href="{{route ('manager.dashboard')}}">Prizes</a></li>
        <li><a href="{{route ('manager.daysOff')}}">Days Off</a></li>
        <li><a href="{{route ('accounting.money')}}">acc</a></li>
      </ul>
        <!-- Employee List Section -->
        <div class="list-section">
            <h3>Employees</h3>
            <div class="list-boxes">
                @foreach($employees as $employee)
                    <a href="{{ route('manager.view.employee', $employee->id) }}" class="list-box">
                        <h4>{{ $employee->first_name }} {{ $employee->last_name }}</h4>
                        <p>User ID: {{ $employee->user_id }}</p>
                        <p>Position: {{ $employee->position }}</p>
                        <p>Joining Date: {{ $employee->joining_date }}</p>
                        <p>Email Address: {{ $employee->email_address }}</p>
                    </a>
                @endforeach
            </div>
        </div>
        <!-- Add Employee Form -->
        <form class="add-employee-form" action="{{ route('add.employee') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="text" name="position" placeholder="Position" required>
            <input type="date" name="joining_date" placeholder="Joining Date" required>
            <input type="text" name="email_address" placeholder="Email Address" required>
            <input type="number" name="user_id" placeholder="User ID" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Add Employee</button>
        </form>

        <div class="upload-csv-section">
            <h3>Upload Salary Data (CSV)</h3>
            <form action="{{ route('upload.salaries.csv') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="csv_file" accept=".csv" required>
                <button type="submit">Upload</button>
            </form>
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
