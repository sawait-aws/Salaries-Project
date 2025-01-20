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
       
      <form action="{{ route('boxes.store') }}" method="POST">
        @csrf
        <div>
            <label for="name">Box Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div>
            <label for="amount">Initial Amount:</label>
            <input type="number" id="amount" name="amount" required step="0.01" min="0">
        </div>
        <button type="submit">Create Box</button>
    </form>
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Box Name</th>
                <th scope="col">Amount</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($boxes as $box)
                <tr>
                    <td>{{ $box->name }}</td>
                    <td>{{ number_format($box->amount, 0, '.', '') }}</td>
                    <td>
                        <!-- Delete Button -->
                        <form action="{{ route('boxes.delete', $box->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <form method="POST" action="{{ route('transactions.store') }}">
        @csrf
        <div class="row mb-3">
            <!-- Sender Box -->
            <div class="col-md-4">
                <label for="sender_box" class="form-label">Sender Box</label>
                <select id="sender_box" name="sender_box" class="form-select" required>
                    <option value="" disabled selected>Select Sender</option>
                    @foreach($boxes as $box)
                        <option value="{{ $box->id }}" data-amount="{{ $box->amount }}">
                            {{ $box->name }} ({{ number_format($box->amount, 0, '.', '') }} available)
                        </option>
                    @endforeach
                </select>
            </div>
    
            <!-- Receiver Box -->
            <div class="col-md-4">
                <label for="receiver_box" class="form-label">Receiver Box</label>
                <select id="receiver_box" name="receiver_box" class="form-select" required>
                    <option value="" disabled selected>Select Receiver</option>
                    @foreach($boxes as $box)
                        <option value="{{ $box->id }}" data-amount="{{ $box->amount }}">
                            {{ $box->name }} ({{ number_format($box->amount, 0, '.', '') }} available)
                        </option>
                    @endforeach
                </select>
            </div>
    
            <!-- Transaction Amount -->
            <div class="col-md-4">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" id="amount" name="amount" class="form-control" min="0.01" step="0.01" placeholder="Enter amount" required>
            </div>
        </div>
    
        <div class="row mb-3">
            <!-- Commission Kind -->
            <div class="col-md-6">
                <label for="commission_kind" class="form-label">Commission Type</label>
                <select id="commission_kind" name="commission_kind" class="form-select" required>
                    <option value="" disabled selected>Select Commission Type</option>
                    <option value="percentage">Percentage</option>
                    <option value="static">Static</option>
                </select>
            </div>
    
            <!-- Commission Amount -->
            <div class="col-md-6">
                <label for="commission_amount" class="form-label">Commission Amount</label>
                <input type="number" id="commission_amount" name="commission_amount" class="form-control" min="0" step="0.01" placeholder="Enter commission amount" required>
            </div>
        </div>
    
        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary" id="submit_button">Submit Transaction</button>
    </form>
    
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Transaction ID</th>
                <th scope="col">Sender Box</th>
                <th scope="col">Sender Box Amount at that time</th>
                <th scope="col">Receiver Box</th>
                <th scope="col">Receiver Box Amount at that time</th>
                <th scope="col">Amount</th>
                <th scope="col">Commission Type</th>
                <th scope="col">Commission Amount</th>
                <th scope="col">Transaction Date</th>
                <th scope="col">Performed By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->id }}</td>
                    <td>{{ $transaction->sender_box }}</td>
                    <td>{{ number_format($transaction->sender_box_amount, 2, '.', '') }}</td>
                    <td>{{ $transaction->receiver_box }}</td>
                    <td>{{ number_format($transaction->receiver_box_amount, 2, '.', '') }}</td>
                    <td>{{ number_format($transaction->amount, 2, '.', '') }}</td>
                    <td>{{ ucfirst($transaction->commission_kind) }}</td> <!-- Capitalize commission type -->
                    <td>{{ number_format($transaction->commission_amount, 2, '.', '') }}</td>
                    <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $transaction->user_first_name }} {{ $transaction->user_last_name }}</td>
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
