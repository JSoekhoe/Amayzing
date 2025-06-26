<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || $request->user()->email !== 'jamaytuller@gmail.com') {
            abort(403, 'Geen toegang: alleen admins');
        }

        return $next($request);
    }
}
