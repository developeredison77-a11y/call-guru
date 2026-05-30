<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ((int) $request->user()?->type !== 1) {
            abort(403, 'Only superadmin users can access the dashboard.');
        }

        return $next($request);
    }
}
