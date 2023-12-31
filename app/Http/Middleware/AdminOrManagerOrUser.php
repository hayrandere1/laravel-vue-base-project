<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOrManagerOrUser
{
    protected $auth;

    /**
     * AdminOrUser constructor.
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (empty($guards)) {
            $guards = ['admin', 'manager', 'user'];
        }
        if ($this->auth->guard('admin')->check()) {
            if (str_starts_with($request->getPathInfo(), '/Admin')) {
                return $next($request);
            } else {
                $this->auth->guard('user')->logout();
                $this->auth->guard('manager')->logout();
            }
        }
        if ($this->auth->guard('manager')->check()) {
            if (str_starts_with($request->getPathInfo(), '/Manager')) {
                return $next($request);
            } else {
                $this->auth->guard('admin')->logout();
                $this->auth->guard('user')->logout();
            }
        }
        if ($this->auth->guard('user')->check()) {
            if (str_starts_with($request->getPathInfo(), '/User')) {
                return $next($request);
            } else {
                $this->auth->guard('admin')->logout();
                $this->auth->guard('manager')->logout();
            }
        }
        if ( !str_starts_with($request->getPathInfo(), '/Admin')
            && !str_starts_with($request->getPathInfo(), '/Manager')) {
            return $next($request);
        }

        throw new AuthenticationException(
            'Unauthenticated.', $guards, $this->redirectTo($request)
        );
    }

    private function redirectTo(Request $request)
    {
        if (str_starts_with($request->getPathInfo(), '/Admin')) {
            return route('admin.login');
        }

        if (str_starts_with($request->getPathInfo(), '/Manager')) {
            return route('manager.login');
        }
        if (str_starts_with($request->getPathInfo(), '/User')) {
            return route('user.login');
        }
        if (!$request->expectsJson()) {
            return route('guest.home');
        }
    }
}
