<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="profile-section">
            <h2 class="profile-title">Edit Employee Details</h2>
        </div>

        <!-- Display Validation Errors -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Display Success Message -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Edit Employee Section -->
        <div class="edit-employee-container">
            <form action="{{ route('edit.employee', $employee->id) }}" method="POST" enctype="multipart/form-data" class="form-container add-employee-form">
                @csrf
                @method('POST')
                <div class="form-group">
                    <label for="first_name" class="form-label">First Name:</label>
                    <input type="text" name="first_name" id="first_name" value="{{ $employee->first_name }}" required class="form-control">
                </div>
            
                <div class="form-group">
                    <label for="last_name" class="form-label">Last Name:</label>
                    <input type="text" name="last_name" id="last_name" value="{{ $employee->last_name }}" required class="form-control">
                </div>
            
                <div class="form-group">
                    <label for="user_id" class="form-label">User ID:</label>
                    <input type="number" name="user_id" id="user_id" value="{{ $employee->user_id }}" required class="form-control">
                </div>
            
                <div class="form-group">
                    <label for="password" class="form-label">Password (optional):</label>
                    <input type="password" name="password" id="password" class="form-control">
                </div>
            
                <div class="form-buttons">
                    <button type="submit" class="btn-login">Update Employee</button>
                    <a href="{{ route('manager.view.employee', $employee->id) }}" class="btn-cancel">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
