<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $role =  auth()->user()->role->name;
        if($role == 'Student') {
            return $next($request);
        }
        return response('Unauthorized.', 401);
    }
}
