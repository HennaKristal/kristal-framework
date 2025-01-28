<?php namespace Backend\Controllers;
defined("ACCESS") or exit("Access Denied");

class LanguageController
{
    public function changeLanguage($language = null)
    {
        if (in_array($language, unserialize(AVAILABLE_LANGUAGES)) && $language != getAppLocale())
        {
            setAppLocale($language);
            redirect(route(""));
        }

        refreshPage();
    }
}
