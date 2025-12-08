<?php

function image($file) { return kristal_getAssetURL("Images", $file); }
function css($file) { return kristal_getAssetURL("CSS", $file); }
function js($file) { return kristal_getAssetURL("Javascript", $file); }
function download($file) { return kristal_getAssetURL("Downloads", $file); }
function audio($file) { return kristal_getAssetURL("Audio", $file); }

function kristal_getAssetURL($folder, $file)
{
    // Remove leading slash if present
    if (strpos($file, "/") === 0)
    {
        $file = substr($file, 1);
    }

    $searchFolder = "App/Public/" . $folder . "/";
    $filePath = WEBROOT . "/" . $searchFolder . $file;

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



function imagePath($file) { return kristal_getAssetPath("Images", $file); }
function cssPath($file) { return kristal_getAssetPath("CSS", $file); }
function jsPath($file) { return kristal_getAssetPath("Javascript", $file); }
function downloadPath($file) { return kristal_getAssetPath("Downloads", $file); }
function audioPath($file) { return kristal_getAssetPath("Audio", $file); }

function kristal_getAssetPath($folder, $file)
{
    $filePath = WEBROOT . "/App/Public/" . $folder . "/" . $file;

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
