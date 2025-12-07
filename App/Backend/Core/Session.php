<?php namespace Backend\Core;
defined("ACCESS") or exit("Access Denied");


class Session
{
    public static function initialize()
    {
        self::start();
    }


    public static function start()
    {
        // Start session if it is not yet active
        if (!self::isActive())
        {
            self::startSession(self::getUsersUniqueIdentity());
        }
    }


    public static function getClientIPAddress()
    {
        $IP_address = 'unknown';

        $keys = array('HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        foreach ($keys as $key) 
        {
            if (isset($_SERVER[$key]))
            {
                $ip_list = explode(',', $_SERVER[$key]);
                foreach ($ip_list as $ip) 
                {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP))
                    {
                        $IP_address = $ip;
                        return $IP_address;
                    }
                }
            }
        }
      
        return $IP_address;
    }


    public static function getUserAgentHash()
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    }


    public static function getUsersUniqueIdentity()
    {
        return hash('sha256', self::getUserAgentHash());
    }


    public static function startSession($visitor_identity)
    {
        if (SESSION_NAME == "________")
        {
            if (PRODUCTION_MODE)
            {
                debuglog("Could not start session for security reasons because the session name was default value", "warning");
                return;
            }
            else
            {
                exit("Please update your session key from the default value to a unique value in config.php");
            }
        }
        
        session_name(SESSION_NAME);

        ini_set('session.cookie_lifetime', SESSION_LIFETIME);
        ini_set('session.cookie_path', '/');
        ini_set('session.cookie_domain', DOMAIN);
        ini_set('session.cookie_secure', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', COOKIE_SAMESITE);

        session_start();

        // Check session duration
        self::afkTimeout(SESSION_AFK_TIMEOUT);

        if (empty(self::get("visitor_identity")))
        {
            // Set user identity if not yet set
            self::add("visitor_identity", $visitor_identity);
            session_regenerate_id(true);
        }
        else if (self::get("visitor_identity") !== $visitor_identity)
        {
            // Restart if user's IP address doesn't match the original one
            self::restart();
        }
    }


    public static function end()
    {
        self::removeAll();
        session_destroy();
    }


    public static function restart()
    {
        self::end();
        self::start();
    }


    // Add variables to session
    public static function add($identifier, $value)
    {
        $_SESSION[$identifier] = $value;
        return;
    }


    // Check does session has variable
    public static function has($key)
    {
        return (isset($_SESSION[$key])) ? true : false;
    }


    // Remove variables from session
    public static function remove($key)
    {
        // Remove single variable
        if (!is_array($key))
        {
            unset($_SESSION[$key]);
            return;
        }

        // Remove multiple variables
        foreach ($key as $variable)
        {
            unset($_SESSION[$variable]);
        }
    }


    // Remove every variable from session
    public static function removeAll()
    {
        session_unset();
    }


    // Get variables from session
    public static function get($key, $default_value = null)
    {
        return (isset($_SESSION[$key])) ? $_SESSION[$key] : $default_value;
    }

    // Check if session is already active
    private static function isActive()
    {
        $serverInterface = php_sapi_name();

        if ($serverInterface === "cli") {
            return false;
        }

        return session_status() === PHP_SESSION_ACTIVE;
    }

    // End Session if user isn't active for x seconds (specified in the config.php)
    private static function afkTimeout($duration)
    {
        // Get previous time afk_timeout was set
        $previous_afk_timeout = self::get("afk_timeout");

        // Update the new afk_timeout
        self::add("afk_timeout", time());

        // Nothing to do if there was not check for previous afk_timeout
        if (!isset($previous_afk_timeout))
        {
            return;
        }

        // Check has the user been afk longer than the allowed duration
        if (time() - (int)$previous_afk_timeout > $duration)
        {
            self::restart();
        }
    }
}
