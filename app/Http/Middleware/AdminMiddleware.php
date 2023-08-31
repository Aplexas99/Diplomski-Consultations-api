<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $role =  auth()->user()->role->name;
        if($role == 'Admin') {
            return $next($request);
        }
        return response('Unauthorized.', 401);
    }
}
