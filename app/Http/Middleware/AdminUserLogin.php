<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminUserLogin
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\RedirectResponse|mixed|void
     */
    public function handle(Request $request, Closure $next)
    {
        if (is_numeric($request->segments()[2])) {
            if (is_null(Auth::guard('user')->user()) || $request->segments()[2] != Auth::guard('user')->user()->id) {
                $user = User::findOrFail($request->segments()[2]);
                if ($user->is_active == 1) {
                    Auth::guard('user')->login($user);
                } else {
                    return redirect()->to(\url('login'));
                }
            }
            return $next($request);
        }
    }
}
