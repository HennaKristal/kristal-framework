<?php

function webp($file, $compression = "default")
{
    // Normalize input before passing to mode handlers
    if (strpos($file, "/") === 0)
    {
        $file = substr($file, 1);
    }

    if (!is_numeric($compression))
    {
        $compression = WEBP_DEFAULT_QUALITY;
    }
    else
    {
        $compression = intval($compression);

        if ($compression < 0)
        {
            $compression = 0;
        }

        if ($compression > 100)
        {
            $compression = 100;
        }
    }

    if (PRODUCTION_MODE === true)
    {
        return webpProduction($file, $compression);
    }

    return webpDevelopment($file, $compression);
}


function webpProduction($file, $compression)
{
    // Build the expected filename directly
    $cleanName = preg_replace("/[^a-zA-Z0-9\/\.\-_]/", "", $file);
    $cleanName = str_replace("/", "-", $cleanName);
    $cleanName = preg_replace("/\.[a-zA-Z0-9]+$/", "", $cleanName);
    $cleanName = $cleanName . "-" . $compression . ".webp";

    return rtrim(BASE_URL, "/") . "/Cache/WebP/" . $cleanName;
}


function webpDevelopment($file, $compression)
{
    // Resolve folders
    $imageFolder = WEBROOT . "/App/Public/Images/";
    $webpFolder = WEBROOT . "/Cache/WebP/";

    // Ensure output folder exists
    if (!is_dir($webpFolder))
    {
        mkdir($webpFolder, 0755, true);
    }

    // Resolve full path
    $filePath = $imageFolder . $file;

    // Attempt alternative extensions
    if (!file_exists($filePath))
    {
        $matches = glob($filePath . ".*");

        if (empty($matches))
        {
            return "";
        }

        $filePath = $matches[0];
    }

    // Detect type
    $imageType = exif_imagetype($filePath);

    // Resolve relative name
    $searchFolder = "/App/Public/Images/";
    $position = strpos($filePath, $searchFolder);

    if ($position === false)
    {
        return "";
    }

    $relativeName = substr($filePath, $position + strlen($searchFolder));
    $cleanName = str_replace("/", "-", $relativeName);

    // Native webp
    if ($imageType === IMAGETYPE_WEBP)
    {
        // Remove extension from cleanName
        $cleanName = preg_replace("/\.[a-zA-Z0-9]+$/", "", $cleanName);

        // Output should match naming rules: name-quality.webp
        $outputPath = $webpFolder . $cleanName . "-" . $compression . ".webp";

        // Use cached version if available
        if (!file_exists($outputPath))
        {
            copy($filePath, $outputPath);
        }

        $fileName = basename($outputPath);
        return rtrim(BASE_URL, "/") . "/Cache/WebP/" . $fileName;
    }

    // Build output
    $cleanName = preg_replace("/\.[a-zA-Z0-9]+$/", "", $cleanName);
    $outputPath = $webpFolder . $cleanName . "-" . $compression . ".webp";

    // Cached version
    if (file_exists($outputPath))
    {
        $fileName = basename($outputPath);
        return rtrim(BASE_URL, "/") . "/Cache/WebP/" . $fileName;
    }

    // Load source
    if ($imageType === IMAGETYPE_PNG)
    {
        $image = imagecreatefrompng($filePath);
    }
    else if ($imageType === IMAGETYPE_JPEG)
    {
        $image = imagecreatefromjpeg($filePath);
    }
    else
    {
        return "";
    }

    // Generate
    imagewebp($image, $outputPath, $compression);
    imagedestroy($image);

    $fileName = basename($outputPath);
    return rtrim(BASE_URL, "/") . "/Cache/WebP/" . $fileName;
}
