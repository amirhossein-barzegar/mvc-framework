<?php

namespace App\Middlewares;

use App\Models\Admin\AdminToken;
use App\Requests\Request;
use App\Responses\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthAdminMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Response $response, \Closure $next): Request|Response
    {
        $response->setHeader('Content-Type','application/json');
        if (!isset($request->headers()['x-admin-token'])) {
            $response->setBody([
                'status' => 'error',
                'error_code' => 22,
                'message' => 'No x-admin-token provided.'
            ]);
            return $response;
        }
        $token = $request->headers()['x-admin-token'];
        $jwt = base64_decode($token);
        try {
            $decoded = JWT::decode($jwt, new Key('secret', 'HS256'));
        } catch(\Exception $e) {
            $response->setBody([
                'status' => 'error',
                'error_code' => 20,
                'message' =>  $e->getMessage()
            ]);
            return $response;
        }
        $adminToken = AdminToken::findById($decoded->token_id);
        if (!$adminToken || in_array($adminToken->getAtStatus(),[AdminToken::EXPIRED_TOKEN,AdminToken::EXPIRED_TOKEN]) || !is_null($adminToken->getAtExpiredAt())) {
            $response->setBody([
                'status' => 'error',
                'error_code' => 21,
                'message' => 'Expired x-admin-token provided!'
            ]);
            return $response;
        }
        return $next($request);
    }
}