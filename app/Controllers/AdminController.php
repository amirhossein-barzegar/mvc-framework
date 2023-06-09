<?php

namespace App\Controllers;

use App\Models\Admin;
use App\Requests\FormRequest;
use App\Requests\Request;
use App\Responses\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AdminController extends BaseController
{
//    public function register(Request $request, Response $response): Response
//    {
//        $response->setHeader('Content-Type', 'application/json');
//        $data = $request->body();
//        $formRequest = new FormRequest($data);
//        $formRequest->rules = [
//            'first_name' => 'required',
//            'last_name' => 'required',
//            'username' => 'required',
//            'password' => 'required|confirmed',
//            'password_confirmation' => 'required'
//        ];
//        if ($formRequest->validate($formRequest)) {
//            $admin = Admin::create([
//                'a_first_name' => $data['first_name'],
//                'a_last_name' => $data['last_name'],
//                'a_user_name' => $data['username'],
//                'a_password' => password_hash($data['password'], PASSWORD_DEFAULT),
//                'a_created_at' => time(),
//                'a_modified_at' => time()
//            ]);
//            if ($admin) {
//                $token = Admin\AdminToken::create([
//                    'at_agent' => $request->server()['HTTP_USER_AGENT'],
//                    'at_ip' => getIp(),
//                    'at_status' => Admin\AdminToken::ENABLED_TOKEN,
//                    'at_admin_id' => $admin->getAId(),
//                    'at_created_at' => time()
//                ]);
//                if ($token) {
//                    $key = 'secret';
//                    $payload = [
//                        'token_id' => $token->getAtId(),
//                        'ip_address' => $token->getAtIp(),
//                        'agent' => $token->getAtAgent()
//                    ];
//                    $jwt = JWT::encode($payload,$key,'HS256');
//                    $token = base64_encode($jwt);
//                    $response->setHeader('x-admin-token', $token);
//                    $response->setBody([
//                        'state' => 'success',
//                        'message' => 'Admin registered successfully!'
//                    ]);
//                }
//            }
//        } else {
//            $response->setBody("Error");
//        }
//        return $response;
//    }

    public function login(Request $request, Response $response): Response
    {
        $data = $request->body();
        $formRequest = new FormRequest($data);
        $formRequest->rules = [
            'username' => 'required',
            'password' => 'required'
        ];
        if ($formRequest->validate()) {
            $validatedData = $formRequest->getRequest();
            $admin = Admin::findBy('a_user_name', $validatedData['username']);
            if ($admin) {
                if (password_verify($data['password'], $admin->getAPassword())) {
                    $token = Admin\AdminToken::create([
                        'at_agent' => $request->server()['HTTP_USER_AGENT'],
                        'at_ip' => getIp(),
                        'at_status' => Admin\AdminToken::ENABLED_TOKEN,
                        'at_admin_id' => $admin->getAId(),
                        'at_created_at' => time()
                    ]);
                    if ($token instanceof Response) {
                        return $token;
                    } elseif ($token) {
                        $key = 'secret';
                        $payload = [
                            'token_id' => $token->getAtId(),
                            'ip_address' => $token->getAtIp(),
                            'agent' => $token->getAtAgent()
                        ];
                        $jwt = JWT::encode($payload,$key,'HS256');
                        $token = base64_encode($jwt);
                        $response->setHeader('x-admin-token', $token);
                        $response->setBody([
                            'state' => 'success',
                            'message' => 'Admin successfully logged in!',
                            'admin_id' => $admin->getAId()
                        ]);
                    } else {
                        $response->setBody([
                            'state' => 'error',
                            'error_code' => 31,
                            'message' => 'Something went\'s wrong on creating!'
                        ]);
                    }
                } else {
                    $response->setBody([
                        'state' => 'error',
                        'error_code' => 11,
                        'message' => 'Invalid password given.'
                    ]);
                }
            } else {
                $response->setBody([
                    'state' => 'error',
                    'error_code' => 10,
                    'message' => 'Invalid username given.'
                ]);
            }
        } else {
            $response->setBody([
                'state' => 'error',
                'error_code' => 1,
                'message' => 'Invalid parameters passed.'
            ]);
        }
        return $response;
    }

    public function logout(Request $request, Response $response): Response
    {
        if (isset($request->headers()['x-admin-token'])) {
            $jwt = base64_decode($request->headers()['x-admin-token']);
            try {
                $decoded = JWT::decode($jwt,new Key('secret','HS256'));
                $token = Admin\AdminToken::findById($decoded->token_id);
                if ($token && $token->getAtStatus() === Admin\AdminToken::ENABLED_TOKEN) {
                    $update = Admin\AdminToken::update($token->getAtId(),[
                        'at_status' => Admin\AdminToken::EXPIRED_TOKEN,
                        'at_expired_at' => time()
                    ]);
                    if ($update instanceof Response) {
                        return $update;
                    } elseif ($update) {
                        $response->setBody([
                            'state' => 'success',
                            'message' => 'Admin successfully logged out!'
                        ]);
                    } else {
                        $response->setBody([
                            'state' => 'error',
                            'error_code' => 31,
                            'message' => 'Something went\'s wrong on updating!'
                        ]);
                    }
                } else {
                    $response->setBody([
                        'state' => 'error',
                        'error_code' => 21,
                        'message' => 'Expired x-admin-token provided!'
                    ]);
                }
            } catch(\Exception $e) {
                $response->setBody([
                    'state' => 'error',
                    'error_code' => 20,
                    'message' => $e->getMessage()
                ]);
            }
        } else {
            $response->setBody([
                'state' => 'error',
                'error_code' => 22,
                'message' => 'No x-admin-token provided!',
            ]);
        }
        return $response;
    }
}