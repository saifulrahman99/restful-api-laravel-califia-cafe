<?php

namespace App\Http;

use App\Exceptions\Handler;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Middleware\TrustHosts;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;

class Kernel extends HttpKernel
{
    protected $middleware = [
        TrustHosts::class,
        TrustProxies::class,
        ValidateCsrfToken::class,
        SubstituteBindings::class,
    ];

    protected $middlewareGroups = [
        'api' => [
            ThrottleRequests::class . ':api',
            SubstituteBindings::class,
        ],
    ];
}
