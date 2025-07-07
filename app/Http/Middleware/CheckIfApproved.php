<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        // Admins hebben altijd toegang
        if ($user && $user->is_admin) {
            return $next($request);
        }

        // Check of user approved is
        if ($user && $user->approved) {
            return $next($request);
        }

        abort(403, 'Uw account is nog niet goedgekeurd door de admin.');
    }

}
