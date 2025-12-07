<?php namespace Backend\Controllers;
defined("ACCESS") or exit("Access Denied");

use Backend\Core\Session;


class ThemeController
{
    private $themes_folder_path = WEBSITE_ROOT . "/App/Public/CSS/";


    public function changeTheme($theme = null)
    {
        // Make sure theme is not null
        if ($theme === null)
        {
            return translate("Aborted action to change theme, because no name was specified.");
        }
    
        // Format theme name: remove the file extension, convert to lowercase and add .css extension
        $formatted_theme_name = strtolower(pathinfo($theme, PATHINFO_FILENAME)) . '.css';

    
        // Check if the theme file exists
        if (!file_exists($this->themes_folder_path . $formatted_theme_name))
        {
            return translate("Tried to activate theme called %s, but theme didn't exist.", [$theme]);
        }
        
        // Add the new theme to session
        Session::add("theme", $formatted_theme_name);
        return translate("Successfully changed theme to %s.", [$theme]);
    }
}
