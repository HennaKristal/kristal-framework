<?php namespace Backend\Controllers;
defined("ACCESS") or exit("Access Denied");

use Backend\Core\Session;

class ThemeController
{
    public function changeTheme($theme)
    {
        $theme = sanitizeFileName(trim($theme)) . '.css';

        // Check if the theme file exists
        if (!file_exists(WEBROOT . "/App/Public/CSS/" . $theme))
        {
            return translate("Tried to activate theme called %s, but theme didn't exist.", [$theme]);
        }
        
        // Add the new theme to session
        Session::add("theme", $theme);

        return translate("Successfully changed theme to %s.", [$theme]);
    }
}
