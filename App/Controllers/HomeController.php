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
            'name' => 'required|min:5'
        ];

        $request->validate($request->request);
    }

    public function getUser($id){
        echo $id;
    }

    public function postUser(FormRequest $request,$id,$stuff,$some) {
        dumper($request);
        dumper($id);
        dumper($stuff);
    }

    public function showUser($id, $roleId) {
        echo "show User Method id is $id and roleId is $roleId";
    }

    public function showPost($postId) {
        echo "showPost() $postId";
    }
}