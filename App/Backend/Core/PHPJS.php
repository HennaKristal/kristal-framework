<?php namespace Backend\Core;
defined("ACCESS") or exit("Access Denied");


class PHPJS
{
    private static $js_variables = [];
    private static $js_scripts = [];


    public static function addJSVariable($variable, $value = "")
    {
        if (is_array($variable))
        {
            foreach ($variable as $key => $val)
            {
                self::$js_variables[$key] = $val;
            }
        }
        else
        {
            self::$js_variables[$variable] = $value;
        }
    }


    public static function addScript($script)
    {
        self::$js_scripts[] = $script;
    }


    public static function release()
    {
        // Automatically include the BASE_URL constant
        self::$js_variables['baseURL'] = BASE_URL;
        self::$js_variables['production_mode'] = (PRODUCTION_MODE ? "true" : "false");
        self::$js_variables['language'] = getAppLocale();

        // Create div for javascript variables
        echo '<div id="javascript-variables" style="display: none"></div>';

        // Add variables and scripts
        echo '<script>';

            echo 'function getVariable(key) { return $("#javascript-variables").attr(key); }';

            echo 'window.addEventListener("DOMContentLoaded", function() {';

                // Output the JS variables
                foreach (self::$js_variables as $key => $val)
                {
                    echo "document.getElementById('javascript-variables').setAttribute('{$key}', '{$val}');";
                }

                // Output the JS scripts
                foreach (self::$js_scripts as $script)
                {
                    echo $script;
                }

            echo '});';
        echo '</script>';
    }
}
