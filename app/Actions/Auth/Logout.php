<?php

namespace App\Actions\Auth;

use App\Actions\Concerns\RoutesScopedToGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

class Logout
{
    use AsAction;
    use RoutesScopedToGroup;

    public static function handle(string $guard = 'web'): void
    {
        Auth::guard($guard)->logout();
    }

    public function asController(Request $request)
    {
        $this->handle();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
