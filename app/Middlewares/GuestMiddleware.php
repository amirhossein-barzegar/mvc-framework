<?php

namespace App\Middlewares;

use App\Requests\Request;
use App\Responses\Response;

class GuestMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Response $response, \Closure $next): Request|Response
    {
//        if (isset($_SESSION['customer_id']) || isset($_SESSION['admin_id'])) {
//            $response->setHeader('Content-Type', 'application/json');
//            $response->setBody([
//                'guest-middleware' => 'Only regular users can visit this route!'
//            ]);
//            return $response;
//        }
        return $next($request);
    }
}