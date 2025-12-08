<?php defined("ACCESS") or exit("Access Denied");

// ------------------------------------------------------------------------------------------------
// Page Helpers
// ------------------------------------------------------------------------------------------------
function page($file)
{
    $file = ensurePHPExtension($file);
    $path = WEBROOT . "/App/Pages/" . $file;

    return file_exists($path) ? $path : false;
}

function pageExists($file)
{
    return file_exists(WEBROOT . "/App/Pages/" . ensurePHPExtension($file));
}


// ------------------------------------------------------------------------------------------------
// Routing and Redirect Helpers
// ------------------------------------------------------------------------------------------------
function route($page = "")
{
    if (ENABLE_LANGUAGES)
    {
        return BASE_URL . getAppLocale() . "/" . $page;
    }

    return BASE_URL . $page;
}

function redirect($target = null)
{
    // Redirect to given page
    if (!empty($target))
    {
        header("Location: " . $target);
        exit;
    }

    // Redirect back to previous page
    if (isset($_SERVER["HTTP_REFERER"]))
    {
        header("Location: " . sanitizeString($_SERVER["HTTP_REFERER"]));
        exit;
    }

    refreshPage();
}

function redirectBack($fallback = null)
{
    // Redirect back to previous page
    if (isset($_SERVER["HTTP_REFERER"]))
    {
        header("Location: " . sanitizeString($_SERVER["HTTP_REFERER"]));
        exit;
    }

    // Redirect to fallback page
    if (!empty($fallback))
    {
        header("Location: " . $fallback);
        exit;
    }

    refreshPage();
}

function refreshPage()
{
    header("Refresh:0");
    exit;
}


// ------------------------------------------------------------------------------------------------
// File Extension Helpers
// ------------------------------------------------------------------------------------------------
function ensurePHPExtension($file)
{
    return substr($file, -4) === ".php" ? $file : $file . ".php";
}

function ensureJSExtension($file)
{
    return substr($file, -3) === ".js" ? $file : $file . ".js";
}

function ensureCSSExtension($file)
{
    return substr($file, -4) === ".css" ? $file : $file . ".css";
}

function ensureSCSSExtension($file)
{
    return substr($file, -5) === ".scss" ? $file : $file . ".scss";
}


// ------------------------------------------------------------------------------------------------
// Sanitizers
// ------------------------------------------------------------------------------------------------
function sanitizeFileName(string $fileName)
{
    $safe = preg_replace("/[^a-zA-Z0-9._-]/", "_", $fileName);
    return substr($safe, 0, 255);
}

function sanitizeString(string $value)
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}
