<?php

namespace App\Actions\Auth;

use App\Http\Middleware\SetGroupFromDomainMiddleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class Login
{
    use AsAction;

    public static function routes(Router $router): void
    {
        $router->get('/login', static::class)->name('login');
        $router->post('/login', static::class);
    }

    public function handle(
        string $email,
        string $password,
        bool $remember = false,
    ) {
        return Auth::attempt(['email' => $email, 'password' => $password], $remember);
    }

    public function getControllerMiddleware(): array
    {
        return ['guest', SetGroupFromDomainMiddleware::class];
    }

    public function asController(Request $request)
    {
        if ($request->isMethod('GET')) {
            return view('auth.login');
        }

        $throttle_key = Str::transliterate(Str::lower($request->string('email')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttle_key, 5)) {
            $seconds = RateLimiter::availableIn($throttle_key);

            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! $this->handle(
            email: $request->input('email'),
            password: $request->input('password'),
            remember: $request->boolean('remember')
        )) {
            RateLimiter::hit($throttle_key);
            throw ValidationException::withMessages(['email' => trans('auth.failed')]);
        }

        RateLimiter::clear($throttle_key);

        $request->session()->regenerate();

        return redirect()->intended(url('/'));
    }
}
