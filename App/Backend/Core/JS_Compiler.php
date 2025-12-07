<?php namespace Backend\Core;
defined("ACCESS") or exit("Access Denied");

use Patchwork\JSqueeze;


final class JS_Compiler
{
    public static function initialize()
    {
        $folder_path = WEBSITE_ROOT . "/App/Public/Javascript/";
        $compiled_folder_path = WEBSITE_ROOT . "/App/Public/Javascript/";
        $compiler = new JSqueeze();
        $js_bundles = unserialize(JS_BUNDLES);


        foreach ($js_bundles as $container => $files)
        {
            $compiled_file_path = $compiled_folder_path . ensureJSExtension($container);
            $compiled_file_date = file_exists($compiled_file_path) ? filemtime($compiled_file_path) : 0;

            $should_compile = false;

            foreach ($files as $file)
            {
                $file_path = $folder_path . ensureJSExtension($file);
                 
                // Check is any js file updated since last JS compile
                if (file_exists($file_path) && filemtime($file_path) > $compiled_file_date)
                {
                    $should_compile = true;
                    break;
                }
            }

            if ($should_compile)
            {
                $compiled_js = "";

                foreach ($files as $file)
                {
                    $file_path = $folder_path . ensureJSExtension($file);
                    $compiled_js .= $compiler->squeeze(file_get_contents($file_path), true, true, false);
                }

                // Add date to generated JavaScript
                if (PRINT_COMPILE_DATE_JS)
                {
                    $compiled_js .= "\n\n/* Generated at: " . date(DATE_FORMAT . " " . TIME_FORMAT) . " */";
                }

                $compiled_js .= "\n";
                
                file_put_contents($compiled_file_path, $compiled_js);
            }
        }
    }
}
