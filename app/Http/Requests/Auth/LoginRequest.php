<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
	public function rules(): array
	{
		return [
			'email' => ['required', 'string', 'email'],
			'password' => ['required', 'string'],
		];
	}
	
	public function authenticate(): void
	{
		$this->ensureIsNotRateLimited();
		
		if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
			RateLimiter::hit($this->throttleKey());
			
			throw ValidationException::withMessages([
				'email' => trans('auth.failed'),
			]);
		}
		
		RateLimiter::clear($this->throttleKey());
	}
	
	protected function ensureIsNotRateLimited(): void
	{
		if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
			return;
		}
		
		event(new Lockout($this));
		
		$seconds = RateLimiter::availableIn($this->throttleKey());
		
		throw ValidationException::withMessages([
			'email' => trans('auth.throttle', [
				'seconds' => $seconds,
				'minutes' => ceil($seconds / 60),
			]),
		]);
	}
	
	protected function throttleKey(): string
	{
		return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
	}
}
