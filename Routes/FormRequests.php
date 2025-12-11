<?php declare(strict_types=1); 
namespace Backend\Core;
defined("ACCESS") or exit("Access Denied");

use Backend\Controllers\ThemeController;
use Backend\Controllers\LanguageController;

class FormRequests extends FormRequest
{
    public function __construct()
    {
        // Protected function can only be called when parent::__construct() is called with ["allow_protected_calls" => true] parameter
        // You can specify your own condition in the IF statement if you want to access protected functions from form requests
        // For example Session::get("logged_in") === true or Session::get("role") === "admin"
        $allowProtected = false;

        parent::__construct([
            "allow_protected_calls" => $allowProtected
        ]);
    }

    // Form Request for changing theme
    public function change_theme(array $request): void
    {
        // $request variable contains all data sent by the form
        $themeController = new ThemeController();
        $themeController->changeTheme($request["theme-name"]);
    }

    // Form Request for changing language
    public function change_language(array $request): void
    {
        $languageController = new LanguageController();
        $languageController->changeLanguage($request["language"]);
    }

    // Protected functions can only be called when the parent class is constructed with 'true' parameter or internally from other functions
    protected function xxxxxxxx(array $request): void
    {
        // ...
    }

    // Private functions can only be called internally from other functions within this class
    private function xxxxxxx(array $request): void
    {
        // ...
    }
}
