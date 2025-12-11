<?php declare(strict_types=1); 
namespace Backend\Core;
defined("ACCESS") or exit("Access Denied");

use \ReflectionMethod;

class FormRequest
{
    public function __construct(array $parameters = array("allow_protected_calls" => false))
    {
        $csrfIdentifier = isset($_POST["csrf_identifier"]) ? $_POST["csrf_identifier"] : "";
        $csrfToken = isset($_POST["csrf_token"]) ? $_POST["csrf_token"] : "";

        $csrfData = Session::get("csrf_" . $csrfIdentifier);
        if (!$csrfData)
            return;

        $requestedMethod = $csrfData["formRequest"];
        $expectedToken = $csrfData["token"];

        if (REGENERATE_CSRF_ON_PAGE_REFRESH)
            CSRF::reset();

        // Only handle real form submissions
        if ($_SERVER["REQUEST_METHOD"] !== "POST")
            return;

        // Verify required CSRF fields
        if (empty($requestedMethod) || empty($expectedToken) || empty($csrfIdentifier) || empty($csrfToken))
            return;

        // Make sure CSRF token matches
        if ($csrfToken !== $expectedToken)
            return;

        // Make sure requested method exists
        if (!method_exists($this, $requestedMethod))
            return;

        // Reset CSRF on successful request
        CSRF::reset();

        $allowProtectedCalls = isset($parameters["allow_protected_calls"]) && $parameters["allow_protected_calls"] === true;
        $method = new ReflectionMethod($this, $requestedMethod);
        $isPublic = $method->isPublic();
        $isProtectedAndAllowed = $method->isProtected() && $allowProtectedCalls;

        if ($isPublic || $isProtectedAndAllowed)
        {
            $requestData = array_merge($_POST, $_FILES);
            $this->{$requestedMethod}($requestData);
        }
    }
}
