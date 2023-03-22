<?php 

namespace App\Requests;

class FormRequest {
    public array $rules = [];
    public function __construct(
        /**
         * Form post request
         */
        public array $request
    ) 
    {}

    /**
     * 
     * @param array $request
     * @return void
     */
    public function validate(array $request) : void 
    {
        // if (!empty($_SESSION)) {
        //     $this->redirectBack();
        // }
        session_unset(); 
        $this->runRules($this->rules,$request);
    }

    /**
     * Field under the validation must be fill
     */
    public function required(array $request, string $fieldName) : bool
    {
        if (!isset($request[$fieldName]) || strlen($request[$fieldName]) < 1) {
            $_SESSION['errors'][$fieldName] = "{$fieldName} field is required!";
            $_SESSION['requests'] = $request;
            return false;
        } else {
            return true;
        }
    }

    /**
     * Field under the validation must have minimum length
     */
    public function min(array $request, string $fieldName, int $length) : bool
    {
        if (strlen($request[$fieldName]) < $length) {
            $_SESSION['errors'][$fieldName] = "Minimum length for {$fieldName} field is {$length} characters";
            $_SESSION['requests'] = $request;
            return false;
        } else {
            return true;
        }
    }

    /**
     * Redirect to previous route
     */
    public function redirectBack() {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

    /**
     * Run all defined rules
     */
    public function runRules($rules,$request)
    {
        foreach($rules as $fieldName=>$rule):
        $rule_scopes = array_reverse(explode('|',$rule));
            foreach($rule_scopes as $scope):
                $result = false;
                if (str_contains($scope,':')) {
                    $scope_parts = explode(':',$scope);
                    $method = $scope_parts[0];
                    $value = end($scope_parts);
                    $result = call_user_func([$this,$method],$request,$fieldName,$value);
                } else {
                    $method = $scope;
                    $result = call_user_func([$this,$method],$request,$fieldName);
                }

                // If validation rule return false redirect back...
                if ($result === false) {
                    $this->redirectBack();
                }
            endforeach;
        endforeach;
    } 
}