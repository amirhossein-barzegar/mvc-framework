<?php 

namespace App\Requests;

class FormRequest {
    public array $rules = [];
    
    public function __construct(
        public array $request
    ) 
    {}

    /**
     * Validate request with our custom rules
     *
     * @param array $request
     * @return void
     */
    public function validate(FormRequest $request) : void
    {
        session_unset(); 
        $this->runRules($this->rules,$request->request);
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
     *
     * @return void
     */
    public function redirectBack(): void
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        } else {
            http_response_code(403);
            die(json_encode([
                'error' => $_SESSION['errors'],
                'message' => 'Validation Error'
            ]));
        }
    }

    /**
     * Run all defined rules
     *
     * @param array $rules
     * @param array $request
     * @return void
     */
    public function runRules(array $rules,array $request): void
    {
        foreach($rules as $fieldName=>$rule):
        $rule_scopes = explode('|',$rule);
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
                if ($result === false || isset($_SESSION['errors'])) {
                    $this->redirectBack();
                }
            endforeach;
        endforeach;
    } 
}