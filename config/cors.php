<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Define paths that are allowed

    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', '*'], // Allow all HTTP methods

    'allowed_origins' => [
        'http://localhost:3000',  // Local dev environment (React)
        'https://firefighterweb.a1professionals.com',  // Front-end domain
        'https://firefighterweb.a1professionals.net',  // Another front-end domain
        // You can add additional domains here if necessary
    ],

    'allowed_headers' => ['*'],  // Allow all headers for the preflight request

    'exposed_headers' => [],  // Any headers you want to expose to the client

    'max_age' => 0,  // Cache duration for CORS preflight request

    'supports_credentials' => true, 
    
];
