<?php

namespace Routes;

use App\Requests\FormRequest;

class Route {
    private static ?string $uri = null;
    private static array $uris = [];
    private static ?string $routeName;
    private static ?string $request_uri;
    private static array $uriParamsKeys = [];
    private static array $uriParamsValues = [];
    private static array $uriParams;
    private static int $fakeUriKey = 0;
    private static ?array $fakeUris;
    private static ?string $remindUri;
    private static ?string $controller;
    private static ?string $controllerName;
    private static ?string $method;
    private static array $routesHaveResponse;

    public function __construct()
    {
//        echo "<h5>Construct</h5>";
        self::$controller = null;
        self::$controllerName = null;
        self::$method = null;
        self::$uri = null;
        self::$uriParams = [];
        self::$uriParamsKeys = [];
        self::$uriParamsValues = [];
        self::$remindUri = null;
    }

    /**
     * Handle Get Route
     * @param string $uri
     * @param string $action
     * @return Route
     */
    public static function get(string $uri, string $action): self
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            // add / to begin of the uri
            if ($uri[0] != "/"){
                self::$uri = "/" . $uri;
            } else {
                self::$uri = $uri;
            }

            if (!str_ends_with(self::$uri, '/')) {
                self::$uri = self::$uri . '/';
            }

            $action = explode('@',$action);
            self::$controller = $action[0];
            self::$controllerName = 'App\Controllers\\'.self::$controller;
            self::$method = end($action);

            // should have / in begin of uri
            if ($_SERVER['REQUEST_URI'][0] != "/"){
                self::$request_uri  = substr_replace($_SERVER['REQUEST_URI'],"/",0,0);
            } else {
                self::$request_uri = $_SERVER['REQUEST_URI'];
            }

            // should have / in end of uri
            if (!str_ends_with(self::$request_uri, "/")){
                self::$request_uri[strlen(self::$request_uri)] = "/";
            }

            // Add current uri to web uris list
            if (str_contains(self::$uri,'{')) {
                self::generateFakeUri(self::$uri);
                if (isset(self::$fakeUris)) {
                    self::$uris[self::$fakeUris[self::$fakeUriKey]] = null;
                }
            } else {
                self::$uris[self::$uri] = null;
            }

            self::checkAndSendResponse();

