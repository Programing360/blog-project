<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Allow the request through only for authenticated administrators.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(
            $request->user() && $request->user()->role === 'admin',
            403,
            'Access Denied: Admins Only!'
        );

        return $next($request);
    }
}
