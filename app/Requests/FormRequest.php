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
     * @return bool
     */
    public function validate() : bool
    {
        session_unset();
        return $this->runRules($this->rules,$this->request);
    }

    /**
     * Field under the validation must be fill
     */
    public function required(array $request, string $fieldName) : bool
    {
        if (!isset($request[$fieldName])) {
            $_SESSION['errors'][$fieldName] = "{$fieldName} field is required!";
            $_SESSION['requests'] = $request;
            return false;
        } else {
            return true;
        }
    }

    public function string(array $request, string $fieldName): bool
    {
        if (!is_string($request[$fieldName]) || mb_strlen($request[$fieldName]) < 1) {
            return false;
        }
        return true;
    }

    public function enum(array $request, string $fieldName,$values): bool
    {
        $accepted_values = explode(',',$values);

        if (in_array($request[$fieldName],$accepted_values)) {
            return true;
        }
        return false;
    }

    public function optional(array $request, string $fieldName): bool
    {
        if (!isset($request[$fieldName]) || $request[$fieldName] == '') {
             $this->request[$fieldName] = null;
        }
        return true;
    }

    /**
     * Field under the validation must have minimum length
     */
    public function min(array $request, string $fieldName, int $length) : bool
    {
        if (mb_strlen($request[$fieldName]) < $length) {
            $_SESSION['errors'][$fieldName] = "Minimum length for {$fieldName} field is {$length} characters";
            $_SESSION['requests'] = $request;
            return false;
        } else {
            return true;
        }
    }

    public function confirmed(array $request, string $fieldName) : bool
    {
        if (isset($request[$fieldName.'_confirmation'])) {
            if ($request[$fieldName . '_confirmation'] !== $request[$fieldName]) {
                $_SESSION['errors'][$fieldName] = "Password and its confirmation is not match.";
                $_SESSION['requests'] = $request;
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    public function notnull(array $request, string $fieldName) : bool
    {
        if (!isset($request[$fieldName])) {
            unset($this->request[$fieldName]);
        }
        return true;
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
            echo json_encode([
                'error' => $_SESSION['errors'],
                'message' => 'Validation Error'
            ]);
        }
    }

    /**
     * Run all defined rules
     *
     * @param array $rules
     * @param array $request
     * @return bool
     */
    public function runRules(array $rules,array $request): bool
    {
        foreach($rules as $fieldName=>$rule):
        $rule_scopes = explode('|',$rule);
            foreach($rule_scopes as $scope):
                $result = false;
                if (str_contains($scope,':')) {
                    $scope_parts = explode(':',$scope);
                    $method = $scope_parts[0];
                    $value = end($scope_parts);
                    if (method_exists($this,$method)) {
                        $result = call_user_func([$this,$method],$request,$fieldName,$value);
                    }
                } else {
                    $method = $scope;
                    if (method_exists($this,$method)) {
                        $result = call_user_func([$this,$method],$request,$fieldName);
                    }
                }
                // If validation rule return false
                if (!$result) {
                    return false;
                }
            endforeach;
        endforeach;
        return true;
    }

    public function getRequest(string $prefix = ''): array
    {
        $validatedData = [];
        foreach($this->request as $field => $value) {
            $validatedData[$prefix.$field] = $value;
        }
        return $validatedData;
    }
}