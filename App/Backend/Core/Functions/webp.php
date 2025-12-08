<?php

function webp($file, $compression = "default")
{
    // Normalize file input
    if (strpos($file, "/") === 0)
    {
        $file = substr($file, 1);
    }

    // Validate compression
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

    $imageFolder = WEBROOT . "/App/Public/Images/";
    $webpFolder = WEBROOT . "/Cache/WebP/";

    if (!is_dir($webpFolder))
    {
        mkdir($webpFolder, 0755, true);
    }

    // Resolve full file path
    $filePath = $imageFolder . $file;

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

    // Handle native webp
    if ($imageType === IMAGETYPE_WEBP)
    {
        $outputPath = $webpFolder . $cleanName;

        if (!file_exists($outputPath))
        {
            copy($filePath, $outputPath);
        }

        $fileName = basename($outputPath);
        return rtrim(BASE_URL, "/") . "/Cache/WebP/" . $fileName;
    }

    // Build output name for non-webp
    $cleanName = preg_replace("/\.[a-zA-Z0-9]+$/", "", $cleanName);
    $outputPath = $webpFolder . $cleanName . "-" . $compression . ".webp";

    if (file_exists($outputPath))
    {
        $fileName = basename($outputPath);
        return rtrim(BASE_URL, "/") . "/Cache/WebP/" . $fileName;
    }

    // Load image
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
