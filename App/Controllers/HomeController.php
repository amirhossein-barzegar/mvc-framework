<?php
namespace App\Controllers;

use App\Requests\FormRequest;
use App\Controllers\BaseController;

class HomeController extends BaseController {
    public function index() {
        $this->view('index');
    }

    public function postUser(FormRequest $request, $id , $name) {

        $request->rules = [
            'name' => 'required|min:5',
            'lastname' => 'required'
        ];

        $request->validate($request->request);
    }
}