<?php
namespace App\Controllers;

use App\Requests\FormRequest;
use App\Controllers\BaseController;

class HomeController extends BaseController {
    public function __construct() {
        parent::__construct();
    }
    public function index() {
        return view('index');
    }

    public function register() {
        echo 'amirrrrrr';
    }

    public function login() {
        echo 'Loginnnnn';
    }

    public function postLogin(FormRequest $request) {

        $request->rules = [
            'name' => 'required|min:5',
            'lastname' => 'required'
        ];

        $request->validate($request->request);

        echo "post User";
        dd($request);
    }

    public function postUser(FormRequest $request,$id, $name) {
        dd($request, $id, $name);
    }

    public function showUser($id, $roleId) {
        echo "show User Method id is $id and roleId is $roleId"."<br/>";
    }

    public function showPost($postId) {
        echo "showPost() $postId"."<br/>";
    }

    public function some($some) {
        echo "Some method $some param"."<br/>";
    }
}