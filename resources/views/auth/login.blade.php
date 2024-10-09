<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <!-- Link to your CSS file -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2 class="login-title">Sign in</h2>
            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="form-group">
                    <input id="user_id" type="text" class="form-control @error('user_id') is-invalid @enderror" name="user_id" value="{{ old('user_id') }}" required autofocus placeholder="User ID">
                    @error('user_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="Password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn-login">Sign in</button>
                </div>
            </form>
        </div>
    </div>
    @if(session('showUnauthorizedModal'))
        @include('components.unauthorized-modal')
    @endif

    <script src="{{ asset('js/modal.js') }}"></script>
</body>
</html>
