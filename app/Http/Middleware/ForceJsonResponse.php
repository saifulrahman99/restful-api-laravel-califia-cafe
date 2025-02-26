<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceJsonResponse
{
    public function handle(Request $request, Closure $next)
    {
        // Paksa header "Accept" menjadi JSON
        $request->headers->set('Accept', 'application/json');

        $response = $next($request);

        // Jika response bukan JSON, ubah menjadi JSON
        if (!$response->headers->has('Content-Type') || $response->headers->get('Content-Type') !== 'application/json') {
            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }
}
