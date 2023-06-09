<?php

namespace App\Middlewares;

use App\Requests\Request;
use App\Responses\Response;

interface MiddlewareInterface
{
    public function handle(Request $request, Response $response,\Closure $next): Request|Response;
}