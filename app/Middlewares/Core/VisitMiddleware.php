<?php

namespace App\Middlewares\Core;

use App\Middlewares\MiddlewareInterface;
use App\Models\Customer\CustomerVisit;
use App\Requests\Request;
use App\Responses\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class VisitMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Response $response, \Closure $next): Request|Response
    {
//        if (!isset($request->headers()['x-auth-token'])) {
//            $customerVisit = CustomerVisit::findBy('cs_agent', $request->server()['HTTP_USER_AGENT']);
//            if ($customerVisit && $customerVisit->getCsIp() === getIp()) {
//                dump('Hello there');die;
//            } elseif($customerVisit->getCsIp() !== getIp()) {
//                dump('your ip is invalid. access denied');die;
//            }
//            CustomerVisit::create([
//                'cv_agent' => $request->server()['HTTP_USER_AGENT'],
//                'cv_ip' => getIp(),
//    //            'cv_customer_id',
//    //            'cv_logged_in_at',
//    //            'cv_logged_out_at'
//            ]);
//            return $next($request);
//        }
//        $token = $request->headers()['x-auth-token'];
//        $jwt = base64_decode($token);
//        $decoded = JWT::decode($jwt, new Key('secret', 'HS256'));
////        if ($decoded)
        return $next($request);
    }
}
