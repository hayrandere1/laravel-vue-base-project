<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssignRequestId
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $requestId = (string)Str::uuid();
        $context = [
            'request-id' => $requestId,
            'auth_id' => 'guest'
        ];
        if (Auth::check()) {
            $context['auth_id'] = Auth::user()->id;
        }
        Log::withContext($context);
        $response = $next($request);
        if ($response instanceof BinaryFileResponse) {
            return $response;
        } elseif ($response instanceof StreamedResponse) {
            return $response;
        } else {
            return $response->header('Request-Id', $requestId);
        }
    }
}
