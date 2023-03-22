<?php

namespace Routes;

use App\Requests\FormRequest;

class Route {
    public static string $uri;
    public static array $uris = [];
    public static ?string $routeName;
    public static ?string $request_uri;
    public static array $uriParamsKeys = [];
    public static array $uriParamsValues = [];
    public static array $uriParams;
    public static ?string $fakeUri;
    public static ?string $remindUri;

    /**
     * Handle Get Route
     * @param string $uri
     * @param string $action
     * @return Route
     */
    public static function get(string $uri, string $action)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {

            // Reset Properties start
            self::$uriParamsKeys = [];
            self::$uriParamsValues = [];
            self::$fakeUri = null;
            self::$remindUri = null;
            // Reset Properties end





            if ($uri[0] != "/"){
                self::$uri = substr_replace($uri,"/",0,0);
            } else {
                self::$uri = $uri;
            }


            $action = explode('@',$action);
            $controller = $action[0];
            $controllerName = 'App\Controllers\\'.$controller;
            $method = end($action);

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
            if (str_contains($uri,'{')) {
                self::generateFakeUri($uri);
                if (isset(self::$fakeUri)) {
                    self::$uris[self::$fakeUri] = null;
                }
            } else {
                self::$uris[$uri] = null;
            }

            // Do something if uris is match is match
            if (self::$request_uri == $uri) {
                require_once 'App/Controllers/'.$controller.'.php';
                $instance = new $controllerName;
                call_user_func([$instance,$method]);
                return new self;
            } elseif (key_exists($uri,self::$uris)) {
                // Uri is Currect
            } elseif (str_contains($uri, '{')) {
                // this uri have parameters!
                if (self::fakeUriCondition()) {
                    // Param uri is Currect
                    require_once 'App/Controllers/'.$controller.'.php';
                    $instance = new $controllerName;
                    call_user_func([$instance,$method],...self::$uriParamsValues);
                    return new self;
                } else {
                    // Uri is Invalid
                    http_response_code(404);
                    echo 'Get Page not found 404'.'<br>';
                }
            } else {
                // Uri is Invalid
                http_response_code(404);
                echo 'Get Page not found 404'.'<br>';
            }
            dumper(self::$uriParams ?? '');
            return new self;
        }
    }

    /**
     * Named Route
     */
    public static function name(string $name = null) 
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
    public static function post(string $uri, string $action) 
    {
        if (isset($_POST['csrf_field']) && $_SERVER['REQUEST_METHOD'] == 'POST')
        {
            self::$uri = $uri;
            self::$uris[$uri] = null;
            $action = explode('@',$action);
            $controller = $action[0];
            $controllerName = 'App\Controllers\\'.$controller;
            $method = end($action);
            self::$request_uri = $_SERVER['REQUEST_URI'];
            if (self::$request_uri == $uri) {
                require_once 'App/Controllers/'.$controller.'.php';
                $instance = new $controllerName;
                $request = new FormRequest($_POST);
                call_user_func([$instance,$method],$request);
                return new self;
            } elseif (key_exists($uri,self::$uris)) {
                /* 
                if ($uri[0] != "/"){
                    $uri = substr_replace($uri,"/",0,0);
                }
                if (str_contains($uri,'{')) {
                    $exp_open_bracket = explode('{',$uri);
                    $paramIndex = 0;
                    foreach($exp_open_bracket as $open_bracket) {
                        $exp_close_bracket = explode('}',$open_bracket);
                        foreach($exp_close_bracket as $key=>$param) {
                            if (str_contains($param,"/")) {
                                $paramIndex += strlen($param);
                                $remindUri = substr(self::$request_uri,$paramIndex);
                                if (str_contains($remindUri,'/')) {
                                    $paramVal = explode('/',$remindUri)[0];
                                    $paramIndex += strlen($paramVal);
                                } else {
                                    $paramVal = $remindUri;
                                    $paramIndex += strlen($paramVal);
                                }
                                self::$uriParamsValues[] = $paramVal;
                            } else {
                                if ($param == "") continue;
                                self::$uriParamsKeys[] = $param;
                            }
                        }
                    }
                    foreach(self::$uriParamsValues as $key=>$paramValue) {
                        self::$uriParams[self::$uriParamsKeys[$key]] = $paramValue;
                    }
                    require_once 'App/Controllers/'.$controller.'.php';
                    $instance = new $controllerName;
                    $request = new FormRequest($_POST);
                    call_user_func([$instance,$method],$request,...self::$uriParamsValues);
                    return new self;
                    
                } else {
                    return new self;
                }
                */
            } else {
                http_response_code(404);
                echo 'Post Page not found 404';
            }
        }
    }

    # Generate fake uri
    public static function generateFakeUri(string $uri) 
    {
        $uri_exp = explode("{",$uri);
        foreach($uri_exp as $part) {
            // id}/role/
            if (str_contains($part, "}")) {
                $contains_end_bracket = explode("}",$part);
                foreach($contains_end_bracket as $value) {
                    if ($value != null) {
                        if (str_contains($value, "/")) {
                            self::$fakeUri.=$value;
                            self::$remindUri = str_replace($value, "", self::$remindUri);
                            $uriParamValue = explode('/', self::$remindUri)[0];
                            self::$uriParamsValues[] = $uriParamValue;
                            self::$remindUri = substr(self::$remindUri, strlen($uriParamValue));
                        } else {
                            self::$uriParamsKeys[] = $value;
                            self::$fakeUri.="*";
                        }
                    }
                }
            } else { // /users/
                self::$remindUri = str_replace($part, "", self::$request_uri);
                $uriParamValue = explode('/', self::$remindUri)[0];
                self::$uriParamsValues[] = $uriParamValue;
                self::$remindUri = substr(self::$remindUri, strlen($uriParamValue));
                if (isset(self::$fakeUri)) self::$fakeUri.=$part;
                else self::$fakeUri = $part;
            }
        }

        if (!str_ends_with(self::$fakeUri,'/')) {
            self::$fakeUri[strlen(self::$fakeUri)] = "/";
        }

        foreach(self::$uriParamsKeys as $key=>$paramKey) {
            self::$uriParams[self::$fakeUri][$paramKey] = self::$uriParamsValues[$key];
        }
    }

    # Fake Uri Condition
    public static function fakeUriCondition(): bool
    {
        if (isset(self::$fakeUri)) {
            $fakeUriWithoutStar = explode("*",self::$fakeUri);
            foreach($fakeUriWithoutStar as $uri_part) {
                if (!str_contains(self::$request_uri, $uri_part)) return false;
            }
            if (substr_count(self::$fakeUri, '/') != substr_count(self::$request_uri, '/')) return false;
            return true;
        } else {
            return false;
        }
    }
}



