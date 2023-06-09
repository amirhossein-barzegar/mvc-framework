<?php

namespace App\Controllers;

class BaseController {
    /**
     * Show view file with params
     * @param string $view
     * @param string|array|null $params
     *
     * @return void
     */
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