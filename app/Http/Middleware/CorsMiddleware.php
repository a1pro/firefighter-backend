<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedOrigins = [
            'http://localhost:3000',
            'http://localhost:5173',
            'https://firefighterweb.a1professionals.com',
            'https://firefighterweb.a1professionals.net',
        ];

        $origin = $request->headers->get('Origin');

        $headers = [
            'Access-Control-Allow-Origin'      => in_array($origin, $allowedOrigins) ? $origin : '',
            'Access-Control-Allow-Methods'     => 'GET, POST, PUT, DELETE, PATCH, OPTIONS',
            'Access-Control-Allow-Headers'     => 'Origin, Content-Type, Accept, Authorization, X-Requested-With',
            'Access-Control-Allow-Credentials' => 'true',
        ];

        // Handle preflight (OPTIONS) request
        if ($request->getMethod() === 'OPTIONS') {
            return response()->json('CORS preflight', 204, $headers);
        }

        // Proceed with the request and attach CORS headers
        $response = $next($request);

        foreach ($headers as $key => $value) {
            if ($value !== '') {
                $response->headers->set($key, $value);
            }
        }

        return $response;
    }
}
