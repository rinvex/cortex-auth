<?php

declare(strict_types=1);

namespace Cortex\Auth\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

class AuthenticateSession
{
    /**
     * The authentication factory implementation.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param \Illuminate\Contracts\Auth\Factory $auth
     *
     * @return void
     */
    public function __construct(AuthFactory $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @throws \Illuminate\Auth\AuthenticationException
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $passwordHashKey = 'hash';

        if (! $request->hasSession() || ! $request->user()) {
            return $next($request);
        }

        if ($this->auth->viaRemember()) {
            $passwordHash = explode('|', $request->cookies->get($this->auth->getRecallerName()))[2] ?? null;

            if (! $passwordHash || $passwordHash !== $request->user()->getAuthPassword()) {
                $this->logout($request);
            }
        }

        if (! $request->session()->has($passwordHashKey)) {
            $this->storePasswordHashInSession($request, $passwordHashKey);
        }

        if ($request->session()->get($passwordHashKey) !== $request->user()->getAuthPassword()) {
            $this->logout($request);
        }

        return tap($next($request), function () use ($request, $passwordHashKey) {
            if (! is_null($this->auth->user())) {
                $this->storePasswordHashInSession($request, $passwordHashKey);
            }
        });
    }

    /**
     * Store the user's current password hash in the session.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $passwordHashKey
     *
     * @return void
     */
    protected function storePasswordHashInSession($request, $passwordHashKey)
    {
        if (! $request->user()) {
            return;
        }

        $request->session()->put([
            $passwordHashKey => $request->user()->getAuthPassword(),
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\AuthenticationException
     *
     * @return void
     */
    protected function logout($request)
    {
        $this->auth->logoutCurrentGuard();

        throw new AuthenticationException('Unauthenticated.', [$this->auth->getDefaultDriver()]);
    }
}
