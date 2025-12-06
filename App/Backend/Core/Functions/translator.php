<?php defined("ACCESS") or exit("Access Denied");

// Set language for translator
function setAppLocale($language)
{
    if (in_array($language, unserialize(AVAILABLE_LANGUAGES), true) && $language != getAppLocale())
    {
        Session::add("language", $language);
    }
}

// Get translator's language
function getAppLocale()
{
    return Session::has("language") ? Session::get("language") : DEFAULT_LANGUAGE;
}

// Output translation
function ts($key, $variables = [])
{
    return translate($key, $variables);
}

// Return translation
function translate($key, $variables = [])
{
    // Get translations
    global $translations;

    if (!isset($translations))
    {
        $path = WEBSITE_ROOT . '/App/Public/Translations/translations.php';
    
        if (!file_exists($path))
        {
            throw new Exception("Missing translation file at App/Public/Translations/translations.php");
        }

        $translations = include $path;
    }
    
    // Return original string if no translation was found
    if (!array_key_exists($key, $translations))
    {
        return $key;
    }

    // Make sure $variables is an array
    if (!is_array($variables))
    {
        $variables = [$variables];
    }

    // Get translation language
    $language = getAppLocale();

    // Get valid languages
    foreach ($translations[$key] as $lang => $value)
    {
        $valid_languages[$lang] = $lang;
    }

    // Check if given language is found from translation
    if (isset($loadedTranslations[$key][$language]))
        {
            return vsprintf($loadedTranslations[$key][$language], $variables);
        }
    
        return vsprintf($key, $variables);
}
