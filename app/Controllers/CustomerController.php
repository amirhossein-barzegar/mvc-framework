<?php

namespace App\Controllers;

use App\Models\Customer\Customer;
use App\Models\Customer\CustomerLog;
use App\Models\Customer\CustomerPassword;
use App\Models\Customer\CustomerVisit;
use App\Requests\FormRequest;
use App\Requests\Request;
use App\Responses\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CustomerController extends BaseController
{
    public function register(Request $request, Response $response): Response
    {
        $response->setHeader('Content-Type', 'application/json');
        $data = $request->body();
        $formRequest = new FormRequest($data);
        $formRequest->rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
        ];
        $validate = $formRequest->validate($formRequest);
        if ($validate) {
            $customer = Customer::create([
                'c_first_name' => $data['first_name'],
                'c_last_name' => $data['last_name'],
                'c_phone' => $data['phone']
            ]);

            if ($customer) {
                $password = CustomerPassword::create([
                    'cp_password' => rand(100000,999999),
                    'cp_expire_at' => time()+60,
                    'cp_customer_id' => $customer->getCId(),
                    'cp_created_at' => time()
                ]);
                if ($password) {
                    $_SESSION['restrict_auth_attempt'] = time()+80;
                    $message = urlencode("سلام به تست فورکی خوش آمدید. \n رمز عبور شما {$password->getCpPassword()}");
                    file_get_contents("https://maharattvto.ir/sms-service.php?number={$customer->getCPhone()}&message={$message}");
                    $response->setBody([
                        'success' => 'Customer one-time password successfully created! please send this password in less than 60 second',
                        'password' => $password->getCpPassword()
                    ]);
                } else {
                    $response->setBody([
                        'status' => $_SESSION['errors']
                    ]);
                }
            } else {
                $response->setStatusCode(400);
                $response->setHeader('Content-Type', 'application/json');
                $response->setBody([
                    'error' => $_SESSION['error']
                ]);
            }

        } else {
            $response->setStatusCode(400);
            $response->setBody($request->sessions());
        }
        return $response;
    }

    public function customers(Request $request, Response $response): Response
    {
        $customers = Customer::all();
        $response->setHeader('Content-Type', 'application/json');
        $response->setBody($customers);
        return $response;
    }

    public function check(Request $request,Response $response) {
        $response->setBody($request);
        return $response;
        $token = $request->headers()['x-auth-token'];
        $jwt = base64_decode($token);
        $decoded = JWT::decode($jwt, new Key('secret', 'HS256'));
        $response->setHeader('Content-Type', 'application/json');
        $response->setBody(['check' => 'success','decrypt' => $decoded]);
        return $response;
    }

    public function logout(Request $request, Response $response): Response
    {
        $response->setHeader('Content-Type', 'application/json');
        if (!isset($request->headers()['x-auth-token'])) {
            $response->setBody([
                'state' => 'error',
                'message' => 'x-auth-token is not provided!'
            ]);
            return $response;
        }
        $token = $request->headers()['x-auth-token'];
        $jwt = base64_decode($token);
        $decoded = JWT::decode($jwt, new Key('secret', 'HS256'));
        $customerVisit = CustomerVisit::findBy('cv_customer_id', $decoded->customer_id);
        if ($customerVisit) {
//            $customerLog = CustomerLog::updateBy('cl_customer_id' , $customerVisit->getCvCustomerId());
            $response->setBody('You are successfully logged out!');
        } else {
            $response->setBody('Error');
        }
//        if (isset($_SESSION['customer_id']) && $_SESSION['customer_id'] === $decoded->customer_id) {
//            unset($_SESSION['customer_id']);
//            $response->setBody(['user' => $decoded, 'success'=>'Customer successfully logged out.']);
//        } else {
//            $response->setBody(['status' => 'failed', 'error' => 'No logged in customer founded!']);
//        }
        return $response;
    }

    public function login(Request $request, Response $response): Response
    {
        if (!isset($_SESSION['customer_id'])) {
            $response->setHeader('Content-Type', 'application/json');
            $data = $request->body();
            $formRequest = new FormRequest($data);
            $formRequest->rules = [
                'phone' => 'required',
            ];
            $validate = $formRequest->validate($formRequest);
            if ($validate) {
                $customer = Customer::findBy('c_phone', $data['phone']);
                if ($customer) {
                    $password = CustomerPassword::create([
                        'cp_password' => rand(100000,999999),
                        'cp_expire_at' => time()+60,
                        'cp_customer_id' => $customer->getCId(),
                        'cp_created_at' => time()
                    ]);
                    if ($password) {
                        $_SESSION['restrict_auth_attempt'] = time()+80;
                        $message = urlencode("سلام به تست فورکی خوش آمدید. \n رمز عبور شما {$password->getCpPassword()}");
                        file_get_contents("https://maharattvto.ir/sms-service.php?number={$customer->getCPhone()}&message={$message}");
                        $response->setBody([
                            'success' => 'Customer one-time password successfully created! please send this password in less than 60 second',
                            'password' => $password->getCpPassword()
                        ]);
                    } else {
                        $response->setBody([
                            'status' => 'error'
                        ]);
                    }
                } else {
                    $response->setBody([
                        'errors' => 'No customer found with this credentials!'
                    ]);
                }
            } else {
                $response->setBody([
                    'status' => 'error',
                    'message' => $_SESSION['errors']
                ]);
            }
        } else {
            $response->setBody([
                'errors' => 'Customer already signed in.'
            ]);
        }
        return $response;
    }

    public function confirm(Request $request, Response $response): Response
    {
        $response->setHeader('Content-Type', 'application/json');
        $data = $request->body();
        $formRequest = new FormRequest($data);
        $formRequest->rules = [
            'password' => 'required|min:3'
        ];
        $validate = $formRequest->validate($formRequest);
        if ($validate) {
            $customerPassword = CustomerPassword::findBy('cp_password',$data['password']);
            if ($customerPassword) {
                if ($customerPassword->getCpExpireAt() > time() && is_null($customerPassword->getCpUsedAt())) {
                    $customerPasswordUpdated = CustomerPassword::update($customerPassword->getCpId(),[
                        'cp_used_at' => time()
                    ]);
                    if ($customerPasswordUpdated) {
//                        $_SESSION['customer_id'] = $customerPasswordUpdated->getCpCustomerId();
                        $key = 'secret';
                        $payload = [
                            'customer_id' => $customerPasswordUpdated->getCpCustomerId(),
                            'agent' => $request->server()['HTTP_USER_AGENT'],
                            'ip' => getIp()
                        ];
                        $customerVisit = CustomerVisit::create([
                            'cv_customer_id' => $payload['customer_id'],
                            'cv_agent' => $payload['agent'],
                            'cv_ip' => $payload['ip'],
                            'cv_created_at' => time(),
                            'cv_modified_at' => time()
                        ]);
                        if ($customerVisit) {
                            $jwt = JWT::encode($payload, $key, 'HS256');
                            $token = base64_encode($jwt);
                            $response->setHeader('x-auth-token', $token);
                            $response->setBody([
                                'success' => 'you are successfully logged in!',
                                'customer_id' => $customerPasswordUpdated->getCpCustomerId(),
                            ]);
//                            CustomerVisit::create([
//                                'cl_agent' => $request->server()['HTTP_USER_AGENT'],
//                                'cl_customer_id' => $customerVisit->getCvCustomerId(),
//                                'cl_logged_in_at' => time()
//                            ]);
                        } else {
                            // some error messages
                        }

                    } else {
                        $response->setBody([
                            'errors' => 'Updating was failed!'
                        ]);
                    }
                } else {
                    $response->setBody([
                        'errors' => 'Password already expired or used!'
                    ]);
                }
            } else {
                $response->setBody([
                    'errors' => 'Incorrect password!'
                ]);
            }
        } else {
            $response->setBody([
                'errors' => $_SESSION['errors']
            ]);
        }
        return $response;
    }

    public function getCustomer(Request $request, Response $response)
    {
//        dump(CustomerPassword::findById($request->params['id'],'customer'));
        dump(Customer::all());
    }

    public function getPasswords(Request $request, Response $response)
    {
        dump(Customer::findById($request->params['id'],['passwords']));
    }
}