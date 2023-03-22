<?php

namespace App\Controllers;

class BaseController {
    public function __construct() {
        // include 'Routes/route.php';
    }
}

function view(string $view,string|array $params = null) { 
    if (is_array($params)) {
        foreach($params as $key=>$value) {
            ${$key} = $value;
        }
    }
    unset($params);

    require_once 'Views/'.$view.'.php';
    // $view = new View('Views/'.$view.'.php',$params);
}

require_once 'Routes/web.php';