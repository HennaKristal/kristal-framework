<?php defined("ACCESS") or exit("Access Denied");


# --------------------------------------------------------------------------
# Framework configurations
# --------------------------------------------------------------------------

// Optimizes your application for production (disable for development, enable for production)
define("PRODUCTION_MODE", false);


// Base URL of your website (ensure to append a "/" at the end of the URL)
define("BASE_URL", "https://example.com/");





# --------------------------------------------------------------------------
# Maintenance configurations
# --------------------------------------------------------------------------

// Activates a maintenance page for visitors
define("MAINTENANCE_MODE", false);

// Substitute with a robust password (use sha256 for hashing the password)
define("MAINTENANCE_PASSWORD", hash('sha256', "________"));

// Maintenance login attempts are limited to a specified number
define("MAINTENANCE_LOCKOUT_LIMIT", 5);

// Login attempts reset after waiting a defined number of seconds
define("MAINTENANCE_LOCKOUT_CLEAR_TIME", 900);





# --------------------------------------------------------------------------
# Error reporting configurations
# --------------------------------------------------------------------------

// Should debugging features be enabled (disabling will turn off all error debugging features)
define("ENABLE_DEBUG", true);

// Determines whether debug messages are recorded in a log file
define("ENABLE_DEBUG_LOG", true);

// Determines the visibility of debug messages in frontend (can not be displayed in production mode)
define("ENABLE_DEBUG_DISPLAY", true);

// When enabled, PHP notices will not be displayed or logged (can not be displayed in production mode)
define("DEBUG_IGNORE_WARNINGS", false);

// When enabled, PHP deprecated notices will not be displayed or logged (can not be displayed in production mode)
define("DEBUG_IGNORE_NOTICES", false);

// When enabled, PHP warnings will not be displayed or logged (can not be displayed in production mode)
define("DEBUG_IGNORE_DEPRECATED", false);

// When enabled, PHP strict standards notices will not be displayed or logged (can not be displayed in production mode)
define("DEBUG_IGNORE_STRICT", false);

// Defines the file path for logging debug messages (please keep this outside of html root)
define("DEBUG_LOG_PATH", "/debug.log");





# --------------------------------------------------------------------------
# Database configurations
# --------------------------------------------------------------------------

// Specify your database connections here
define("DATABASES", serialize([
    "primary" => (object) [
        "host" => "________",
        "database_name" => "________",
        "username" => "________",
        "password" => "________"
    ],
    "secondary" => (object) [
        "host" => "________",
        "database_name" => "________",
        "username" => "________",
        "password" => "________"
    ],
    "additional" => (object) [
        "host" => "________",
        "database_name" => "________",
        "username" => "________",
        "password" => "________"
    ],
]));





# --------------------------------------------------------------------------
# SMTP Mail configurations
# --------------------------------------------------------------------------

// Host of the email service provider
define("MAILER_HOST", "________");

// Email address for the account
define("MAILER_EMAIL", "________");

// Password for the email account
define("MAILER_PASSWORD", "________");

// Display name for outgoing emails
define("MAILER_NAME", "________");

// Common protocols include ssl. Leave blank if your email service lacks encryption
define("MAILER_PROTOCOL", "ssl");

// Common email service ports include 25, 465, 587, and 2525
define("MAILER_PORT", 25);





# --------------------------------------------------------------------------
# reCAPTCHA
# --------------------------------------------------------------------------

define("RECAPTCHA_V2_SITE_KEY", "xxxxxxxxxxxxxxxxxxxxxxxxxxxx");
define("RECAPTCHA_V2_SITE_SECRET", "xxxxxxxxxxxxxxxxxxxxxxxxxxxx");

define("RECAPTCHA_V3_SITE_KEY", "xxxxxxxxxxxxxxxxxxxxxxxxxxxx");
define("RECAPTCHA_V3_SITE_SECRET", "xxxxxxxxxxxxxxxxxxxxxxxxxxxx");





# --------------------------------------------------------------------------
# Session configurations
# --------------------------------------------------------------------------

// Replace with a securely generated string (30-50 characters recommended)
define("SESSION_NAME", "________");

// Session expires after x seconds
define("SESSION_TIMEOUT", 18000);

// Session expires after x seconds if user doesn't perform any actions
define("SESSION_AFK_TIMEOUT", 1800);

// Regenerate CSRF tokens on each page request for heightened security rather than solely on form submission
define("REGENERATE_CSRF_ON_PAGE_REFRESH", false);





# --------------------------------------------------------------------------
# Cookie configurations
# --------------------------------------------------------------------------

// Replace with a securely generated string (30-50 characters recommended)
define("COOKIE_NAME", "________");

// Cookies are set to expire after a specified number of seconds
define("COOKIE_EXPIRE_TIME", 86400);





