<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     * @throws AuthorizationException
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->isAdmin()) {
            return $next($request);
        }
        return throw new AuthorizationException();
    }
}
