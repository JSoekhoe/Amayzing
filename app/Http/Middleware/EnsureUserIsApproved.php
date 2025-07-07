<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->approved) {
            auth()->logout();
            return redirect()->route('not.approved');
        }

        return $next($request);
    }
}
