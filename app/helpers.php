<?php

use routes\Router;
function route($name, $params = []): string
{
    foreach(Router::getRoutes() as $routeClass) {
        if ($routeClass->getName() === $name) {
            $route = trim($routeClass->getUrl(),'/');
            $routeExp = explode('/',$route);
            $path = '';
            $i = 0;
            $hasColon = false;
            foreach($routeExp as $part) {
                if (!str_starts_with($part,':')) {
                    $path .= $part . '/';
                } else {
                    $hasColon = true;
                    if (!isset($params[$i])) {
                        try {
                            throw new Exception("Route name $name requires parameter called " . substr($part, 1));
                        } catch(Exception $e) {
                            return $e;
                        }
                    }
                    $path .= $params[$i] . '/';
                    $i++;
                }
            }
            if ($hasColon) {
                return trim($path,'/') == '' ? $path : trim($path,'/');
            } else {
                return trim($routeClass->getUrl(),'/') == '' ? $routeClass->getUrl() : trim($routeClass->getUrl(),'/');
            }
        }
    }
    return '';
}

function dumper(...$stuffs): void
{
    foreach($stuffs as $stuff):
        echo '<pre>';
        var_dump($stuff);
        echo '</pre>';
    endforeach;
}

function snackToCamel(string $string): string
{
    $string = str_replace('_', '', ucwords($string,'_'));
    return lcfirst($string);
}

function propSetterName(string $string): string
{
    $string = str_replace('_', '', ucwords('set_'.$string,'_'));
    return lcfirst($string);
}

function pascalToSnake($str): string
{
    $snake = preg_replace('/[A-Z]/', '_$0', $str); // add underscore before each uppercase letter
    $snake = strtolower($snake); // convert all characters to lowercase
    $snake = ltrim($snake,'_'); //remove leading underscore
    return $snake;
}


function getIp () {
    $ip_list = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];
    foreach ($ip_list as $ip) {
        if (array_key_exists($ip,$_SERVER)) {
            return $_SERVER[$ip];
        }
    }
}