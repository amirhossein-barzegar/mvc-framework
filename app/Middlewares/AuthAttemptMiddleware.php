<?php

namespace App\Middlewares;

use App\Requests\Request;
use App\Responses\Response;

class AuthAttemptMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Response $response, \Closure $next): Request|Response
    {
//        if (isset($_SESSION['restrict_auth_attempt']) && $_SESSION['restrict_auth_attempt'] > time()) {
//            $response->setHeader('Content-Type','application/json');
//            $response->setBody([
//                'restrict-auth-middleware' => 'After '.($_SESSION['restrict_auth_attempt']-time()).' second retry again.'
//            ]);
//            return $response;
//        }
        return $next($request);
    }
}