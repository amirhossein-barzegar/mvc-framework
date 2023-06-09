<?php

namespace App;

use App\Middlewares\Middleware;
use App\Migrations\Migration;
use App\Responses\Response;
use App\Requests\Request;
use Config\DB;
use Routes\Router;

class Core
{
    private static self $instance;
    public Router $router;
    protected Request $request;
    protected Response $response;
    protected array $middlewares = [];

    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    private function __construct() {
        // Load required classes
        $this->request = new Request();
        $this->router = Router::getInstance();
        $this->response = new Response();
        require_once __DIR__ . '/helpers.php';
        require_once dirname(__DIR__) . '/jdf.php';
        DB::instance();
//        Migration::handle();
    }

    public function run(): void
    {
        // call the middleware functions after processing the request
        foreach(array_reverse($this->middlewares) as $middleware) {
            $middleware = new $middleware();
            $middlewareResponse = $middleware->handle($this->request, $this->response, function ($request) {
                return $request;
            });
            if ($middlewareResponse instanceof Response) {
                // set headers and body from middlewareResponse
                foreach($middlewareResponse->getHeaders() as $header=>$value) {
                    $value = trim($value);
                    $value = str_replace(PHP_EOL, '', $value);
                    header($header . ': ' . $value);
                }
                echo match (gettype($response_body = $middlewareResponse->getBody())) {
                    'array' => json_encode($response_body),
                    'object' => json_encode(get_object_vars($response_body)),
                    default => $response_body,
                };
            } elseif (!$middlewareResponse instanceof Request) {
                return;
            }
        }

        // dispatch the request to a matching route
        $this->router->dispatch($this->request,$this->response);
    }

    public function addMiddleware($middleware): void
    {
        $this->middlewares[] = $middleware;
    }
}