<?php

namespace App\Middlewares;

use App\Requests\Request;
use App\Responses\Response;

class Middleware implements MiddlewareInterface
{
    public function handle(Request $request, Response $response, \Closure $next): Request|Response {
        if ($request == 'amirhossein') {
            return $next($request);
        } else {
            echo 'Error';
        }
    }
}