<?php namespace Backend\Core;
defined("ACCESS") or exit("Access Denied");

/*==============================================================================*\
|  This class handles logic behind form requests class in the controllers folder |
\*==============================================================================*/

use \ReflectionMethod;

class FormRequest
{
    private $requestedMethod = "";

    public function __construct($parameters = array("allow_protected_calls" => false))
    {
        // Only handle real form submissions
        if ($_SERVER["REQUEST_METHOD"] !== "POST")
        {
            return;
        }

        // Verify required CSRF fields
        if (!isset($_POST["form_request"]) || !isset($_POST["csrf_token"]) || !isset($_POST["csrf_identifier"]))
        {
            if (REGENERATE_CSRF_ON_PAGE_REFRESH)
            {
                CSRF::reset();
            }
            return;
        }

        $csrfIdentifier = $_POST["csrf_identifier"];
        $csrfToken = $_POST["csrf_token"];
        $expectedToken = CSRF::get($csrfIdentifier);

        if ($csrfToken !== $expectedToken)
        {
            if (REGENERATE_CSRF_ON_PAGE_REFRESH)
            {
                CSRF::reset();
            }

            return;
        }

        CSRF::reset();

        $this->requestedMethod = $_POST["form_request"];

        if (!method_exists($this, $this->requestedMethod))
        {
            return;
        }

        $method = new ReflectionMethod($this, $this->requestedMethod);
        $allowProtectedCalls = isset($parameters["allow_protected_calls"]) && $parameters["allow_protected_calls"] === true;
        $isPublic = $method->isPublic();
        $isProtectedAndAllowed = $method->isProtected() && $allowProtectedCalls;

        if (!$isPublic && !$isProtectedAndAllowed)
        {
            return;
        }

        $requestData = array_merge($_POST, $_FILES);
        $this->{$this->requestedMethod}($requestData);
    }
}