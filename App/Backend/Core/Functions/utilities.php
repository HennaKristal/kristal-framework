<?php defined("ACCESS") or exit("Access Denied");

// ------------------------------------------------------------------------------------------------
// Asset Helpers
// ------------------------------------------------------------------------------------------------

function kristal_getAssetPath($folder, $file, array $params = ["path" => "url"])
{
    $filePath = "App/Public/" . $folder . "/" . $file;

    // Handle glob matching
    if (!file_exists($filePath))
    {
        $files = glob($filePath . ".*");

        if (!empty($files))
        {
            $filePath = $files[0];
        }
        else
        {
            return "";
        }
    }

    // Determine the return path type
    $returnPath = strtolower($params["path"]) === "url" ? BASE_URL . $filePath : $filePath;

    // Append ?ver= with last modified date for CSS and JavaScript if 'path' is 'url'
    if (strtolower($params["path"]) === "url" && in_array($folder, ["CSS", "Javascript"]))
    {
        $lastModified = filemtime($filePath);
        $returnPath .= "?ver=" . $lastModified;
    }

    return $returnPath;
}

function image($file, array $parameters = ["path" => "url"])
{
    return kristal_getAssetPath("Images", $file, $parameters);
}

function css($file, array $parameters = ["path" => "url"])
{
    return kristal_getAssetPath("CSS", $file, $parameters);
}

function js($file, array $parameters = ["path" => "url"])
{
    return kristal_getAssetPath("Javascript", $file, $parameters);
}

function download($file, array $parameters = ["path" => "url"])
{
    return kristal_getAssetPath("Downloads", $file, $parameters);
}

function audio($file, array $parameters = ["path" => "url"])
{
    return kristal_getAssetPath("Audio", $file, $parameters);
}

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

// ------------------------------------------------------------------------------------------------
// Password Validation
// ------------------------------------------------------------------------------------------------

function isSecurePassword($password)
{
    if (strlen($password) < 12) { return false; }
    if (!preg_match("/[A-Z]/", $password)) { return false; }
    if (!preg_match("/[a-z]/", $password)) { return false; }
    if (!preg_match("/\d/", $password)) { return false; }
    if (!preg_match("/[\W_]/", $password)) { return false; }

    return true;
}
