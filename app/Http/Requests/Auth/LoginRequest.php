<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'numeric'], // Validate user_id as numeric and required
            'password' => ['required'], // Validate password as required
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (!Auth::attempt(['user_id' => $this->user_id, 'password' => $this->password], $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'user_id' => __('The provided credentials are incorrect.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        throw ValidationException::withMessages([
            'user_id' => __('Too many login attempts. Please try again in a few minutes.'),
        ]);
    }

    public function throttleKey(): string
    {
        return strtolower($this->input('user_id')) . '|' . $this->ip();
    }
}
