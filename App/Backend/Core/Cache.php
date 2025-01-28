<?php namespace Backend\Core;
defined("ACCESS") or exit("Access Denied");


class Cache
{
    private static $cache_path = "App/Cache/Cache/";


    public static function add($name, $value, $duration = 24)
    {
        // Ensure cache directory exists
        if (!file_exists(self::$cache_path)) {
            mkdir(self::$cache_path, 0755, true);
        }

        // Create path, content and duration for the cache
        $file = self::$cache_path . sanitizeFileName($name) . ".php";
        $value = var_export($value, true);
        $date = strtotime(date('Y-m-d H:i:s'));
        $duration = $duration * 3600;

        // Create file for cached content
        $cache = '<?php' . "\n\nif ($date - strtotime(date('Y-m-d H:i:s')) + $duration < 0) { return null; }\n\nreturn $value;\n" . '?>';

        // Write file
        return file_put_contents($file, $cache);
    }


    public static function get($name)
    {
        // Create file path
        $file = self::$cache_path . sanitizeFileName($name) . ".php";

        // Check if file exists
        if (file_exists($file))
        {
            // Get content
            $value = include $file;

            // Remove cache if cache file returned null (expired)
            if ($value === null)
            {
                self::remove($name);
            }
        }

        return $value;
    }


    public static function remove($name)
    {
        // Create file path
        $file = self::$cache_path . sanitizeFileName($name) . ".php";

        // Check if file exists
        if (file_exists($file))
        {
            // Delete file
            unlink($file);
            return true;
        }

        return false;
    }
}
