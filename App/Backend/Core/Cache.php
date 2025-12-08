<?php namespace Backend\Core;
defined("ACCESS") or exit("Access Denied");

/* ===============================================
Usage:
Cache::add("cache_name", $value, "1 day");
Cache::get("cache_name");
Cache::remove("cache_name");
Cache::clear();
=============================================== */

class Cache
{
    private static $cachePath = WEBROOT . "/Cache/";

    // Add value to cache --------------------------------------------------------
    public static function add($name, $value, $duration = "24 hours")
    {
        if (!file_exists(self::$cachePath))
        {
            mkdir(self::$cachePath, 0755, true);
        }

        $fileName = sanitizeFileName($name) . ".json";
        $filePath = self::$cachePath . $fileName;
        $expires = strtotime("now + " . $duration);

        if ($expires === false)
        {
            debuglog("Invalid cache duration for {$name}. Duration given: {$duration}", "warning");
            $expires = strtotime("now + 1 day");
        }

        $content = [
            "expires" => $expires,
            "data" => serialize($value)
        ];

        return file_put_contents($filePath, json_encode($content, JSON_PRETTY_PRINT));
    }

    // Get value from cache ------------------------------------------------------
    public static function get($name)
    {
        $fileName = sanitizeFileName($name) . ".json";
        $filePath = self::$cachePath . $fileName;

        if (!file_exists($filePath))
        {
            return null;
        }

        $content = json_decode(file_get_contents($filePath), true);

        if (!is_array($content) || !isset($content["expires"]) || !isset($content["data"]))
        {
            debuglog("Cache file is invalid or corrupted for {$name}. Removing file.", "warning");
            self::remove($name);
            return null;
        }

        if (time() > (int)$content["expires"])
        {
            self::remove($name);
            return null;
        }

        return unserialize($content["data"]);
    }

    public static function remove($name)
    {
        $fileName = sanitizeFileName($name) . ".json";
        $filePath = self::$cachePath . $fileName;

        if (file_exists($filePath))
        {
            unlink($filePath);
            return true;
        }

        return false;
    }

    public static function clear()
    {
        if (!file_exists(self::$cachePath))
            return;

        // Delete all json files
        foreach (glob(self::$cachePath . "*.json") as $file)
        {
            unlink($file);
        }
    }
}
