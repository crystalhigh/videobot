<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Log;

use App\User;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards) {
        try {
            $user = $request->user();
            if ($user->require_login == 1) {
                return response('Need to re-login', 401);
            }
            if (!$user || !$user->id) {
                return response('User not found', 401);
            }
            $u = User::find($user->id);
            if ($u) {
                return $next($request);
            } else {
                return response('User not found', 401);
            }
        } catch (\Throwable $e) {
            Log::error('guard error: ' . $e->getMessage());
            Log::error('handle guard error');
            return response('Handle guard error', 401);
        }
    }
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
