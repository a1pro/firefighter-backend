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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the origin from the request headers
        $origin = $request->headers->get('Origin');

        // Define the allowed origins (adjust this based on your needs)
        $allowedOrigins = ['http://localhost:3000', 'https://firefighterweb.a1professionals.com','https://firefighterweb.a1professionals.net'];

        // Check if the origin is in the allowed list
        if (in_array($origin, $allowedOrigins)) {
            // Add the CORS headers to the response
            $response = $next($request)
                ->header('Access-Control-Allow-Origin', $origin)  // Allow only specific origin
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH')  // Allow these HTTP methods
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')  // Allow specific headers
                ->header('Access-Control-Allow-Credentials', 'true');  // Allow credentials (cookies, etc.)
        } else {
            // If the origin is not allowed, pass the request without setting CORS headers
            $response = $next($request);
        }

        // Handle OPTIONS request (CORS preflight)
        if ($request->getMethod() == "OPTIONS") {
            return response('', 204)
                ->header('Access-Control-Allow-Origin', $origin)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
                ->header('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }

}
