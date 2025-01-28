<?php namespace Backend\Core;
defined("ACCESS") or exit("Access Denied");

use ScssPhp\ScssPhp\Compiler;


final class SCSS_Compiler
{
    private static $glob_themes_folder = "App/Public/CSS/Themes/*.scss";
    private static $glob_compiled_css_folder = "App/Public/CSS/*.css";
    private static $compiled_css_folder_path = "App/Public/CSS/";
    private static $scss_folder_path = "App/Public/CSS/SCSS/";


    public static function initialize()
    {
        $compiler = new Compiler();
    
        // Select formatter
        $compiler->setOutputStyle(strtolower(COMPILED_CSS_TYPE) === "expanded" ? "expanded" : "compressed");

        // Check should we compile
        if (self::shouldCompile())
        {
            self::compile($compiler);
        }
    }


    private static function shouldCompile()
    {
        $latest_compiled_css_time = 0;

        // Determine the most recently modified time of compiled CSS files
        foreach (glob(self::$glob_compiled_css_folder) as $file)
        {
            $file_time = filemtime($file);

            if ($file_time > $latest_compiled_css_time)
            {
                $latest_compiled_css_time = $file_time;
            }
        }

        // Check if any theme file is newer than the latest compiled CSS
        foreach (glob(self::$glob_themes_folder) as $theme)
        {
            if (filemtime($theme) > $latest_compiled_css_time)
            {
                return true;
            }
        }

        // Check against the default theme
        $defaultThemeTime = file_exists(self::$compiled_css_folder_path . ensureCSSExtension(DEFAULT_THEME)) ? filemtime(self::$compiled_css_folder_path . ensureCSSExtension(DEFAULT_THEME)) : 0;
        if ($defaultThemeTime > $latest_compiled_css_time)
        {
            return true;
        }

        // Check recursively if any SCSS file is newer than the latest compiled CSS
        $directory = new \RecursiveDirectoryIterator(self::$scss_folder_path);
        $iterator = new \RecursiveIteratorIterator($directory);
        foreach ($iterator as $file)
        {
            if ($file->isFile() && strtolower($file->getExtension()) === 'scss')
            {
                if (filemtime($file->getRealPath()) > $latest_compiled_css_time)
                {
                    return true;
                }
            }
        }
    
        return false;
    }


    private static function compile($compiler)
    {
        // Determine if there are any themes
        $themes = glob(self::$glob_themes_folder);

        // Compile without theme
        if (empty($themes))
        {
            self::compileOutput($compiler, DEFAULT_THEME);
            return;
        }

        // Compile with each theme
        foreach ($themes as $theme)
        {
            $theme_name = strtolower(str_replace(".scss", "", basename($theme)));
            self::compileOutput($compiler, $theme_name, $theme);
        }
    }


    private static function compileOutput($compiler, $output_name, $theme_file = null)
    {
        $scss = "";
    
        // Add theme variables if a theme file is provided
        if (isset($theme_file))
        {
            $scss .= file_get_contents($theme_file);
        }
    
        // Iterate through each file in the directory and its subdirectories
        $directory = new \RecursiveDirectoryIterator(self::$scss_folder_path);
        $iterator = new \RecursiveIteratorIterator($directory);
        foreach ($iterator as $file)
        {
            if ($file->isFile() && strtolower($file->getExtension()) === 'scss')
            {
                $scss .= file_get_contents($file->getRealPath());
            }
        }
    
        // Compile sass files
        $compiled_css = $compiler->compileString($scss)->getCSS();
    
        // Add comment when file was last modified
        if (PRINT_COMPILE_DATE_CSS)
        {
            $compiled_css .= "\n\n/* Generated at: " . date(DATE_FORMAT . " " . TIME_FORMAT) . " */";
        }
        
        $compiled_css .= "\n";
    
        // Create css files from compiled sass
        file_put_contents(self::$compiled_css_folder_path . ensureCSSExtension($output_name), $compiled_css);
    }
}
