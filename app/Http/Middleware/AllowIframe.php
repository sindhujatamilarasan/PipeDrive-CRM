<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowIframe
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Remove Laravel's default frame-blocking
        $response->headers->remove('X-Frame-Options');

        // Allow embedding from Pipedrive
        $response->headers->set('Content-Security-Policy', "frame-ancestors 'self' https://*.pipedrive.com");

        return $response;
    }
}
