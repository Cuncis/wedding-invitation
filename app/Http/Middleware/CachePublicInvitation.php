<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CachePublicInvitation
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->isSuccessful()) {
            $response->headers->set(
                'Cache-Control',
                'public, max-age=3600, s-maxage=3600, stale-while-revalidate=60'
            );
            $response->headers->set('Vary', 'Accept-Encoding');
        }

        return $response;
    }
}
