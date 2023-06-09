<?php

namespace Routes;

use App\Middlewares\MiddlewareInterface;
use App\Responses\Response;
use App\Requests\Request;

class Router
{
    private static array $routes = [];
    private static array $groupedRoutes = [];
    private static self $instance;
    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {}

    public function addRoute($method, $url, $controller, $action, $name = '', $parameters = []): self
    {
        self::$routes[] = new Route($method, $url, $controller, $action, $name, $parameters);
        return new self();
    }

    public function haveRoute($method, $url, $controller, $action, $name = '', $parameters = []): self
    {
        self::$groupedRoutes[] = new Route($method, $url, $controller, $action, $name, $parameters);
        return new self();
    }

    public function haveMiddleware($middleware): self
    {
        foreach(self::$groupedRoutes as $route)
            $route->addMiddleware($middleware);
        return new self();
    }

    public function addMiddleware($middleware): self
    {
        self::$routes[array_key_last(self::$routes)]->addMiddleware($middleware);
        return new self();
    }

    public function addGroup(\Closure $router,MiddlewareInterface|array $middlewares): void
    {
        $router($this);
        if (is_iterable($middlewares)) {
            foreach($middlewares as $middleware) {
                $this->haveMiddleware($middleware);
            }
        } else {
            $this->haveMiddleware($middlewares);
        }
    }

    public static function getRoutes(): array
    {
        return self::$routes;
    }

    public function dispatch(Request $request, Response $response): void
    {
        $route_found = false;
        $response_code = null;
        $response_headers = [];
        $response_body = null;
        $allRoutes = array_merge(self::$routes,self::$groupedRoutes);

        foreach($allRoutes as $routeClass) {
            $isMatch = true;

            // match HTTP method
            if ($routeClass->getMethod() != $request->server()['REQUEST_METHOD'] || $routeClass->getUrl() === '/something/:id/someone') {
                continue;
            }

            $route = trim($routeClass->getUrl(),'/');
            $path = trim($request->server()['REQUEST_URI'],'/');
            $remindedRoute = '';
            $remindedPath = '';
            $routeExp = explode('/',$route);
            $pathExp = explode('/',$path);

            foreach ($routeExp as $key=>$part) {
                // Check if path parts not equal to route parts
                if (!isset($pathExp[$key])) {
                    $isMatch = false;
                }
                if (str_starts_with($part, ':')) {
                    continue;
                }
                if (isset($pathExp[$key])) {
                    $remindedPath .= $pathExp[$key] . '/';
                }
                $remindedRoute .= $part . '/';
            }

            foreach ($pathExp as $key=>$part) {
                if (!isset($routeExp[$key])) {
                    $isMatch = false;
                }
            }

            $remindedRoute = trim($remindedRoute,'/');
            $remindedPath = trim($remindedPath,'/');

//            if ($routeClass->getUrl() == '/law-collections') {
//                dumper($remindedRoute,$remindedPath,$this->isMatch);
//            }

            // Reminded URLs are match (by removing dynamic part of these URLs)
            if ($remindedRoute === $remindedPath && $isMatch) {
                // match parameters in URL
                $parameters = [];
                foreach($routeExp as $key => $part) {
                    if (str_starts_with($part,':')) {
                        $parameters[substr($part, 1)] = $pathExp[$key];
                    }
                }

                // Run the matched route Middlewares
                if (is_iterable($routeClass->getMiddlewares())) {
                    foreach ($routeClass->getMiddlewares() as $middleware) {
                        $middleware = new $middleware();
                        $middlewareResponse = $middleware->handle($request, $response, function($request) {
                            return $request;
                        });
                        if ($middlewareResponse instanceof Response) {
                            // set headers and body from response
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
                        }
                        if (!$middlewareResponse instanceof Request) {
                            return;
                        }
                    }
                }


                // match the controller and action
                $controller_name = $routeClass->getController();
                $action_name = $routeClass->getAction();

                // run the action method on the controller
                $request->params = $parameters;

                $route_found = true;

                // Default headers
                $response->setHeader('Access-Control-Allow-Origin', '*');
                $response->setHeader('Content-Type', 'application/json; charset=UTF-8');
                $response->setHeader('Access-Control-Allow-Methods', $request->server()['REQUEST_METHOD']);
                $response->setHeader('Access-Control-Allow-Headers', 'Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Headers');

                // Create an instance of the controller
                $controller = new $controller_name();
                $response = $controller->$action_name($request,$response);
                $response_body = $response->getBody();


                // set headers and body from response
                foreach($response->getHeaders() as $header=>$value) {
                    $value = trim($value);
                    $value = str_replace(PHP_EOL, '', $value);
                    header($header . ': ' . $value);
                }
            }
        }


        if (!$route_found) {
            $response = new Response();
            $response->setHeader('Content-Type', 'text/html');
            // set headers and body from response
            foreach($response->getHeaders() as $header=>$value) {
                $value = trim($value);
                $value = str_replace(PHP_EOL, '', $value);
                header($header . ': ' . $value);
            }
            $response->setStatusCode(404);
            http_response_code($response->getStatusCode());
            $response->setBody('404 Not Found');
            echo $response->getBody();
            exit;
        }

        // Show result body
        echo match (gettype($response_body)) {
            'array' => json_encode($response_body),
            'object' => json_encode(get_object_vars($response_body)),
            default => $response_body,
        };
    }
}