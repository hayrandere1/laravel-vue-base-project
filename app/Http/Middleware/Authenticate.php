<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
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
