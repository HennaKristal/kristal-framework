<?php namespace Backend\Core;
defined("ACCESS") or exit("Access Denied");

class Block
{
    protected static $shortcodes = [];

    public static function initialize()
    {
        // Get all directories within the blocks folder
        $directories = glob(WEBSITE_ROOT . '/App/Backend/Blocks/*', GLOB_ONLYDIR);

        foreach ($directories as $dir)
        {
            // The shortcode name is the directory name
            $shortcode = basename($dir);
            
            // The file we're looking for is index.php inside the directory
            $file = $dir . '/index.php';
            
            // Check if the index.php file exists before registering it
            if (file_exists($file))
            {
                self::$shortcodes[$shortcode] = $file;
            }
        }
    }

    public static function render($shortcode, $atts = [])
    {
        if (!array_key_exists($shortcode, self::$shortcodes)) { return ''; }

        // Render the specified shortcode with the given attributes.
        ob_start();
        extract($atts);
        include self::$shortcodes[$shortcode];
        echo ob_get_clean();
    }
}