            return new self;
        }
        return new self;
    }

    /**
     * Named Route
     */
    public static function name(string $name = null): void
    {
        static::$uris[static::$uri] = $name;
        echo '<pre>';
        var_dump('name');
        echo '</pre>';
        var_dump(self::$uris);
    }

    /**
     * Handle Post Route
     */
    public static function post(string $uri, string $action): self
    {
        if (isset($_POST['csrf_field']) && $_SERVER['REQUEST_METHOD'] == 'POST')
        {
            // add / to begin of the uri
            if ($uri[0] != "/"){
                self::$uri = "/" . $uri;
            } else {
                self::$uri = $uri;
            }

            if (!str_ends_with(self::$uri, '/')) {
                self::$uri = self::$uri . '/';
            }

            $action = explode('@',$action);
            self::$controller = $action[0];
            self::$controllerName = 'App\Controllers\\'.self::$controller;
            self::$method = end($action);
            self::$request_uri = $_SERVER['REQUEST_URI'];

            // should have / in begin of uri
            if ($_SERVER['REQUEST_URI'][0] != "/"){
                self::$request_uri  = substr_replace($_SERVER['REQUEST_URI'],"/",0,0);
            } else {
                self::$request_uri = $_SERVER['REQUEST_URI'];
            }

            // should have / in end of uri
            if (!str_ends_with(self::$request_uri, "/")){
                self::$request_uri = self::$request_uri . "/";
            }
            // Add current uri to web uris list
            if (str_contains(self::$uri,'{')) {
                $values = [];
                $values = self::generateFakeUri(self::$uri);
                self::$uriParamsValues = $values;
                if (isset(self::$fakeUris)) {
                    self::$uris[self::$fakeUris[self::$fakeUriKey]] = null;
                }
            } else {
                self::$uris[self::$uri] = null;
            }

            $request = new FormRequest($_POST);
            self::checkAndSendResponse(request: $request, values: self::$uriParamsValues);

            return new self;
        }
        return new self;
    }

    # Generate fake uri
    public static function generateFakeUri(): array
    {
        $uri_exp = explode("{",self::$uri);
        foreach($uri_exp as $part) {
            // id}/role/
            if (str_contains($part, "}")) {
                $contains_end_bracket = explode("}",$part);
                foreach($contains_end_bracket as $value) {
                    if ($value != null) {
                        if (str_contains($value, "/")) {
                            self::$fakeUris[self::$fakeUriKey].=$value;
                            self::$remindUri = str_replace($value, "", self::$remindUri);
                            $uriParamValue = explode('/', self::$remindUri)[0];
                            if ($uriParamValue != '') self::$uriParamsValues[] = $uriParamValue;
                            self::$remindUri = substr(self::$remindUri, strlen($uriParamValue));
                        } else {
                            self::$uriParamsKeys[] = $value;
                            self::$fakeUris[self::$fakeUriKey].="*";
                        }
                    }
                }
            } else { // /users/
                self::$remindUri = str_replace($part, "", self::$request_uri);
                $uriParamValue = explode('/', self::$remindUri)[0];
                if ($uriParamValue != '') self::$uriParamsValues[] = $uriParamValue;
                self::$remindUri = substr(self::$remindUri, strlen($uriParamValue));
                if (isset(self::$fakeUris[self::$fakeUriKey])) self::$fakeUris[self::$fakeUriKey].=$part;
                else self::$fakeUris[self::$fakeUriKey] = $part;
            }
        }
        foreach(self::$fakeUris as $fakeUriKey=>$fakeUri) {
            if (!str_ends_with($fakeUri,'/')) {
                $fakeUri .= "/";
                self::$fakeUris[$fakeUriKey] = $fakeUri;
            }
            foreach(self::$uriParamsKeys as $key=>$paramKey) {
                self::$uriParams[self::$fakeUris[$fakeUriKey]][$paramKey] = isset(self::$uriParamsValues[$key]) ? self::$uriParamsValues[$key]:null;
            }
            return self::$uriParamsValues;
        }
    }

    # Fake Uri Condition
    public static function fakeUriCondition(): bool
    {
        if (isset(self::$fakeUris[self::$fakeUriKey])) {
            $fakeUriWithoutStar = explode("*",self::$fakeUris[self::$fakeUriKey]);
            foreach($fakeUriWithoutStar as $uri_part) {
                if (!str_contains(self::$request_uri, $uri_part)) return false;
            }
            if (substr_count(self::$fakeUris[self::$fakeUriKey], '/') != substr_count(self::$request_uri, '/')) return false;
            return true;
        } else {
            return false;
        }
    }

    public static function sendResponse($request = null,$params = []): self
    {
        require_once 'App/Controllers/' . self::$controller . '.php';
        $instance = new self::$controllerName;
        if (isset($params)) {
            if (isset($request)) call_user_func([$instance, self::$method],$request,...$params);
            else call_user_func([$instance,self::$method],...$params);
        } else {
            if (isset($request)) call_user_func([$instance, self::$method],$request);
            else call_user_func([$instance,self::$method]);
        }
        return new self;
    }

    public static function checkAndSendResponse($request = null,$values = []): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if (self::$request_uri == self::$uri) {
                self::sendResponse();
                self::$routesHaveResponse[self::$uri] = true;
            } elseif (key_exists(self::$uri,self::$uris)) {
                // Uri is Correct but should not have response
                self::$routesHaveResponse[self::$uri] = false;
            } elseif (self::fakeUriCondition()) {
                self::sendResponse(request: $request, params: self::$uriParamsValues);
                self::$routesHaveResponse[self::$uri] = true;
            } else {
                self::$routesHaveResponse[self::$uri] = false;
            }
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (self::$request_uri == self::$uri) {
                self::sendResponse(request: $request);
                self::$routesHaveResponse[self::$uri] = true;
            } elseif (key_exists(self::$uri,self::$uris)) {
                // Uri is Correct but should not have response
                self::$routesHaveResponse[self::$uri] = false;
            } elseif (self::fakeUriCondition()) {
                self::sendResponse(request: $request, params: self::$uriParamsValues);
                self::$routesHaveResponse[self::$uri] = true;
            } else {
                self::$routesHaveResponse[self::$uri] = false;
            }
        }
    }

    public function __destruct() {
        self::$fakeUriKey++;
    }

    public static function execute(): void
    {
        foreach(self::$routesHaveResponse as $routeHaveResponse) {
            if ($routeHaveResponse) {
                return;
            }
        }
        // Uri is Invalid
        http_response_code(404);
        echo 'Page not found 404'.'<br>';
        return;
    }
}



