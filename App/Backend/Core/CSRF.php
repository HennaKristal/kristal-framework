<?php namespace Backend\Core;
defined("ACCESS") or exit("Access Denied");

class CSRF
{
    public static function reset()
    {
        foreach ($_SESSION as $key => $value)
        {
            if (str_starts_with($key, "csrf_"))
            {
                unset($_SESSION[$key]);
            }
        }

        Session::add("csrf_default", bin2hex(random_bytes(32)));
    }

    public static function new($identifier = "default")
    {
        Session::add("csrf_" . $identifier, bin2hex(random_bytes(32)));  
    }

    public static function get($identifier = "default")
    {
        if (!Session::has("csrf_" . $identifier)) { return false; }
        return Session::get("csrf_" . $identifier);
    }

    public static function request($action)
    {
        echo "<input type='hidden' name='form_request' value='$action'>";
    }

    public static function create($identifier = "default")
    {
        if (!Session::has("csrf_" . $identifier))
        {
            self::new($identifier);
        }

        echo "<input type='hidden' name='csrf_identifier' value='" . $identifier . "'>";
        echo "<input type='hidden' name='csrf_token' value='" . Session::get("csrf_" . $identifier) . "'>";
    }
}
