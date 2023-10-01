<?php

namespace App\Http\Middleware;

use App\Models\Manager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminManagerLogin
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\RedirectResponse|mixed|void
     */
    public function handle(Request $request, Closure $next)
    {
        if (is_numeric($request->segments()[2])) {

            if (is_null(Auth::guard('manager')->user()) || $request->segments()[2] != Auth::guard('manager')->user()->id) {
                $manager = Manager::findOrFail($request->segments()[2]);
                if ($manager->is_active == 1) {
                    Auth::guard('manager')->login($manager);
                } else {
                    return redirect()->to(\url('login'));
                }
            }
            return $next($request);
        }
    }
}
