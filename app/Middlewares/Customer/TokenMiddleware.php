<?php

namespace App\Middlewares\Customer;

use App\Middlewares\MiddlewareInterface;
use App\Models\Customer\CustomerVisit;
use App\Requests\Request;
use App\Responses\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Response $response, \Closure $next): Request|Response
    {

//        $response->setHeader('Content-Type', 'application/json');
        return $next($request);
    }
}