<?php declare(strict_types=1); 
namespace Backend\Controllers;
defined("ACCESS") or exit("Access Denied");

use Backend\Core\Session;

class ThemeController
{
    public function changeTheme(string $theme): string
    {
        $theme = sanitize_file(trim($theme)) . '.css';

        // Check if the theme file exists
        if (!file_exists(PATH_CSS . $theme))
        {
            return translate("Tried to activate theme called %s, but theme didn't exist.", [$theme]);
        }
        
        // Add the new theme to session
        Session::add("theme", $theme);

        return translate("Successfully changed theme to %s.", [$theme]);
    }
}
