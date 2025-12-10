<?php

// Grant access to php files
define("ACCESS", "Granted");

// Load configurations
require_once WEBROOT . "/Config/config.php";
require_once WEBROOT . "/App/Backend/Core/Functions/config.php";

// Load composer autoload.php
if (!file_exists(WEBROOT . "/vendor/autoload.php"))
{
    if (PRODUCTION_MODE)
    {
        exit("Fatal error.");
    }
    else
    {
        exit("Composer autoload file is missing. Run 'composer install --prefer-dist --optimize-autoloader'. If this does not fix the issue, run 'composer dump-autoload --optimize' to regenerate the autoload.php file.");
    }
}

// Load core files
require_once WEBROOT . "/vendor/autoload.php";
require_once WEBROOT . "/App/Backend/Core/Debug/errors.php";
require_once WEBROOT . "/App/Backend/Core/Debug/debug.php";
require_once WEBROOT . "/App/Backend/Core/Functions/assetHelper.php";
require_once WEBROOT . "/App/Backend/Core/Functions/webp.php";
require_once WEBROOT . "/App/Backend/Core/Functions/utilities.php";
require_once WEBROOT . "/App/Backend/Core/Functions/cookies.php";
require_once WEBROOT . "/App/Backend/Core/Functions/translator.php";
require_once WEBROOT . "/App/Backend/Cron/cron.php";

// Initialize session
class_alias("Backend\Core\Session", "Session");
Session::initialize();

// Include cross-site request forgery protection
class_alias("Backend\Core\CSRF", "CSRF");

// Initialize Blocks
class_alias("Backend\Core\Block", "Block");
Block::initialize();

// Compile SCSS and JavaScript
if (!PRODUCTION_MODE)
{
    // Compile SCSS
    if (AUTO_COMPILE_SCSS)
    {
        Backend\Core\SCSS_Compiler::initialize();
    }

    // Compile JavaScript
    if (AUTO_COMPILE_JS)
    {
        Backend\Core\JS_Compiler::initialize();
    }
}

// Load routes
require_once WEBROOT . "/Routes/routes.php";
