<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        \Log::info('User Role:', [$request->user()->role]);
        \Log::info('Roles Passed:', $roles);
    
        if (in_array($request->user()->role, $roles)) {
            return $next($request);
        }
    
        return redirect('admin/dashboard')->with('info', 'You are not authorised person this url.');
    }
    
}
