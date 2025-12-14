<?php defined("ACCESS") or exit("Access Denied");

// ------------------------------------------------------------------------------------------------
// Page Helpers
// ------------------------------------------------------------------------------------------------
function page($file)
{
    $file = ensurePHPExtension($file);
    $realPath = realpath(PATH_TEMPLATES . $file);

    if ($realPath === false)
        return false;

    if (strpos($realPath, PATH_TEMPLATES) !== 0)
        return false;

    return $realPath;
}

function pageExists($file)
{
    return page($file) !== false;
}


// ------------------------------------------------------------------------------------------------
// Routing and Redirect Helpers
// ------------------------------------------------------------------------------------------------
function route($page = "")
{
    if (ENABLE_LANGUAGES)
    {
        return URL_BASE . getAppLocale() . "/" . $page;
    }

    return URL_BASE . $page;
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
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }

    refreshPage();
}

function redirectBack($fallback = null)
{
    // Redirect back to previous page
    if (isset($_SERVER["HTTP_REFERER"]))
    {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
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
