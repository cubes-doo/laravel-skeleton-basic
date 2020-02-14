<?php

namespace App\Http\Middleware;

use Closure;

class AcceptJson
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string|null              $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($request->is('api/*')) {
            $request->headers->set('Accept', 'application/json', true);
        }

        return $next($request);
    }
}
