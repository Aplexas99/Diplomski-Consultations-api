<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CORS
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Origin, Authorization, 
        Accept, charset, boundary, Content-Length');
        header('Access-Control-Allow-Origin: http://localhost:4200/');
        header('Access-Control-Allow-Credentials: true');
        return $next($request);
    }
}