# --------------------------------------------------------------------------
# Time configurations
# --------------------------------------------------------------------------

// List of all available timezones: https://www.php.net/manual/en/timezones.php
define("TIMEZONE", "UTC");

// Specify the format for displaying dates (for example: "j.n.Y" for 31.1.2020, "m/d/Y" for 01/31/2020, "F j, Y" for January 31, 2020)
define("DATE_FORMAT", "j.n.Y");

// Define the format for displaying times (for example: "H:i:s" for 18:50:04, "g:i a" for 06:50 pm, "g:i:s a" for 06:50:04 pm)
define("TIME_FORMAT", "H:i:s");





# --------------------------------------------------------------------------
# Language configurations
# --------------------------------------------------------------------------

// Enable native support for multilingual URLs, appending the language to the URL (for example: 'example.com/en')
define("ENABLE_LANGUAGES", true);

// Set the default language for your site (this will also be the main language of your site)
define("DEFAULT_LANGUAGE", "en");

// List of all available languages
define("AVAILABLE_LANGUAGES", serialize([
    "en",
    "fi",
    "sv",
]));





# --------------------------------------------------------------------------
# SEO configurations
# --------------------------------------------------------------------------

// Automatically generate sitemaps from your routes (excluding private and protected functions)
define("AUTO_COMPILE_SITEMAP", false);





# --------------------------------------------------------------------------
# HTML configurations
# --------------------------------------------------------------------------

// Condense HTML into a single line when activated
define("MINIFY_HTML", false);





# --------------------------------------------------------------------------
# SCSS configurations (does not work when in production mode)
# --------------------------------------------------------------------------

// Automatically recompile SCSS files upon modifications and page reload
define("AUTO_COMPILE_SCSS", true);

// Define the SCSS compilation mode, choose between 'expanded' or 'compressed'
define("COMPILED_CSS_TYPE", "compressed");

// Set a default theme if Session::get('theme') is undefined. If you don't use any themes then assign a default name for the compilation target of your SCSS files
define("DEFAULT_THEME", "dark");

// Include a comment indicating the last compilation time of the CSS file
define("PRINT_COMPILE_DATE_CSS", true);





# --------------------------------------------------------------------------
# JavaScript configurations (does not work when in production mode)
# --------------------------------------------------------------------------

// Automatically recompile JavaScript files upon modifications and page reload
define("AUTO_COMPILE_JS", true);

// Include a comment indicating the last compilation time of the JavaScript file
define("PRINT_COMPILE_DATE_JS", true);

// Tell the framework how do you want your js files to be bundled and minified
define("JS_BUNDLES", serialize([
    "core.js" => [
        "Core/form.js",
        "Core/tooltips.js",
        "Core/translator.js",
    ],
    "maintenance.js" => [
        "Scripts/maintenance.js",
    ],
    "main.js" => [
        "Scripts/main.js",
    ],
]));





# --------------------------------------------------------------------------
# Page metadata configurations
# --------------------------------------------------------------------------
// This tell the header file how to set the page's metadata
define("METADATA", serialize([

    // Metadata for home page
    "home" => (object) [
        "type" => "website",                    // Define the website type for search engines
        "author" => "________",                 // Assign the author name for search engine recognition
        "publisher" => "________",              // Designate the publisher name for search engine recognition
        "url" => BASE_URL,                      // Declare the canonical URL of your website
        "title" => "________",                  // Set the page title for display in browser tabs
        "og:title" => "________",               // Define the page title for visibility in social media search engines
        "description" => "________",            // Describe the page's content for search engine optimization
        "og:description" => "________",         // Provide a description to assist social media search engines in identifying page content
        "keywords" => "Key, Words, Here",       // List keywords to aid search engines in categorizing the page content
        "robots" => "all",                      // Utilize 'none' to block search engine access or 'all' to grant full access
    ],

    // Metadata from '/demo' page
    "demo" => (object) [
        "type" => "website",
        "author" => "________",
        "publisher" => "________",
        "url" => BASE_URL,
        "title" => "________",
        "og:title" => "________",
        "description" => "________",
        "og:description" => "________",
        "keywords" => "Key, Words, Here",
        "robots" => "all",
    ],

    // Metadata for pages without predefined specifications
    "*" => (object) [
        "type" => "website",
        "author" => "________",
        "publisher" => "________",
        "url" => BASE_URL,
        "title" => "________",
        "og:title" => "________",
        "description" => "________",
        "og:description" => "________",
        "keywords" => "Key, Words, Here",
        "robots" => "all",
    ],

]));





# --------------------------------------------------------------------------
# Custom configurations
# --------------------------------------------------------------------------

// You can also define custom constants for use throughout the application
define("CUSTOM_NAME", "CUSTOM_VALUE");
