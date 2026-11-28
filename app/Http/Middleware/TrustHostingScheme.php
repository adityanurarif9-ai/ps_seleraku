<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;

class TrustHostingScheme extends TrustProxies
{
    // The public edge forwards to this container over the hosting private network.
    // Direct requests from public client addresses never gain proxy trust.
    protected function proxies()
    {
        return app()->environment('production') ? ['127.0.0.1', '::1', '10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', 'fc00::/7'] : [];
    }

    protected function headers()
    {
        return Request::HEADER_X_FORWARDED_PROTO;
    }
}
