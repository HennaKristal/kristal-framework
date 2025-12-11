<?php declare(strict_types=1); 
namespace Backend\Core;
defined("ACCESS") or exit("Access Denied");

class CSRF
{
    // Resets all CSRF data from session
    public static function reset(): void
    {
        $sessionVariables = Session::getAll();

        foreach ($sessionVariables as $key => $value)
        {
            if (str_starts_with($key, "csrf_"))
            {
                Session::remove($key);
            }
        }
    }

    // Add CSRF:create("identifier", "formRequest") inside a form template to create CSRF protected form requests
    public static function create(string $identifier, string $formRequest): void
    {
        $token = bin2hex(random_bytes(32));

        Session::add("csrf_" . $identifier, [
            "token" => $token,
            "formRequest" => $formRequest,
        ]);

        echo "<input type='hidden' name='csrf_identifier' value='$identifier'>";
        echo "<input type='hidden' name='csrf_token' value='" . $token . "'>";
    }
}
