<?php defined("ACCESS") or exit("Access Denied");

// ------------------------------------------------------------------------------------------------
// Asset Helpers
// ------------------------------------------------------------------------------------------------
function kristal_getAssetURL($folder, $file)
{
    // Remove leading slash if present
    if (strpos($file, "/") === 0)
    {
        $file = substr($file, 1);
    }

    $searchFolder = "App/Public/" . $folder . "/";
    $filePath = WEBSITE_ROOT . "/" . $searchFolder . $file;

    // If file doesn't exist try to find same file with any extension
    if (!file_exists($filePath))
    {
        $matches = glob($filePath . ".*");

        if (empty($matches))
            return "";

        $filePath = $matches[0];
    }

    // Extract filename from file path
    $position = strpos($filePath, $searchFolder);
    if ($position === false)
        return "";

    // Extract filename from file path
    $fileName = substr($filePath, $position);

    // URL version for css and javascript
    if ($folder === "CSS" || $folder === "Javascript")
    {
        $fileName .= "?ver=" . filemtime($filePath);
    }

    return rtrim(BASE_URL, "/") . "/" . $fileName;
}

function kristal_getAssetPath($folder, $file)
{
    $filePath = WEBSITE_ROOT . "/App/Public/" . $folder . "/" . $file;

    // Return path as is if file exists
    if (file_exists($filePath))
    {
        return $filePath;
    }

    // Get all files that match the file name
    $matches = glob($filePath . ".*");

    // Return null if no file was found
    if (empty($matches))
    {
        return null;
    }

    // Return 1st file that matches to mimic getAssetURL logic
    return $matches[0];
}

function image($file) { return kristal_getAssetURL("Images", $file); }
function css($file) { return kristal_getAssetURL("CSS", $file); }
function js($file) { return kristal_getAssetURL("Javascript", $file); }
function download($file) { return kristal_getAssetURL("Downloads", $file); }
function audio($file) { return kristal_getAssetURL("Audio", $file); }

function imagePath($file) { return kristal_getAssetPath("Images", $file); }
function cssPath($file) { return kristal_getAssetPath("CSS", $file); }
function jsPath($file) { return kristal_getAssetPath("Javascript", $file); }
function downloadPath($file) { return kristal_getAssetPath("Downloads", $file); }
function audioPath($file) { return kristal_getAssetPath("Audio", $file); }


// ------------------------------------------------------------------------------------------------
// Page Helpers
// ------------------------------------------------------------------------------------------------
function page($file)
{
    $file = ensurePHPExtension($file);
    $path = WEBSITE_ROOT . "/App/Pages/" . $file;

    return file_exists($path) ? $path : false;
}

function pageExists($file)
{
    return file_exists(WEBSITE_ROOT . "/App/Pages/" . ensurePHPExtension($file));
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
