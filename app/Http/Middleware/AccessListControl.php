<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Response;
use Route;

class AccessListControl
{
    public function handle($request, Closure $next)
    {
        if ( !Auth::user()->can(Route::currentRouteName())) {
            abort(401);
        }

        return $next($request);
    }
}