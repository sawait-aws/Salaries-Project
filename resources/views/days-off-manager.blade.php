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
                <td>{{ $request->first_name }} {{ $request->last_name }}</td>
                <td>{{ $request->date }}</td>
                <td>{{ $request->day_off_kind }}</td>
                <td>{{ $request->emp_notes ?? 'No notes provided' }}</td>
                <!-- Editable Manager Notes -->
                <td>
                    <input type="text" name="manager_notes_{{ $request->id }}" 
                           id="manager_notes_{{ $request->id }}" class="form-control"
                           value="{{ $request->manager_notes }}" placeholder="Add or edit notes">
                </td>
                <td>{{ $request->top_manager_notes ?? 'N/A' }}</td>
                <td>{{ ucfirst($request->status) }}</td>
                <td>
                    @if ($request->proof)
                        <a href="{{ asset('storage/' . $request->proof) }}" target="_blank">View Proof</a>
                    @else
                        No Proof
                    @endif
                </td>
                <!-- Approve and Reject Buttons -->
                <td>
                    <!-- Approve Form -->
                    <form action="{{ route('daysOffRequests.approve', ['id' => $request->id]) }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="manager_notes" id="approve_notes_{{ $request->id }}">
                        <button type="submit" class="btn btn-sm btn-success" 
                                onclick="document.getElementById('approve_notes_{{ $request->id }}').value = document.getElementById('manager_notes_{{ $request->id }}').value">
                            Approve
                        </button>
                    </form>
                    <!-- Reject Form -->
                    <form action="{{ route('daysOffRequests.reject', ['id' => $request->id]) }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="manager_notes" id="reject_notes_{{ $request->id }}">
                        <button type="submit" class="btn btn-sm btn-danger" 
                                onclick="document.getElementById('reject_notes_{{ $request->id }}').value = document.getElementById('manager_notes_{{ $request->id }}').value">
                            Reject
                        </button>
                    </form>
                </td>
            </tr>            
            @endforeach
        </tbody>
    </table>
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
