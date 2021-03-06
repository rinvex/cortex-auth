<?php

declare(strict_types=1);

namespace Cortex\Auth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string|null              ...$guards
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return intend([
                    'url' => route($request->accessarea().'.home'),
                    'with' => ['success' => trans('cortex/auth::messages.authenticated')],
                ]);
            }
        }

        return $next($request);
    }
}
