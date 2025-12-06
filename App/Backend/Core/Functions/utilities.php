<?php defined("ACCESS") or exit("Access Denied");


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

function image($file, array $params = ["path" => "url"]): string
{
    return kristal_getAssetPath("Images", $file, $params);
}

function css($file, array $params = ["path" => "url"]): string
{
    return kristal_getAssetPath("CSS", $file, $params);
}

function js($file, array $params = ["path" => "url"]): string 
{
    return kristal_getAssetPath("Javascript", $file, $params);
}

function download($file, array $params = ["path" => "url"]): string
{
    return kristal_getAssetPath("Downloads", $file, $params);
}

function audio($file, array $params = ["path" => "url"]): string
{
    return kristal_getAssetPath("Audio", $file, $params);
}


// ============================================================================================================== \\

function page($file)
{
    $file = ensurePHPExtension($file);
    if (file_exists("App/Pages/$file"))
    {
        return "App/Pages/$file";
    }

    return false;
}

function pageExists($file)
{
    return file_exists("App/Pages/" . ensurePHPExtension($file));
}

// ============================================================================================================== \\

function route($page = "")
{
    if (ENABLE_LANGUAGES)
    {
        return BASE_URL . getAppLocale() . "/" . $page;
    }

    return BASE_URL . $page;
}

// ============================================================================================================== \\

function redirect($target = null)
{
    // Redirect to given page
    if (isset($target))
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

    // Refresh page
    header("Refresh:0");
    exit;
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
    if (isset($fallback))
    {
        header("Location: " . $fallback);
        exit;
    }

    // Refresh page
    header("Refresh:0");
    exit;
}

function refreshPage()
{
    header("Refresh:0");
    exit;
}

// ============================================================================================================== \\

function ensurePHPExtension($file)
{
    return (substr($file, -4) === ".php") ? $file : $file . ".php";
}

function ensureJSExtension($file)
{
    return (substr($file, -3) === ".js") ? $file : $file . ".js";
}

function ensureCSSExtension($file)
{
    return (substr($file, -4) === ".css") ? $file : $file . ".css";
}

function ensureSCSSExtension($file)
{
    return (substr($file, -5) === ".scss") ? $file : $file . ".scss";
}

// ============================================================================================================== \\

function sanitizeFileName(string $fileName)
{
    $sanitized = preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
    return substr($sanitized, 0, 255);
}

function sanitizeString(string $name)
{
    $sanitized = htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
    return $sanitized;
}

// ============================================================================================================== \\

function isSecurePassword($password)
{
    // Check the length
    if (strlen($password) < 8)
    {
        return false;
    }

    // Check for uppercase letter
    if (!preg_match('/[A-Z]/', $password))
    {
        return false;
    }

    // Check for lowercase letter
    if (!preg_match('/[a-z]/', $password))
    {
        return false;
    }

    // Check for digit
    if (!preg_match('/\d/', $password))
    {
        return false;
    }

    // Check for special character
    if (!preg_match('/[\W_]/', $password))
    {
        return false;
    }

    return true;
}

// ============================================================================================================== \\
