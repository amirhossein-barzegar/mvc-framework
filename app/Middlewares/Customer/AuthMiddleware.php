<?php

namespace App\Middlewares\Customer;

use App\Middlewares\MiddlewareInterface;
use App\Requests\Request;
use App\Responses\Response;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Response $response,\Closure $next): Request|Response
    {
//        if (!isset($_SESSION['customer_id'])) {
//            $response = new Response();
//            $response->setHeader('Content-Type', 'application/json');
//            $response->setBody([
//                'auth-middleware' => 'Only authenticated customers can access to this route!'
//            ]);
//            return $response;
//        }
        return $next($request);
    }
}