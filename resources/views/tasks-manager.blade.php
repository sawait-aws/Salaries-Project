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


      <h1>Add Task</h1>
      <form action="{{ route('tasks.store') }}" method="POST">
          @csrf
  
          <!-- Task Name -->
          <div class="form-group">
              <label for="name">Task Name</label>
              <input type="text" name="name" id="name" class="form-control" required>
          </div>
  
          <!-- Description -->
          <div class="form-group">
              <label for="description">Description</label>
              <input type="text" name="description" id="description" class="form-control">
          </div>
  
          <!-- Priority -->
          <div class="form-group">
              <label for="priority">Priority</label>
              <select name="priority" id="priority" class="form-control" required>
                  <option value="high">High</option>
                  <option value="normal">Normal</option>
                  <option value="low">Low</option>
              </select>
          </div>
  
          <!-- Employees -->
          <div class="form-group">
              <label for="employees">Assign Employees</label>
              <div id="employee-list">
                  @foreach ($employees as $employee)
                      <button type="button" class="btn btn-outline-primary m-1" onclick="addEmployee(
        {{ $employee->user_id }},
        '{{ $employee->first_name }}',
        '{{ $employee->last_name }}'
    )">
                          {{ $employee->first_name }} {{$employee->last_name}}
                      </button>
                  @endforeach
              </div>
          </div>
  
          <!-- Selected Employees -->
          <input type="hidden" name="emp" id="selected-emps">
          <div id="selected-employee-list" class="mt-2">
              <strong>Selected Employees:</strong>
              <ul id="selected-emps-list"></ul>
          </div>
  
          <!-- Submit -->
          <button type="submit" class="btn btn-success mt-3">Add Task</button>
      </form>


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
     <script>
        const selectedEmployees = [];
    
        function addEmployee(user_id,first_name,last_name) {
            console.log("Adding employee:", { user_id, first_name, last_name }); // Debugging
            if (!selectedEmployees.some(emp => emp.user_id === user_id)) {
                selectedEmployees.push({user_id,first_name,last_name});
                console.log("Selected Employees:", selectedEmployees); // Debugging
                updateSelectedEmployees();
            }
            else{
                console.log("Employee already exists:", user_id); // Debugging
            }
        }
        function removeEmployee(user_id) {
    const index = selectedEmployees.findIndex(emp => emp.user_id === user_id);
    if (index !== -1) {
        selectedEmployees.splice(index, 1); // Remove employee from the array
        updateSelectedEmployees();
    }
}
        function updateSelectedEmployees() {
            const empInput = document.getElementById('selected-emps');
            const empList = document.getElementById('selected-emps-list');
            console.log("Updating selected employees list...");


            empInput.value = JSON.stringify(selectedEmployees.map(emp => emp.user_id));
            empList.innerHTML = '';
            console.log("Hidden input value:", empInput.value);
            selectedEmployees.forEach(emp => {
                const li = document.createElement('li');
                li.textContent = emp.first_name + ' ' + emp.last_name;
                const removeButton = document.createElement('button');
        removeButton.textContent = 'Remove';
        removeButton.className = 'btn btn-danger btn-sm ms-2'; // Styling
        removeButton.onclick = () => removeEmployee(emp.user_id);

        // Append the remove button to the list item
        li.appendChild(removeButton);
                empList.appendChild(li);
            });
        }
    </script>
</body>
</html>