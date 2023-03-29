<?php

namespace App\Controllers;

class BaseController {
    public function __construct() {
    }
    
    protected function view(string $view,string|array $params = null): void
    {
        if (is_array($params)) {
            foreach($params as $key=>$value) {
                ${$key} = $value;
            }
        }
        unset($params);
        
        if (str_contains($view, '.')) {
            $view_exp = explode('.', $view);
            $view = implode('/', $view_exp);
        }
        
        $viewName = 'Views/'.$view.'.php';
        try {
            if (file_exists($viewName)) require_once $viewName;
            else throw new \Exception("File $viewName not found!");
        } catch(\Exception $e) {
            echo $e->getMessage();
        }
    }
}



require_once 'Routes/web.php';